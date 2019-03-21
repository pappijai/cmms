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



        return DB::select("SELECT md5(concat(a.SectionID)) SectionID, a.SectionName,a.SectionYear,a.CourseID,b.CourseDescription,b.CourseYears 
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
        ]);         

        $subject_tagging = DB::insert('
            INSERT INTO subject_taggings (SubjectID,ProfessorID,SectionID,STSem,STYear,STYearFrom,STYearTo,STStatus,created_at,updated_at) VALUES
                                    ("'.$request['SubjectID'].'","'.$request['ProfessorID'].'",
                                    "'.$section_id.'","'.$request['STSem'].'","'.$request['STYear'].'",
                                    "'.$request['STYearFrom'].'","'.$request['STYearTo'].'",
                                    "'.$request['STStatus'].'",now(),now())
            
        ');

        $subject_taggings_id = DB::getPdo()->lastInsertId();
        
        // get the subject meetings of the subject added
        $subject_meetings = DB::select('SELECT * FROM subject_meetings WHERE SubjectID = "'.$request['SubjectID'].'"');
        
        // get the time and days 
        $schedule_time = DB::select('SELECT * FROM schedules');
        $days = DB::select('SELECT * FROM days');
        
             
        // foreach loop for subject meetings
        foreach($subject_meetings as $row){
            $classroom_available = DB::select('SELECT * FROM classrooms WHERE ClassroomType = "'.$row->CTID.'"');
            
            shuffle($classroom_available);

            // foreach loop for classroom available
            foreach($classroom_available as $row_1){
                
                $sts_day = '';
                $start_time = '';
                $total_hours = 0;
                $timestamps = '';
                $end_time = '';
                $can_sched = false;  
                $record_sched_start = '';               
                
                shuffle($days);

                // foreach loop of days
                foreach($days as $row_2){

                    // getting all the tagged schedule where the day is same day in the loop and same the classroom ID
                    $subject_tagged_schedule = DB::select('SELECT * FROM subject_tagging_schedules WHERE 
                    ClassroomID = "'.$row_1->ClassroomID.'" AND 
                    STSStatus = "Active" AND
                    STSDay = "'.$row_2->DayName.'" ORDER BY STSTimeStart ASC');
                    
                    // foreach loop of schedule time
                    foreach($schedule_time as $row_3){
                        // check if the generated end time is greater than the classroom availability
                        if($end_time > $row_1->ClassroomOut){
                            break 2;
                        }// end of check if the generated end time is greater than the classroom availability
                        else{
                            $sts_day = $row_2->DayName;
                            $start_time = $row_3->SchedTime;
                            $total_hours = $row->SubjectHours * (60*60);
                            $timestamps = strtotime($row_3->SchedTime) + $total_hours;
                            $end_time = date('H:i', $timestamps);

                            // check if no schedule for the room selected
                            if(empty($subject_tagged_schedule)){

                                // getting all the secton subject schedule that is active
                                $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                WHERE b.SectionID = "'.$section_id.'" AND 
                                                                a.STSDay = "'.$sts_day.'" AND 
                                                                b.STYear = "'.$request['STYear'].'" AND
                                                                b.STStatus = "Active" AND 
                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                ');

                                // check the schedule of section if empty
                                if(empty($section_subject_schedule_save)){
                                    $can_sched = true;
                                    break 2;
                                }// end of check the schedule of section if empty
                                else{
                                    // loop of the subject and schedule that this section have
                                    for($i = 0;$i < count($section_subject_schedule_save);$i++){
                                        $j = $i;

                                        // check if the generated start time is available in the section schedule
                                        if($start_time >= $section_subject_schedule_save[$i]->STSTimeEnd){
                                            
                                            $j++;

                                            // check if the schedule subject of section is empty
                                            if(!isset($section_subject_schedule_save[$j])){
                                                $can_sched = true;
                                                $record_sched_start = $section_subject_schedule_save[$i]->STSTimeStart;      
                                                break 3;
                                            }// end of check if the schedule subject of section is empty
                                            else{
                                                if($end_time <= $section_subject_schedule_save[$j]->STSTimeStart){
                                                    $can_sched = true;
                                                    break 3;
                                                }
                                                else{
                                                    $can_sched = false;
                                                    break 1;
                                                }
                                            }
                                            
                                        }// end of check if the generated start time is available in the section schedule
                                        else{
                                            $can_sched = false;
                                            break 1;
                                        }
                                    }// end of loop of the subject and schedule that this section have                                                                
                                }

                            }
                            else{

                                // loop of the subject that is scheduled in the classroom generated
                                for($i = 0;$i < count($subject_tagged_schedule);$i++){
                                    $j = $i;

                                    // check if the generated start time is available
                                    if($start_time >= $subject_tagged_schedule[$i]->STSTimeEnd){

                                        $j++;

                                        // check if the subject schedule in generate classroom is empty
                                        if(!isset($subject_tagged_schedule[$j])){

                                            // getting all the section subject schedule that is active
                                            $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                            FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                            WHERE b.SectionID = "'.$section_id.'" AND 
                                                                            a.STSDay = "'.$sts_day.'" AND 
                                                                            b.STYear = "'.$request['STYear'].'" AND
                                                                            b.STStatus = "Active" AND 
                                                                            a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                            ');

                                            // check if the section subject schedule is empty
                                            if(empty($section_subject_schedule_save)){
                                                $can_sched = true;
                                                break 3;
                                            }// end of check if the section subject schedule is empty
                                            else{

                                                // loop of the section subject schedule
                                                for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                                    $jj = $ii;

                                                    // check if the generated start time is available
                                                    if($start_time >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                        
                                                        $jj++;

                                                        // check if the section subject schedule is empty
                                                        if(!isset($section_subject_schedule_save[$jj])){
                                                            $can_sched = true;
                                                            $record_sched_start = $section_subject_schedule_save[$ii]->STSTimeStart;      
                                                            break 4;
                                                        }// end of check if the section subject schedule is empty
                                                        else{
                                                            if($end_time <= $section_subject_schedule_save[$jj]->STSTimeStart){
                                                                $can_sched = true;
                                                                break 4;
                                                            }
                                                            else{
                                                                $can_sched = false;
                                                                break 2;
                                                            }
                                                        }
                                                        
                                                    }// end of check if the generated start time is available
                                                    else{
                                                        $can_sched = false;
                                                        break 2;
                                                    }
                                                }// end of loop of the section subject schedule                                                                
                                            }
                                        }// end of check if the subject schedule in generate classroom is empty
                                        else{
                                            if($end_time <= $subject_tagged_schedule[$j]->STSTimeStart){

                                                // getting all the section subject schedule that is active
                                                $section_subject_schedule_save = DB::select('SELECT a.STSDay,a.STSTimeStart,a.STSTimeEnd 
                                                                                FROM subject_tagging_schedules a INNER JOIN subject_taggings b ON a.STID = b.STID 
                                                                                WHERE b.SectionID = "'.$section_id.'" AND 
                                                                                a.STSDay = "'.$sts_day.'" AND 
                                                                                b.STYear = "'.$request['STYear'].'" AND
                                                                                b.STStatus = "Active" AND 
                                                                                a.STSStatus = "Active" ORDER BY a.STSTimeStart ASC
                                                                                ');

                                                // check if the section subject schedule is empty
                                                if(empty($section_subject_schedule_save)){
                                                    $can_sched = true;
                                                    break 3;
                                                }// end of check if the section subject schedule is empty
                                                else{

                                                    // loop of section subject schedule save 
                                                    for($ii = 0;$ii < count($section_subject_schedule_save);$ii++){
                                                        $jj = $ii;

                                                        // check if generate start time is available
                                                        if($start_time >= $section_subject_schedule_save[$ii]->STSTimeEnd){
                                                            
                                                            $jj++;
                                                            if(!isset($section_subject_schedule_save[$jj])){
                                                                $can_sched = true;
                                                                $record_sched_start = $section_subject_schedule_save[$ii]->STSTimeStart;      
                                                                break 4;
                                                            }
                                                            else{
                                                                if($end_time <= $section_subject_schedule_save[$jj]->STSTimeStart){
                                                                    $can_sched = true;
                                                                    break 4;
                                                                }
                                                                else{
                                                                    $can_sched = false;
                                                                    break 2;
                                                                }
                                                            }
                                                            
                                                        }// end of check if generate start time is available
                                                        else{
                                                            $can_sched = false;
                                                            break 2;
                                                        }
                                                    }// end of loop of section subject schedule save                                                                 
                                                }
                                            }
                                            else{
                                                $can_sched = false;
                                            }
                                        }
                                        
                                    }// end of check if the generated start time is available
                                    else{
                                        $can_sched = false;
                                    }
                                }// end loop of the subject that is scheduled in the classroom generated
                            }
                        }                       
                    }  // end of foreach loop of schedule time

                }// end of foreach loop of days


                // check if can sched the generated schedule & inserting the schedule to the database
                if($can_sched == true){
                    //return ["day" => $sts_day,"start_time"=>$start_time,"end_time"=>$end_time,"record_time_start"=>$record_sched_start,"classroom_id"=>$row_1->ClassroomID];

                    DB::insert('INSERT INTO subject_tagging_schedules (STID,SMID,ClassroomID,STSTimeStart,STSTimeEnd,STSDay,STSStatus,created_at,updated_at) VALUES
                    ("'.$subject_taggings_id.'","'.$row->SMID.'","'.$row_1->ClassroomID.'",
                    "'.$start_time.'","'.$end_time.'","'.$sts_day.'","Active",now(),now())');
                    break;
                }// check if can sched the generated schedule & end of inserting the schedule to the database   
                
                
            }// end of foreach loop for classroom available 

        }// end of foreach loop for subject meetings
         
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

        return DB::select('SELECT b.SubjectID,b.SubjectDescription FROM course_subject_offereds a 
                        INNER JOIN subjects b ON a.SubjectID = b.SubjectID
                        WHERE a.CourseID = "'.$course_id.'" AND a.CSOYear = "'.$year.'" AND a.CSOSem = "'.$sem.'"        
                ');
    }

    public function tagged_subject_sections($section_id,$sem,$year,$year_from,$year_to){
        return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,c.ProfessorName,GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
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
                            GROUP BY b.SubjectDescription,a.STID,c.ProfessorName
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
                            f.SectionName,c.ProfessorName,g.CourseCode,
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
                            GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode
                        ');
                        
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
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Subject Name</th>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Professor</th>
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
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Subject Name</th>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Professor</th>
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
}
