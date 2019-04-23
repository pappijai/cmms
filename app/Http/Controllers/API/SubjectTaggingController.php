<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\SubjectTagging;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Pdf;

class SubjectTaggingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $year_today = date('Y');
        $month_today = date('m');

        $sections = DB::select("SELECT md5(concat(a.SectionID)) SectionID, a.SectionName,a.SectionYear,a.CourseID,a.SectionStatus,b.CourseDescription,b.CourseYears 
                        FROM sections a INNER JOIN courses b ON a.CourseID = b.CourseID ORDER BY a.SectionYear DESC");



        foreach($sections as $row){
            if($row->CourseYears <= $year_today - $row->SectionYear  && $month_today > 5){
                DB::update('
                UPDATE sections SET
                    SectionStatus = "Inactive"
                    WHERE md5(concat(SectionID)) = "'.$row->SectionID.'"
                ');
            }
            elseif ($row->CourseYears < $year_today - $row->SectionYear) {
                DB::update('
                UPDATE sections SET
                    SectionStatus = "Inactive"
                    WHERE md5(concat(SectionID)) = "'.$row->SectionID.'"         
                ');             
            }
            elseif ($year_today - $row->SectionYear == 0){
                DB::update('
                UPDATE sections SET
                    SectionStatus = "Inactive"
                    WHERE md5(concat(SectionID)) = "'.$row->SectionID.'"         
                ');  
            }
            else{
                DB::update('
                UPDATE sections SET
                    SectionStatus = "Active"
                    WHERE md5(concat(SectionID)) = "'.$row->SectionID.'"                    
                ');
            }
        }



        return DB::select("SELECT md5(concat(a.SectionID)) SectionID, a.SectionName,a.SectionYear,a.CourseID,b.CourseCode,b.CourseDescription,b.CourseYears 
                            FROM sections a INNER JOIN courses b ON a.CourseID = b.CourseID WHERE a.SectionStatus = 'Active' ORDER BY b.CourseDescription, a.SectionYear ASC");
    }   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $section = DB::select('SELECT * FROM sections WHERE md5(concat(SectionID)) = "'.$request->SectionID.'"');
        $section_id = $section['0']->SectionID;

        
        $this->validate($request, [            
            'ProfessorID' => 'required|integer',
            'SubjectID' => 'required|integer|unique:subject_taggings,SubjectID,Null,id,SectionID,'.$section_id.',STSem,"'.$request->STSem.'",STYearFrom,"'.$request->STYearFrom.'",STYearTo,"'.$request->STYearTo.'"',
            'STUnits' => 'required|integer|min:1|max:6',
            'SubjectMeetings' => 'required|integer|min:1|max:2',
            ]);         
            
        $professor = DB::select('SELECT * FROM professors WHERE ProfessorID = "'.$request->ProfessorID.'"');
        $professor_id = $professor['0']->ProfessorID;

        if($request->SubjectMeetings == 1){
            $this->validate($request, [
                'Day1' => 'required',
                'Time_in1' => 'required',    
                'ctid1' => 'required',   
                'hours1' => 'required'    
            ]);
            $total_hours = $request->hours1 * (60*60);
            $timestamps = strtotime($request->Time_in1) + $total_hours;
            $Time_Out1 = date('H:i', $timestamps);
            $classroom_1 = '';
            $subject_taggings_id = 0;

            $classroom_available = DB::select('SELECT * FROM classrooms WHERE ClassroomType = "'.$request->ctid1.'"');
            shuffle($classroom_available);

            $can_sched = false;
            $and_di_pwede = '';
            foreach($classroom_available as $row){
                $can_sched = false;

                $subject_tagged_schedule = DB::select('SELECT * FROM subject_tagging_schedules WHERE 
                                            ClassroomID = "'.$row->ClassroomID.'" AND 
                                            STSStatus = "Active" AND
                                            STSDay = "'.$request->Day1.'" ORDER BY STSTimeStart ASC');

                // check if the classroom selected is available
                if($Time_Out1 > $row->ClassroomOut){
                    $can_sched = false;
                }
                else{

                    // check if no subject tagged in the classroom
                    if(empty($subject_tagged_schedule)){

                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                b.STStatus = "Active" AND 
                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                ');

                        // check if no schedule of section on the same day
                        if(empty($section_subject_schedule_save)){
                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');
                            
                            // check if the professor is available for the given day
                            if(empty($professor_subject_schedule_save)){
                                $can_sched = true;
                            }
                            else{
                                for($i = 0;$i < count($professor_subject_schedule_save);$i++){
                                    $j = $i;

                                    // check if the generated start time is available in the professor schedule
                                    if($request->Time_in1 >= $professor_subject_schedule_save[$i]->STSTimeEnd){
                                        
                                        $j++;

                                        // check if the schedule subject of professor is empty
                                        if(!isset($professor_subject_schedule_save[$j])){
                                            $can_sched = true;
                                            break 1;
                                        }// end of check if the schedule subject of professor is empty
                                        else{
                                            if($Time_Out1 <= $professor_subject_schedule_save[$j]->STSTimeStart){
                                                $can_sched = true;
                                                break 1;
                                            }
                                            else{
                                                $can_sched = false;
                                                // break;
                                                $and_di_pwede ='Professor is not Available';
                                            }
                                        }
                                        
                                    }// end of check if the generated start time is available in the professor schedule
                                    else{
                                        if($Time_Out1 <= $professor_subject_schedule_save[$i]->STSTimeStart){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            $can_sched = false;
                                            $and_di_pwede ='Professor is not Availablee';
                                            break 1;
                                        }
                                        
                                    }
                                }// end of loop of the subject and schedule that this professor have     
                            }

                        }
                        else{
                            for($i = 0;$i < count($section_subject_schedule_save);$i++){
                                $j = $i;
                                // check if the generated start time is available in the professor schedule
                                if($request->Time_in1 >= $section_subject_schedule_save[$i]->STSTimeEnd){
                                    
                                    $j++;

                                    // check if the schedule subject of professor is empty
                                    if(!isset($section_subject_schedule_save[$j])){


                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        $and_di_pwede ='Professor is not Availablee';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }


                                        // $can_sched = true;
                                        // break 1;
                                    }// end of check if the schedule subject of professor is empty
                                    else{
                                        if($Time_Out1 <= $section_subject_schedule_save[$j]->STSTimeStart){

                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                                    b.STStatus = "Active" AND 
                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                    ');

                                            // check if the professor is available for the given day
                                            if(empty($professor_subject_schedule_save)){
                                                $can_sched = true;
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched = false;
                                                                // break;
                                                                $and_di_pwede ='Professor is not Available';
                                                                
                                                                
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            $and_di_pwede ='Professor is not Availablee';
                                                            break 2;
                                                        }
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                            // $can_sched = true;
                                            // break 1;
                                        }
                                        else{
                                            $can_sched = false;
                                            // break;
                                            $and_di_pwede ='Section is not Available';
                                        }
                                    }
                                    
                                }// end of check if the generated start time is available in the professor schedule
                                else{

                                    if($Time_Out1 <= $section_subject_schedule_save[$i]->STSTimeStart){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        $and_di_pwede ='Professor is not Availablee';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }
                                    }
                                    else{
                                        $can_sched = false;
                                        $and_di_pwede ='Section is not Available';
                                        break 1;
                                    }
                                    
                                    
                                }
                            }// end of loop of the subject and schedule that this professor have     
                        }
                        
                    }   
                    else{
                        // loop of the subject that is scheduled in the classroom generated
                        for($i = 0;$i < count($subject_tagged_schedule);$i++){
                            $j = $i;

                            // check if the generated start time is available
                            if($request->Time_in1 >= $subject_tagged_schedule[$i]->STSTimeEnd){

                                $j++;

                                // check if the subject schedule in generate classroom is empty
                                if(!isset($subject_tagged_schedule[$j])){

                                    $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');

                                    // check if no schedule of section on the same day
                                    if(empty($section_subject_schedule_save)){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        //break;
                                                        $and_di_pwede ='Professor is not Availablee';
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                            $jj = $ii;
                                            // check if the generated start time is available in the professor schedule
                                            if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                
                                                $jj++;

                                                // check if the schedule subject of professor is empty
                                                if(!isset($section_subject_schedule_save[$jj])){


                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    //break;
                                                                    $and_di_pwede ='Professor is not Availablee';
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }


                                                    // $can_sched = true;
                                                    // break 1;
                                                }// end of check if the schedule subject of professor is empty
                                                else{
                                                    if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        //break;
                                                                        $and_di_pwede ='Professor is not Availablee';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched = true;
                                                        // break 1;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        // break;
                                                        $and_di_pwede ='Section is not Available';
                                                    }
                                                }
                                                
                                            }// end of check if the generated start time is available in the professor schedule
                                            else{

                                                if($Time_Out1 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    //break;
                                                                    $and_di_pwede ='Professor is not Availablee';
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }
                                                }
                                                else{
                                                    $can_sched = false;
                                                    //break;
                                                    $and_di_pwede ='Section is not Available';
                                                }

                                                
                                            }
                                        }// end of loop of the subject and schedule that this professor have     
                                    }


                                }// end of check if the subject schedule in generate classroom is empty
                                else{
                                    if($Time_out1 <= $subject_tagged_schedule[$j]->STSTimeStart){


                                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                        FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                        WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                        a.STSDay = "'.$request->Day1.'" AND 
                                                                        b.STStatus = "Active" AND 
                                                                        a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                        ');

                                        // check if no schedule of section on the same day
                                        if(empty($section_subject_schedule_save)){
                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                                    b.STStatus = "Active" AND 
                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                    ');

                                            // check if the professor is available for the given day
                                            if(empty($professor_subject_schedule_save)){
                                                $can_sched = true;
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched = false;
                                                                // break;
                                                                $and_di_pwede ='Professor is not Available';
                                                                
                                                                
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            //break;
                                                            $and_di_pwede ='Professor is not Availablee';
                                                        }
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                        }
                                        else{
                                            for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                                $jj = $ii;
                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($section_subject_schedule_save[$jj])){


                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        //break;
                                                                        $and_di_pwede ='Professor is not Availablee';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }


                                                        // $can_sched = true;
                                                        // break 1;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                                                    b.STStatus = "Active" AND 
                                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                    ');

                                                            // check if the professor is available for the given day
                                                            if(empty($professor_subject_schedule_save)){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                    $jjj = $iii;

                                                                    // check if the generated start time is available in the professor schedule
                                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                        
                                                                        $jjj++;

                                                                        // check if the schedule subject of professor is empty
                                                                        if(!isset($professor_subject_schedule_save[$jjj])){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }// end of check if the schedule subject of professor is empty
                                                                        else{
                                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                                $can_sched = true;
                                                                                break 3;
                                                                            }
                                                                            else{
                                                                                $can_sched = false;
                                                                                // break;
                                                                                $and_di_pwede ='Professor is not Available';
                                                                                
                                                                                
                                                                            }
                                                                        }
                                                                        
                                                                    }// end of check if the generated start time is available in the professor schedule
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            //break;
                                                                            $and_di_pwede ='Professor is not Availablee';
                                                                        }
                                                                        
                                                                    }
                                                                }// end of loop of the subject and schedule that this professor have     
                                                            }

                                                            // $can_sched = true;
                                                            // break 1;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Section is not Available';
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{

                                                    if($Time_Out1 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        //break;
                                                                        $and_di_pwede ='Professor is not Availablee';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        //break;
                                                        $and_di_pwede ='Section is not Available';
                                                    }

                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        $can_sched = false;
                                        $and_di_pwede = "No classroom is Available";
                                    }
                                }
                                
                            }// end of check if the generated start time is available
                            else{
                                
                                if($Time_Out1 <= $subject_tagged_schedule[$i]->STSTimeStart){
                                    $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');

                                    // check if no schedule of section on the same day
                                    if(empty($section_subject_schedule_save)){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        $and_di_pwede ='Professor is not Availablee';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                            $jj = $ii;
                                            // check if the generated start time is available in the professor schedule
                                            if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                
                                                $jj++;

                                                // check if the schedule subject of professor is empty
                                                if(!isset($section_subject_schedule_save[$jj])){


                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    $and_di_pwede ='Professor is not Availablee';
                                                                    break 3;
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }


                                                    // $can_sched = true;
                                                    // break 1;
                                                }// end of check if the schedule subject of professor is empty
                                                else{
                                                    if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        $and_di_pwede ='Professor is not Availablee';
                                                                        break 3;
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched = true;
                                                        // break 1;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        // break;
                                                        $and_di_pwede ='Section is not Available';
                                                    }
                                                }
                                                
                                            }// end of check if the generated start time is available in the professor schedule
                                            else{

                                                if($Time_Out1 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    $and_di_pwede ='Professor is not Availablee';
                                                                    break 3;
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }
                                                }
                                                else{
                                                    $can_sched = false;
                                                    $and_di_pwede ='Section is not Available';
                                                    break 2;
                                                }

                                                
                                            }
                                        }// end of loop of the subject and schedule that this professor have     
                                    }
                                }
                                else{
                                    $can_sched = false;
                                    $and_di_pwede = "No Classroom is Available";
                                    break 1;
                                }
                            }
                        }// end loop of the subject that is scheduled in the classroom generated                        
                    }
                }
                
                $classroom_1 = $row->ClassroomName;
                if($can_sched == true){
                    
                    $subject_tagging = DB::insert('
                        INSERT INTO subject_taggings (SubjectID,ProfessorID,SectionID,STUnits,STSem,STYear,STYearFrom,STYearTo,STStatus,created_at,updated_at) VALUES
                                                ("'.$request['SubjectID'].'","'.$request['ProfessorID'].'",
                                                "'.$section_id.'","'.$request['STUnits'].'","'.$request['STSem'].'","'.$request['STYear'].'",
                                                "'.$request['STYearFrom'].'","'.$request['STYearTo'].'",
                                                "'.$request['STStatus'].'",now(),now())
                        
                    ');

                    
                    $subject_taggings_id = DB::getPdo()->lastInsertId();              

                    DB::insert('INSERT INTO subject_tagging_schedules (STID,ClassroomID,STSHours,STSTimeStart,STSTimeEnd,STSDay,STSStatus,created_at,updated_at) VALUES
                    ("'.$subject_taggings_id.'","'.$row->ClassroomID.'","'.$request->hours1.'",
                    "'.$request->Time_in1.'","'.$Time_Out1.'","'.$request->Day1.'","Active",now(),now())');
                    break;
                }
            }

            if($can_sched == true){
                return ["type"=>"success","room"=>$classroom_1, "message" => "Schedule generated successfully","time_out" => $Time_Out1];
            }
            else{
                return ["type"=>"error","room"=>$classroom_1,"message"=>$and_di_pwede,"time_out" => $Time_Out1];
            }            

        }
        else{
            $this->validate($request, [
                'Day1' => 'required',
                'Time_in1' => 'required',
                'ctid1' => 'required',   
                'hours1' => 'required',    
                'Day2' => 'required',
                'Time_in2' => 'required',
                'ctid2' => 'required',    
                'hours2' => 'required',    
            ]);

            $subject_taggings_id = 0;

            $total_hours = $request->hours1 * (60*60);
            $timestamps = strtotime($request->Time_in1) + $total_hours;
            $Time_Out1 = date('H:i', $timestamps);
            $classroom_1 = '';

            $classroom_available = DB::select('SELECT * FROM classrooms WHERE ClassroomType = "'.$request->ctid1.'"');
            shuffle($classroom_available);

            $can_sched = false;
            $and_di_pwede = '';

            foreach($classroom_available as $row){
                $can_sched = false;

                $subject_tagged_schedule = DB::select('SELECT * FROM subject_tagging_schedules WHERE 
                                            ClassroomID = "'.$row->ClassroomID.'" AND 
                                            STSStatus = "Active" AND
                                            STSDay = "'.$request->Day1.'" ORDER BY STSTimeStart ASC');

                // check if the classroom selected is available
                if($Time_Out1 > $row->ClassroomOut){
                    $can_sched = false;
                }
                else{

                    // check if no subject tagged in the classroom
                    if(empty($subject_tagged_schedule)){

                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                b.STStatus = "Active" AND 
                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                ');

                        // check if no schedule of section on the same day
                        if(empty($section_subject_schedule_save)){
                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');
                            
                            // check if the professor is available for the given day
                            if(empty($professor_subject_schedule_save)){
                                $can_sched = true;
                            }
                            else{
                                for($i = 0;$i < count($professor_subject_schedule_save);$i++){
                                    $j = $i;

                                    // check if the generated start time is available in the professor schedule
                                    if($request->Time_in1 >= $professor_subject_schedule_save[$i]->STSTimeEnd){
                                        
                                        $j++;

                                        // check if the schedule subject of professor is empty
                                        if(!isset($professor_subject_schedule_save[$j])){
                                            $can_sched = true;
                                            break 1;
                                        }// end of check if the schedule subject of professor is empty
                                        else{
                                            if($Time_Out1 <= $professor_subject_schedule_save[$j]->STSTimeStart){
                                                $can_sched = true;
                                                break 1;
                                            }
                                            else{
                                                $can_sched = false;
                                                // break;
                                                $and_di_pwede ='Professor is not Available in Meeting 1';
                                            }
                                        }
                                        
                                    }// end of check if the generated start time is available in the professor schedule
                                    else{
                                        if($Time_Out1 <= $professor_subject_schedule_save[$i]->STSTimeStart){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            $can_sched = false;
                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                            break 1;
                                        }
                                        
                                    }
                                }// end of loop of the subject and schedule that this professor have     
                            }

                        }
                        else{
                            for($i = 0;$i < count($section_subject_schedule_save);$i++){
                                $j = $i;
                                // check if the generated start time is available in the professor schedule
                                if($request->Time_in1 >= $section_subject_schedule_save[$i]->STSTimeEnd){
                                    
                                    $j++;

                                    // check if the schedule subject of professor is empty
                                    if(!isset($section_subject_schedule_save[$j])){


                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }


                                        // $can_sched = true;
                                        // break 1;
                                    }// end of check if the schedule subject of professor is empty
                                    else{
                                        if($Time_Out1 <= $section_subject_schedule_save[$j]->STSTimeStart){

                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                                    b.STStatus = "Active" AND 
                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                    ');

                                            // check if the professor is available for the given day
                                            if(empty($professor_subject_schedule_save)){
                                                $can_sched = true;
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched = false;
                                                                // break;
                                                                $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                
                                                                
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                            break 2;
                                                        }
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                            // $can_sched = true;
                                            // break 1;
                                        }
                                        else{
                                            $can_sched = false;
                                            // break;
                                            $and_di_pwede ='Section is not Available in Meeting 1';
                                        }
                                    }
                                    
                                }// end of check if the generated start time is available in the professor schedule
                                else{

                                    if($Time_Out1 <= $section_subject_schedule_save[$i]->STSTimeStart){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }
                                    }
                                    else{
                                        $can_sched = false;
                                        $and_di_pwede ='Section is not Available in Meeting 1';
                                        break 1;
                                    }
                                    
                                    
                                }
                            }// end of loop of the subject and schedule that this professor have     
                        }
                        
                    }   
                    else{
                        // loop of the subject that is scheduled in the classroom generated
                        for($i = 0;$i < count($subject_tagged_schedule);$i++){
                            $j = $i;

                            // check if the generated start time is available
                            if($request->Time_in1 >= $subject_tagged_schedule[$i]->STSTimeEnd){

                                $j++;

                                // check if the subject schedule in generate classroom is empty
                                if(!isset($subject_tagged_schedule[$j])){

                                    $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');

                                    // check if no schedule of section on the same day
                                    if(empty($section_subject_schedule_save)){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        //break;
                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                            $jj = $ii;
                                            // check if the generated start time is available in the professor schedule
                                            if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                
                                                $jj++;

                                                // check if the schedule subject of professor is empty
                                                if(!isset($section_subject_schedule_save[$jj])){


                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    //break;
                                                                    $and_di_pwede ='Professor is not Available int Meeting 1';
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }


                                                    // $can_sched = true;
                                                    // break 1;
                                                }// end of check if the schedule subject of professor is empty
                                                else{
                                                    if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        //break;
                                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched = true;
                                                        // break 1;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        // break;
                                                        $and_di_pwede ='Section is not Available in Meeting 1';
                                                    }
                                                }
                                                
                                            }// end of check if the generated start time is available in the professor schedule
                                            else{

                                                if($Time_Out1 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    //break;
                                                                    $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }
                                                }
                                                else{
                                                    $can_sched = false;
                                                    //break;
                                                    $and_di_pwede ='Section is not Available in Meeting 1';
                                                }

                                                
                                            }
                                        }// end of loop of the subject and schedule that this professor have     
                                    }


                                }// end of check if the subject schedule in generate classroom is empty
                                else{
                                    if($Time_out1 <= $subject_tagged_schedule[$j]->STSTimeStart){


                                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                        FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                        WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                        a.STSDay = "'.$request->Day1.'" AND 
                                                                        b.STStatus = "Active" AND 
                                                                        a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                        ');

                                        // check if no schedule of section on the same day
                                        if(empty($section_subject_schedule_save)){
                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                                    b.STStatus = "Active" AND 
                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                    ');

                                            // check if the professor is available for the given day
                                            if(empty($professor_subject_schedule_save)){
                                                $can_sched = true;
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched = false;
                                                                // break;
                                                                $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                
                                                                
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            //break;
                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                        }
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                        }
                                        else{
                                            for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                                $jj = $ii;
                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($section_subject_schedule_save[$jj])){


                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        //break;
                                                                        $and_di_pwede ='Professor is not Availablee in Meeting 1';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }


                                                        // $can_sched = true;
                                                        // break 1;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                                                    b.STStatus = "Active" AND 
                                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                    ');

                                                            // check if the professor is available for the given day
                                                            if(empty($professor_subject_schedule_save)){
                                                                $can_sched = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                    $jjj = $iii;

                                                                    // check if the generated start time is available in the professor schedule
                                                                    if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                        
                                                                        $jjj++;

                                                                        // check if the schedule subject of professor is empty
                                                                        if(!isset($professor_subject_schedule_save[$jjj])){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }// end of check if the schedule subject of professor is empty
                                                                        else{
                                                                            if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                                $can_sched = true;
                                                                                break 3;
                                                                            }
                                                                            else{
                                                                                $can_sched = false;
                                                                                // break;
                                                                                $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                                
                                                                                
                                                                            }
                                                                        }
                                                                        
                                                                    }// end of check if the generated start time is available in the professor schedule
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            //break;
                                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                        }
                                                                        
                                                                    }
                                                                }// end of loop of the subject and schedule that this professor have     
                                                            }

                                                            // $can_sched = true;
                                                            // break 1;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Section is not Available in Meeting 1';
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{

                                                    if($Time_Out1 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        //break;
                                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        //break;
                                                        $and_di_pwede ='Section is not Available in Meeeting 1';
                                                    }

                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        $can_sched = false;
                                        $and_di_pwede = "No classroom is Available in Meeting 1";
                                    }
                                }
                                
                            }// end of check if the generated start time is available
                            else{
                                
                                if($Time_Out1 <= $subject_tagged_schedule[$i]->STSTimeStart){
                                    $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                    a.STSDay = "'.$request->Day1.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');

                                    // check if no schedule of section on the same day
                                    if(empty($section_subject_schedule_save)){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in1 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched = false;
                                                            // break;
                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out1 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                            $jj = $ii;
                                            // check if the generated start time is available in the professor schedule
                                            if($request->Time_in1 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                
                                                $jj++;

                                                // check if the schedule subject of professor is empty
                                                if(!isset($section_subject_schedule_save[$jj])){


                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                    break 3;
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }


                                                    // $can_sched = true;
                                                    // break 1;
                                                }// end of check if the schedule subject of professor is empty
                                                else{
                                                    if($Time_Out1 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day1.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched = false;
                                                                            // break;
                                                                            $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                        break 3;
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched = true;
                                                        // break 1;
                                                    }
                                                    else{
                                                        $can_sched = false;
                                                        // break;
                                                        $and_di_pwede ='Section is not Available in Meeting 1';
                                                    }
                                                }
                                                
                                            }// end of check if the generated start time is available in the professor schedule
                                            else{

                                                if($Time_Out1 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day1.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in1 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out1 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched = false;
                                                                        // break;
                                                                        $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out1 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    $and_di_pwede ='Professor is not Available in Meeting 1';
                                                                    break 3;
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }
                                                }
                                                else{
                                                    $can_sched = false;
                                                    $and_di_pwede ='Section is not Available in Meeting 1';
                                                    break 2;
                                                }

                                                
                                            }
                                        }// end of loop of the subject and schedule that this professor have     
                                    }
                                }
                                else{
                                    $can_sched = false;
                                    $and_di_pwede = "Classroom is not Available in Meeting 1";
                                    break 1;
                                }
                            }
                        }// end loop of the subject that is scheduled in the classroom generated                        
                    }
                }
                
                if($can_sched == true){
                    $classroom_1 = $row->ClassroomID;
                    break;
                }
            }

            $total_hours1 = $request->hours2 * (60*60);
            $timestamps1 = strtotime($request->Time_in2) + $total_hours1;
            $Time_Out2 = date('H:i', $timestamps1);
            $classroom_2 = '';

            $classroom_available2 = DB::select('SELECT * FROM classrooms WHERE ClassroomType = "'.$request->ctid2.'"');
            shuffle($classroom_available);

            $can_sched_1 = false;
            $and_di_pwede_1 = '';

            foreach($classroom_available2 as $row){
                $can_sched_1 = false;

                $subject_tagged_schedule = DB::select('SELECT * FROM subject_tagging_schedules WHERE 
                                            ClassroomID = "'.$row->ClassroomID.'" AND 
                                            STSStatus = "Active" AND
                                            STSDay = "'.$request->Day2.'" ORDER BY STSTimeStart ASC');

                // check if the classroom selected is available
                if($Time_Out2 > $row->ClassroomOut){
                    $can_sched_1 = false;
                }
                else{

                    // check if no subject tagged in the classroom
                    if(empty($subject_tagged_schedule)){

                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                b.STStatus = "Active" AND 
                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                ');

                        // check if no schedule of section on the same day
                        if(empty($section_subject_schedule_save)){
                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                    a.STSDay = "'.$request->Day2.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');
                            
                            // check if the professor is available for the given day
                            if(empty($professor_subject_schedule_save)){
                                $can_sched_1 = true;
                            }
                            else{
                                for($i = 0;$i < count($professor_subject_schedule_save);$i++){
                                    $j = $i;

                                    // check if the generated start time is available in the professor schedule
                                    if($request->Time_in2 >= $professor_subject_schedule_save[$i]->STSTimeEnd){
                                        
                                        $j++;

                                        // check if the schedule subject of professor is empty
                                        if(!isset($professor_subject_schedule_save[$j])){
                                            $can_sched_1 = true;
                                            break 1;
                                        }// end of check if the schedule subject of professor is empty
                                        else{
                                            if($Time_Out2 <= $professor_subject_schedule_save[$j]->STSTimeStart){
                                                $can_sched_1 = true;
                                                break 1;
                                            }
                                            else{
                                                $can_sched_1 = false;
                                                // break;
                                                $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                            }
                                        }
                                        
                                    }// end of check if the generated start time is available in the professor schedule
                                    else{
                                        if($Time_Out2 <= $professor_subject_schedule_save[$i]->STSTimeStart){
                                            $can_sched_1 = true;
                                            break 1;
                                        }
                                        else{
                                            $can_sched_1 = false;
                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                            break 1;
                                        }
                                        
                                    }
                                }// end of loop of the subject and schedule that this professor have     
                            }

                        }
                        else{
                            for($i = 0;$i < count($section_subject_schedule_save);$i++){
                                $j = $i;
                                // check if the generated start time is available in the professor schedule
                                if($request->Time_in2 >= $section_subject_schedule_save[$i]->STSTimeEnd){
                                    
                                    $j++;

                                    // check if the schedule subject of professor is empty
                                    if(!isset($section_subject_schedule_save[$j])){


                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched_1 = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in2 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched_1 = false;
                                                            // break;
                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out2 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched_1 = false;
                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }


                                        // $can_sched_1 = true;
                                        // break 1;
                                    }// end of check if the schedule subject of professor is empty
                                    else{
                                        if($Time_Out2 <= $section_subject_schedule_save[$j]->STSTimeStart){

                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                    a.STSDay = "'.$request->Day2.'" AND 
                                                                                    b.STStatus = "Active" AND 
                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                    ');

                                            // check if the professor is available for the given day
                                            if(empty($professor_subject_schedule_save)){
                                                $can_sched_1 = true;
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in2 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out2 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched_1 = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched_1 = false;
                                                                // break;
                                                                $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                
                                                                
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        if($Time_Out2 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched_1 = false;
                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                            break 2;
                                                        }
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                            // $can_sched_1 = true;
                                            // break 1;
                                        }
                                        else{
                                            $can_sched_1 = false;
                                            // break;
                                            $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                        }
                                    }
                                    
                                }// end of check if the generated start time is available in the professor schedule
                                else{

                                    if($Time_Out2 <= $section_subject_schedule_save[$i]->STSTimeStart){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched_1 = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in2 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched_1 = false;
                                                            // break;
                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out2 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched_1 = false;
                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }
                                    }
                                    else{
                                        $can_sched_1_1 = false;
                                        $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                        break 1;
                                    }
                                    
                                    
                                }
                            }// end of loop of the subject and schedule that this professor have     
                        }
                        
                    }   
                    else{
                        // loop of the subject that is scheduled in the classroom generated
                        for($i = 0;$i < count($subject_tagged_schedule);$i++){
                            $j = $i;

                            // check if the generated start time is available
                            if($request->Time_in2 >= $subject_tagged_schedule[$i]->STSTimeEnd){

                                $j++;

                                // check if the subject schedule in generate classroom is empty
                                if(!isset($subject_tagged_schedule[$j])){

                                    $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                    a.STSDay = "'.$request->Day2.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');

                                    // check if no schedule of section on the same day
                                    if(empty($section_subject_schedule_save)){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched_1 = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in2 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched_1 = false;
                                                            // break;
                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out2 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched_1 = false;
                                                        //break;
                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                            $jj = $ii;
                                            // check if the generated start time is available in the professor schedule
                                            if($request->Time_in2 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                
                                                $jj++;

                                                // check if the schedule subject of professor is empty
                                                if(!isset($section_subject_schedule_save[$jj])){


                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day2.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        // break;
                                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched_1 = false;
                                                                    //break;
                                                                    $and_di_pwede_1 ='Professor is not Available int Meeting 2';
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }


                                                    // $can_sched_1 = true;
                                                    // break 1;
                                                }// end of check if the schedule subject of professor is empty
                                                else{
                                                    if($Time_Out2 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched_1 = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched_1 = false;
                                                                            // break;
                                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        //break;
                                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched_1 = true;
                                                        // break 1;
                                                    }
                                                    else{
                                                        $can_sched_1 = false;
                                                        // break;
                                                        $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                                    }
                                                }
                                                
                                            }// end of check if the generated start time is available in the professor schedule
                                            else{

                                                if($Time_Out2 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day2.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        // break;
                                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched_1 = false;
                                                                    //break;
                                                                    $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }
                                                }
                                                else{
                                                    $can_sched_1 = false;
                                                    //break;
                                                    $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                                }

                                                
                                            }
                                        }// end of loop of the subject and schedule that this professor have     
                                    }


                                }// end of check if the subject schedule in generate classroom is empty
                                else{
                                    if($Time_out1 <= $subject_tagged_schedule[$j]->STSTimeStart){


                                        $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                        FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                        WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                        a.STSDay = "'.$request->Day2.'" AND 
                                                                        b.STStatus = "Active" AND 
                                                                        a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                        ');

                                        // check if no schedule of section on the same day
                                        if(empty($section_subject_schedule_save)){
                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                    a.STSDay = "'.$request->Day2.'" AND 
                                                                                    b.STStatus = "Active" AND 
                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                    ');

                                            // check if the professor is available for the given day
                                            if(empty($professor_subject_schedule_save)){
                                                $can_sched_1 = true;
                                                break 1;
                                            }
                                            else{
                                                for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available in the professor schedule
                                                    if($request->Time_in2 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the schedule subject of professor is empty
                                                        if(!isset($professor_subject_schedule_save[$jj])){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }// end of check if the schedule subject of professor is empty
                                                        else{
                                                            if($Time_Out2 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched_1 = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                $can_sched_1 = false;
                                                                // break;
                                                                $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                
                                                                
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available in the professor schedule
                                                    else{
                                                        if($Time_Out2 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched_1 = false;
                                                            //break;
                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                        }
                                                        
                                                    }
                                                }// end of loop of the subject and schedule that this professor have     
                                            }

                                        }
                                        else{
                                            for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                                $jj = $ii;
                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in2 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($section_subject_schedule_save[$jj])){


                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched_1 = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched_1 = false;
                                                                            // break;
                                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        //break;
                                                                        $and_di_pwede_1 ='Professor is not Availablee in Meeting 2';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }


                                                        // $can_sched_1 = true;
                                                        // break 1;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out2 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                            $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                    WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                    a.STSDay = "'.$request->Day2.'" AND 
                                                                                                    b.STStatus = "Active" AND 
                                                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                    ');

                                                            // check if the professor is available for the given day
                                                            if(empty($professor_subject_schedule_save)){
                                                                $can_sched_1 = true;
                                                                break 2;
                                                            }
                                                            else{
                                                                for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                    $jjj = $iii;

                                                                    // check if the generated start time is available in the professor schedule
                                                                    if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                        
                                                                        $jjj++;

                                                                        // check if the schedule subject of professor is empty
                                                                        if(!isset($professor_subject_schedule_save[$jjj])){
                                                                            $can_sched_1 = true;
                                                                            break 3;
                                                                        }// end of check if the schedule subject of professor is empty
                                                                        else{
                                                                            if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                                $can_sched_1 = true;
                                                                                break 3;
                                                                            }
                                                                            else{
                                                                                $can_sched_1 = false;
                                                                                // break;
                                                                                $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                                
                                                                                
                                                                            }
                                                                        }
                                                                        
                                                                    }// end of check if the generated start time is available in the professor schedule
                                                                    else{
                                                                        if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                            $can_sched_1 = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched_1 = false;
                                                                            //break;
                                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                        }
                                                                        
                                                                    }
                                                                }// end of loop of the subject and schedule that this professor have     
                                                            }

                                                            // $can_sched_1 = true;
                                                            // break 1;
                                                        }
                                                        else{
                                                            $can_sched_1 = false;
                                                            // break;
                                                            $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{

                                                    if($Time_Out2 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched_1 = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched_1 = false;
                                                                            // break;
                                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        //break;
                                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }
                                                    }
                                                    else{
                                                        $can_sched_1 = false;
                                                        //break;
                                                        $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                                    }

                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        $can_sched_1 = false;
                                        $and_di_pwede_1 = "No classroom is Available in Meeting 2";
                                    }
                                }
                                
                            }// end of check if the generated start time is available
                            else{
                                
                                if($Time_Out2 <= $subject_tagged_schedule[$i]->STSTimeStart){
                                    $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                    FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                    WHERE md5(concat(b.SectionID)) = "'.$request->SectionID.'" AND 
                                                                    a.STSDay = "'.$request->Day2.'" AND 
                                                                    b.STStatus = "Active" AND 
                                                                    a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                    ');

                                    // check if no schedule of section on the same day
                                    if(empty($section_subject_schedule_save)){
                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                        // check if the professor is available for the given day
                                        if(empty($professor_subject_schedule_save)){
                                            $can_sched_1 = true;
                                            break 1;
                                        }
                                        else{
                                            for($ii = 0;$ii < count($professor_subject_schedule_save);$ii++){
                                                $jj = $ii;

                                                // check if the generated start time is available in the professor schedule
                                                if($request->Time_in2 >= $professor_subject_schedule_save[$ii]->STSTimeEnd){
                                                    
                                                    $jj++;

                                                    // check if the schedule subject of professor is empty
                                                    if(!isset($professor_subject_schedule_save[$jj])){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }// end of check if the schedule subject of professor is empty
                                                    else{
                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jj]->STSTimeStart){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            $can_sched_1 = false;
                                                            // break;
                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                            
                                                            
                                                        }
                                                    }
                                                    
                                                }// end of check if the generated start time is available in the professor schedule
                                                else{
                                                    if($Time_Out2 <= $professor_subject_schedule_save[$ii]->STSTimeStart){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        $can_sched_1 = false;
                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                        break 2;
                                                    }
                                                    
                                                }
                                            }// end of loop of the subject and schedule that this professor have     
                                        }

                                    }
                                    else{
                                        for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                            $jj = $ii;
                                            // check if the generated start time is available in the professor schedule
                                            if($request->Time_in2 >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                
                                                $jj++;

                                                // check if the schedule subject of professor is empty
                                                if(!isset($section_subject_schedule_save[$jj])){


                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day2.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        // break;
                                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched_1 = false;
                                                                    $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                    break 3;
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }


                                                    // $can_sched_1 = true;
                                                    // break 1;
                                                }// end of check if the schedule subject of professor is empty
                                                else{
                                                    if($Time_Out2 <= $section_subject_schedule_save[$jj]->STSTimeStart){

                                                        $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                                WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                                a.STSDay = "'.$request->Day2.'" AND 
                                                                                                b.STStatus = "Active" AND 
                                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                                ');

                                                        // check if the professor is available for the given day
                                                        if(empty($professor_subject_schedule_save)){
                                                            $can_sched_1 = true;
                                                            break 2;
                                                        }
                                                        else{
                                                            for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                                $jjj = $iii;

                                                                // check if the generated start time is available in the professor schedule
                                                                if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                    
                                                                    $jjj++;

                                                                    // check if the schedule subject of professor is empty
                                                                    if(!isset($professor_subject_schedule_save[$jjj])){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }// end of check if the schedule subject of professor is empty
                                                                    else{
                                                                        if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                            $can_sched_1 = true;
                                                                            break 3;
                                                                        }
                                                                        else{
                                                                            $can_sched_1 = false;
                                                                            // break;
                                                                            $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                            
                                                                            
                                                                        }
                                                                    }
                                                                    
                                                                }// end of check if the generated start time is available in the professor schedule
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                        break 3;
                                                                    }
                                                                    
                                                                }
                                                            }// end of loop of the subject and schedule that this professor have     
                                                        }

                                                        // $can_sched_1 = true;
                                                        // break 1;
                                                    }
                                                    else{
                                                        $can_sched_1 = false;
                                                        // break;
                                                        $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                                    }
                                                }
                                                
                                            }// end of check if the generated start time is available in the professor schedule
                                            else{

                                                if($Time_Out2 <= $section_subject_schedule_save[$ii]->STSTimeStart){
                                                    $professor_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                            WHERE b.ProfessorID = "'.$request->ProfessorID.'" AND 
                                                                                            a.STSDay = "'.$request->Day2.'" AND 
                                                                                            b.STStatus = "Active" AND 
                                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                            ');

                                                    // check if the professor is available for the given day
                                                    if(empty($professor_subject_schedule_save)){
                                                        $can_sched_1 = true;
                                                        break 2;
                                                    }
                                                    else{
                                                        for($iii = 0;$iii < count($professor_subject_schedule_save);$iii++){
                                                            $jjj = $iii;

                                                            // check if the generated start time is available in the professor schedule
                                                            if($request->Time_in2 >= $professor_subject_schedule_save[$iii]->STSTimeEnd){
                                                                
                                                                $jjj++;

                                                                // check if the schedule subject of professor is empty
                                                                if(!isset($professor_subject_schedule_save[$jjj])){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }// end of check if the schedule subject of professor is empty
                                                                else{
                                                                    if($Time_Out2 <= $professor_subject_schedule_save[$jjj]->STSTimeStart){
                                                                        $can_sched_1 = true;
                                                                        break 3;
                                                                    }
                                                                    else{
                                                                        $can_sched_1 = false;
                                                                        // break;
                                                                        $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                        
                                                                        
                                                                    }
                                                                }
                                                                
                                                            }// end of check if the generated start time is available in the professor schedule
                                                            else{
                                                                if($Time_Out2 <= $professor_subject_schedule_save[$iii]->STSTimeStart){
                                                                    $can_sched_1 = true;
                                                                    break 3;
                                                                }
                                                                else{
                                                                    $can_sched_1 = false;
                                                                    $and_di_pwede_1 ='Professor is not Available in Meeting 2';
                                                                    break 3;
                                                                }
                                                                
                                                            }
                                                        }// end of loop of the subject and schedule that this professor have     
                                                    }
                                                }
                                                else{
                                                    $can_sched_1 = false;
                                                    $and_di_pwede_1 ='Section is not Available in Meeting 2';
                                                    break 2;
                                                }

                                                
                                            }
                                        }// end of loop of the subject and schedule that this professor have     
                                    }
                                }
                                else{
                                    $can_sched_1 = false;
                                    $and_di_pwede_1 = "Classroom is not Available in Meeting 2";
                                    break 1;
                                }
                            }
                        }// end loop of the subject that is scheduled in the classroom generated                        
                    }
                }
                
                if($can_sched_1 == true){
                    $classroom_2 = $row->ClassroomID;
                    break;
                }
            }


            if($can_sched == true && $can_sched_1 == true){
                $subject_tagging = DB::insert('
                INSERT INTO subject_taggings (SubjectID,ProfessorID,SectionID,STUnits,STSem,STYear,STYearFrom,STYearTo,STStatus,created_at,updated_at) VALUES
                                        ("'.$request['SubjectID'].'","'.$request['ProfessorID'].'",
                                        "'.$section_id.'","'.$request['STUnits'].'","'.$request['STSem'].'","'.$request['STYear'].'",
                                        "'.$request['STYearFrom'].'","'.$request['STYearTo'].'",
                                        "'.$request['STStatus'].'",now(),now())
                
                ');

                $subject_taggings_id = DB::getPdo()->lastInsertId();              

                DB::insert('INSERT INTO subject_tagging_schedules (STID,ClassroomID,STSHours,STSTimeStart,STSTimeEnd,STSDay,STSStatus,created_at,updated_at) VALUES
                    ("'.$subject_taggings_id.'","'.$row->ClassroomID.'","'.$request->hours1.'",
                    "'.$request->Time_in1.'","'.$Time_Out1.'","'.$request->Day1.'","Active",now(),now())');

                DB::insert('INSERT INTO subject_tagging_schedules (STID,ClassroomID,STSHours,STSTimeStart,STSTimeEnd,STSDay,STSStatus,created_at,updated_at) VALUES
                ("'.$subject_taggings_id.'","'.$classroom_2.'","'.$request->hours2.'",
                "'.$request->Time_in2.'","'.$Time_Out2.'","'.$request->Day2.'","Active",now(),now())');

                return ["type"=>"success", "message" => "Schedule generated successfully"];
            }
            else{
                if($can_sched == false){
                    return ["type"=>"error","message"=>$and_di_pwede];
                }
                if($can_sched_1 == false){
                    return ["type"=>"error","message"=>$and_di_pwede_1];
                }
            }            


        }


        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function get_professor(){
        return DB::select('SELECT ProfessorID,ProfessorName,created_at FROM professors ORDER BY ProfessorName ASC');
    }

    public function subjects_per_course_year_sem($course_id,$year,$sem){

        // return DB::select('SELECT b.SubjectID,b.SubjectDescription FROM course_subject_offereds a 
        //                 INNER JOIN subjects b ON a.SubjectID = b.SubjectID
        //                 WHERE a.CourseID = "'.$course_id.'" AND a.CSOYear = "'.$year.'" AND a.CSOSem = "'.$sem.'"        
        //         ');

        return DB::select('SELECT SubjectID,SubjectDescription FROM subjects ORDER BY SubjectDescription ASC   
                ');
    }

    public function tagged_subject_sections($section_id,$sem,$year,$year_from,$year_to){
        // return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,c.ProfessorName,GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
        //                     FROM subject_taggings a 
        //                     INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
        //                     INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
        //                     LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
        //                     INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
        //                     WHERE md5(concat(a.SectionID)) = "'.$section_id.'" AND 
        //                     a.STSem = "'.$sem.'" AND 
        //                     a.STYear = "'.$year.'" AND
        //                     a.STYearFrom = "'.$year_from.'" AND
        //                     a.STYearTo = "'.$year_to.'" AND
        //                     a.STStatus = "Active"
        //                     GROUP BY b.SubjectDescription,a.STID,c.ProfessorName
        //                 ');


        return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,b.SubjectID,c.ProfessorID,c.ProfessorName,a.STUnits,
                            GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule,
                            SUM(d.STSHours) AS TotalHours 
                            FROM subject_taggings a 
                            INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                            INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                            LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                            INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                            WHERE md5(concat(a.SectionID)) = "'.$section_id.'" AND 
                            a.STSem = "'.$sem.'" AND 
                            a.STYear = "'.$year.'" AND
                            a.STYearFrom = "'.$year_from.'" AND
                            a.STYearTo = "'.$year_to.'" AND
                            a.STStatus = "Active"
                            GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,c.ProfessorID,b.SubjectID,a.STUnits
                        ');
    }

    public function schedule_per_subject($id){
        return DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd,b.ClassroomCode FROM 
                        subject_tagging_schedules a INNER JOIN classrooms b ON a.ClassroomID = b.ClassroomID 
                        WHERE md5(concat(a.STID)) = "'.$id.'" AND a.STSStatus = "Active"');
    }

    public function update_status_subject_schedule($sem,$year_from,$year_to){
        $sched_expired =  DB::select('SELECT * FROM subject_taggings WHERE 
                    STSem <> "'.$sem.'" OR 
                    STYearFrom <> "'.$year_from.'" OR
                    STYearTo <> "'.$year_to.'"
                    ');

        foreach($sched_expired as $row){
            DB::update('UPDATE subject_tagging_schedules SET 
                        STSStatus = "Inactive"
                        WHERE STID = "'.$row->STID.'"');

            DB::update('UPDATE subject_taggings SET 
            STStatus = "Inactive"
            WHERE STID = "'.$row->STID.'"');
        }
    }

    public function delete_subject_schedule($id){
        $delete_subject_schedule =  DB::select('SELECT * FROM subject_taggings WHERE 
                    md5(concat(STID)) = "'.$id.'"
                    ');

        foreach($delete_subject_schedule as $row){
            DB::delete('DELETE FROM subject_tagging_schedules WHERE md5(concat(STID)) = "'.$id.'"');
        }

        DB::delete('DELETE FROM subject_taggings WHERE md5(concat(STID)) = "'.$id.'"');
    }
    
    public function print_section_schedule($id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_schedule_table_to_html($id));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('section_schedule.pdf');        
    }
    
    public function convert_schedule_table_to_html($id){
        $year_today = date('Y');
        $month_today = date('m');
        $section_year = 0;

        $section = DB::select('SELECT a.*,b.* FROM sections a INNER JOIN courses b ON a.CourseID = b.CourseID WHERE md5(concat(a.SectionID)) = "'.$id.'"');

        $data = DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,
                            f.SectionName,c.ProfessorName,g.CourseCode,a.STUnits,SUM(d.STSHours) AS TotalHours,
                            GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                            FROM subject_taggings a 
                            INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                            INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                            LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                            INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                            INNER JOIN sections f ON a.SectionID = f.SectionID
                            INNER JOIN courses g ON f.CourseID = g.CourseID
                            WHERE md5(concat(a.SectionID)) = "'.$id.'" AND 
                            a.STStatus = "Active"
                            GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STUnits
                        ');

        if($month_today >= 6 && $month_today <= 10){
            $sem = "First Semester";
        }
        if($month_today >= 11 && $month_today <= 12){
            $sem = "Second Semester";
        }
        if($month_today >= 1 && $month_today <= 3){
            $sem = "Second Semester";
        }
        if($month_today >=4 && $month_today <= 5){
            $sem = "Summer Semester";
        }
        
        
        if($month_today >= 6 && $month_today <= 10){
            $year_from = $year_today;
            $year_to = $year_today + 1;
        }
        if($month_today >= 11 && $month_today <= 12){
            $year_from = $year_today;
            $year_to = $year_today + 1;
        }
        if($month_today >= 1 && $month_today <= 3){
            $year_from = $year_today - 1;
            $year_to = $year_today;
        }

        if($month_today >=4 && $month_today <= 5){
            $year_from = $year_today - 1;
            $year_to = $year_today;
        }
                
                        
        $output = '';
        if($month_today >= 5 && $month_today <= 9){
            $section_year = $year_today - $section[0]->SectionYear + 1;
        }
        else{
            $section_year = $year_today - $section[0]->SectionYear;
        }

        if(empty($data)){
            $output .= '
                <h3 align="center";>'.$section[0]->CourseCode.' '.$section_year.' - '.$section[0]->SectionName.' Schedules</h3>
                <h5 align="center";>'.$sem.'  SY '.$year_from.' - '.$year_to.'</h5>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:20%;">Subject Name</th>
                            <th style="border: 1px solid;padding: 12px;width:20%;">Professor</th>
                            <th style="border: 1px solid;padding: 12px;width:5%;">Units</th>
                            <th style="border: 1px solid;padding: 12px;width:5%;">Hours</th>
                            <th style="border: 1px solid;padding: 12px;width:50%;">Schedule</th>
                        </tr>                        
                    </thead>
                    <tbody>
            ';
            $output .='
                    </tbody>
                </table>
            ';

        }
        else{
    
    
            $output .= '
                <h3 align="center";>'.$section[0]->CourseCode.' '.$section_year.' - '.$section[0]->SectionName.' Schedules</h3>
                <h5 align="center";>'.$sem.'  SY '.$year_from.' - '.$year_to.'</h5>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:20%;">Subject Name</th>
                            <th style="border: 1px solid;padding: 12px;width:20%;">Professor</th>
                            <th style="border: 1px solid;padding: 12px;width:5%;">Units</th>
                            <th style="border: 1px solid;padding: 12px;width:5%;">Hours</th>
                            <th style="border: 1px solid;padding: 12px;width:50%;">Schedule</th>
                        </tr>                        
                    </thead>
                    <tbody>
            ';
            foreach($data as $row){
                $output .= '
                    <tr>';
                
                $output .= '
                        <td style="border: 1px solid;padding: 5px;">
                            '.$row->SubjectDescription.'
                        </td>
                        <td style="border: 1px solid;padding: 5px;">
                            '.$row->ProfessorName.'
                        </td>
                        <td style="border: 1px solid;padding: 5px;">
                            '.$row->STUnits.'
                        </td>
                        <td style="border: 1px solid;padding: 5px;">
                            '.$row->TotalHours.'
                        </td>
                        <td style="border: 1px solid;padding: 5px;">
                            '.$row->Schedule.'
                        </td>    
                ';
    
                $output .= '
                    </tr>';
            }
            $output .='
                    </tbody>
                </table>
            ';

        }

        return $output;        

    }

    public function subjecttaggingschedules($STID){
        return DB::select('SELECT md5(concat(a.STSID)) STSID, a.STID, a.SMID, a.STSTimeStart, a.STSTimeEnd, a.STSDay,b.SubjectHours
                        FROM subject_tagging_schedules a INNER JOIN subject_meetings b ON a.SMID = b.SMID WHERE md5(concat(a.STID)) = "'.$STID.'"');       
    }

    public function get_days(){
        return DB::select('SELECT * FROM days');
    }

    public function get_schedules(){
        return DB::select('SELECT * FROM schedules ORDER BY SchedTime ASC');
    }

    public function get_classroom_type($smid){
        return DB::select('SELECT a.*,b.* 
                    FROM classrooms a INNER JOIN classroom_types b ON a.ClassroomType = b.CTID 
                    INNER JOIN subject_meetings c ON c.CTID = a.ClassroomType
                    WHERE c.SMID = "'.$smid.'"');
    }

    public function get_subject_meetings($subject_id){
        return DB::select('SELECT a.*,b.* FROM subjects a INNER JOIN subject_meetings b ON a.SubjectID = b.SubjectID
                        WHERE a.SubjectID = "'.$subject_id.'"
                        ');
    }

    public function tagged_subjects_schedule($stid){
        return DB::select('SELECT a.*,b.*,c.* FROM subject_taggings a 
                            INNER JOIN subject_tagging_schedules b ON a.STID = b.STID 
                            INNER JOIN classrooms c ON b.ClassroomID = c.ClassroomID
                            WHERE md5(concat(a.STID)) = "'.$stid.'"
                        ');
    }

    public function get_classroom_options($ctid){
        return DB::select('SELECT a.*,b.* 
                    FROM classrooms a INNER JOIN classroom_types b ON a.ClassroomType = b.CTID 
                    WHERE a.ClassroomType = "'.$ctid.'"');    
    }

    public function update_subjecttagging(Request $request, $STID){
        $section = DB::select('SELECT * FROM sections WHERE md5(concat(SectionID)) = "'.$request->SectionID.'"');
        $section_id = $section['0']->SectionID;

        $stid = DB::select('SELECT * FROM subject_taggings WHERE md5(concat(STID)) = "'.$STID.'"');
        $st_id = $stid['0']->STID;

        
        $this->validate($request, [            
            'ProfessorID' => 'required|integer',
            'SubjectID' => 'required|integer|unique:subject_taggings,SubjectID,'.$st_id.',STID,SectionID,'.$section_id.',STSem,"'.$request->STSem.'",STYearFrom,"'.$request->STYearFrom.'",STYearTo,"'.$request->STYearTo.'"',
            'STUnits' => 'required|integer|min:1|max:6',
            'SubjectMeetings' => 'required|integer|min:1|max:2',
            ]);         
            
        $professor = DB::select('SELECT * FROM professors WHERE ProfessorID = "'.$request->ProfessorID.'"');
        $professor_id = $professor['0']->ProfessorID;        
    }
}
