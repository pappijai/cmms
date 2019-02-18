<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\SubjectTagging;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            else{
                DB::update('
                UPDATE sections SET
                    SectionStatus = "Active"
                    WHERE md5(concat(SectionID)) = "'.$row->SectionID.'"                    
                ');
            }
        }

        return DB::select("SELECT md5(concat(a.SectionID)) SectionID, a.SectionName,a.SectionYear,a.CourseID,b.CourseDescription,b.CourseYears 
                            FROM sections a INNER JOIN courses b ON a.CourseID = b.CourseID WHERE a.SectionStatus = 'Active' ORDER BY b.CourseDescription ASC");
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
                        if($end_time > $row_1->ClassroomOut){
                            break 2;
                        }
                        else{
                            $sts_day = $row_2->DayName;
                            $start_time = $row_3->SchedTime;
                            $total_hours = $row->SubjectHours * (60*60);
                            $timestamps = strtotime($row_3->SchedTime) + $total_hours;
                            $end_time = date('H:i', $timestamps);


                            if(empty($subject_tagged_schedule)){

                            }
                            else{
                                for($i = 0;$i < count($subject_tagged_schedule);$i++){
                                    if($subject_tagged_schedule[$i]->STSTimeStart >= $start_time && 
                                    $subject_tagged_schedule[$i]->STSTimeStart <= $end_time &&
                                    $subject_tagged_schedule[$i]->STSTimeEnd >= $start_time){
                                        
                                        $can_sched = false;

                                    }
                                }
                            }
                        }                       
                    }  // end of foreach loop of schedule time

                }// end of foreach loop of days


                // inserting the schedule to the database
                if($can_sched == true){
                    // DB::insert('INSERT INTO subject_tagging_schedules (STID,SMID,ClassroomID,STSTimeStart,STSTimeEnd,STSDay,STSStatus,created_at,updated_at) VALUES
                    // ("'.$subject_taggings_id.'","'.$row->SMID.'","'.$row_1->ClassroomID.'",
                    // "'.$start_time.'","'.$end_time.'","'.$sts_day.'","Active",now(),now())');

                    // break;
                }   
                
                
            }// end of foreach loop for classroom available 

        }// end of foreach loop for subject meetings
    
        

        foreach($subject_meetings as $row){
            // $classroom_available = DB::select('SELECT * FROM classrooms WHERE ClassroomType = "'.$row->CTID.'"');
            // shuffle($classroom_available);

            // foreach($classroom_available as $row_1){
                
            //     $sts_day = '';
            //     $start_time = '';
            //     $total_hours = 0;
            //     $timestamps = '';
            //     $end_time = '';
            //     $record_start_time = '';
            //     $record_end_time = '';
            //     $can_sched = false;                 
                
            //     shuffle($days);
            //     foreach($days as $row_2){

            //         // getting all the tagged schedule where the day is same day in the loop and same the classroom ID
            //         $subject_tagged_schedule = DB::select('SELECT * FROM subject_tagging_schedules WHERE 
            //         ClassroomID = "'.$row_1->ClassroomID.'" AND 
            //         STSStatus = "Active" AND
            //         STSDay = "'.$row_2->DayName.'" ORDER BY STSTimeStart ASC');
                    

            //         foreach($schedule_time as $row_3){
            //             if($end_time > $row_1->ClassroomOut){
            //                 break 2;
            //             }
            //             else{
                            
            //                 // if the existing schedule is not in the classroom given then the given day and time will be save
            //                 if(empty($subject_tagged_schedule)){
            //                     $sts_day = $row_2->DayName;
            //                     $start_time = $row_3->SchedTime;
            //                     $total_hours = $row->SubjectHours * (60*60);
            //                     $timestamps = strtotime($row_3->SchedTime) + $total_hours;
            //                     $end_time = date('H:i', $timestamps);
            //                     $record_start_time = '';
            //                     $record_end_time = '';

            //                     $section_subject_schedule_save = DB::select('SELECT b.STSDay,b.STSTimeStart,b.STSTimeEnd 
            //                     FROM subject_taggings a INNER JOIN subject_tagging_schedules b ON a.STID = b.STID 
            //                     WHERE a.SectionID = "'.$section_id.'" ORDER BY b.STSTimeStart
            //                     ');

            //                     foreach($section_subject_schedule_save as $row_5){
            //                         if($row_5->STSTimeStart >= $start_time && 
            //                         $row_5->STSTimeStart <= $end_time &&
            //                         $row_5->STSTimeEnd >= $start_time && 
            //                         $row_5->STSTimeStart <= $end_time &&
            //                         $sts_day == $row_5->STSDay){
            //                             $can_sched = false;

            //                         }
            //                         else{
            //                             $can_sched = true;
            //                             break 3;
            //                         }
            //                     }
                                
            //                 }
            //                 else{
                                
            //                     // saving the given day and time
            //                     $sts_day = $row_2->DayName;
            //                     $start_time = $row_3->SchedTime;
            //                     $total_hours = $row->SubjectHours * (60*60);
            //                     $timestamps = strtotime($row_3->SchedTime) + $total_hours;
            //                     $end_time = date('H:i', $timestamps);
            //                     $record_start_time = '';
            //                     $record_end_time = '';
                                
            //                     // check if the generated time and day is available
            //                     for($i = 0; $i < count($subject_tagged_schedule);$i++){
            //                         $j = $i + 1;
            //                         if($subject_tagged_schedule[$i]->STSTimeStart >= $start_time && 
            //                         $subject_tagged_schedule[$i]->STSTimeStart <= $end_time &&
            //                         $subject_tagged_schedule[$i]->STSTimeEnd >= $start_time && 
            //                         $subject_tagged_schedule[$i]->STSTimeStart <= $end_time){
            //                             $can_sched = false; 
            //                             break; 
            //                         }
            //                         else{
            //                             if($subject_tagged_schedule[$j]->STSTimeStart >= $start_time && 
            //                             $subject_tagged_schedule[$j]->STSTimeStart <= $end_time &&
            //                             $subject_tagged_schedule[$j]->STSTimeEnd >= $start_time && 
            //                             $subject_tagged_schedule[$j]->STSTimeStart <= $end_time){
            //                                 $can_sched = false;
            //                                 break;
            //                             }
            //                             else{
            //                                 $section_subject_schedule_save = DB::select('SELECT b.STSDay,b.STSTimeStart,b.STSTimeEnd 
            //                                                                 FROM subject_taggings a INNER JOIN subject_tagging_schedules b ON a.STID = b.STID 
            //                                                                 WHERE a.SectionID = "'.$section_id.'" ORDER BY b.STSTimeStart
            //                                                                 ');
            //                                 foreach($section_subject_schedule_save as $row_5){
            //                                     if($row_5->STSTimeStart >= $start_time && 
            //                                     $row_5->STSTimeStart <= $end_time &&
            //                                     $row_5->STSTimeEnd >= $start_time && 
            //                                     $row_5->STSTimeStart <= $end_time &&
            //                                     $sts_day == $row_5->STSDay){
            //                                         $can_sched = false;
            //                                         break 2;
            //                                     }
            //                                     else{
            //                                         $can_sched = true;
            //                                     }
            //                                 }
                                            
            //                             }
            //                         }
            //                     }
                                
            //                     // foreach($subject_tagged_schedule as $row_4){

            //                     //     if($row_4->STSTimeStart >= $start_time && 
            //                     //         $row_4->STSTimeStart <= $end_time &&
            //                     //         $row_4->STSTimeEnd >= $start_time && 
            //                     //         $row_4->STSTimeStart <= $end_time){
                                        
            //                     //         $can_sched = false;  
            //                     //     }
            //                     //     else{
                                        
            //                     //         $section_subject_schedule_save = DB::select('SELECT b.STSDay,b.STSTimeStart,b.STSTimeEnd 
            //                     //                                         FROM subject_taggings a INNER JOIN subject_tagging_schedules b ON a.STID = b.STID 
            //                     //                                         WHERE a.SectionID = "'.$section_id.'"
            //                     //                                         ');
            //                     //         foreach($section_subject_schedule_save as $row_5){
            //                     //             if($row_5->STSTimeStart >= $start_time && 
            //                     //             $row_5->STSTimeStart <= $end_time &&
            //                     //             $row_5->STSTimeEnd >= $start_time && 
            //                     //             $row_5->STSTimeStart <= $end_time &&
            //                     //             $sts_day == $row_5->STSDay){
            //                     //                 $can_sched = false;
            //                     //             }
            //                     //         }

            //                     //         $can_sched = true;
            //                     //         $record_start_time = $row_4->STSTimeStart;
            //                     //         $record_end_time = $row_4->STSTimeEnd;
                                        
            //                     //     }
            //                     // }
    
            //                 }

            //             }                        


            //         }                    
            //     }

            //     if($can_sched == true){
            //         DB::insert('INSERT INTO subject_tagging_schedules (STID,SMID,ClassroomID,STSTimeStart,STSTimeEnd,STSDay,STSStatus,created_at,updated_at) VALUES
            //         ("'.$subject_taggings_id.'","'.$row->SMID.'","'.$row_1->ClassroomID.'",
            //         "'.$start_time.'","'.$end_time.'","'.$sts_day.'","Active",now(),now())');

            //         break;

            //         // return [
            //         //     "day"=> $sts_day,
            //         //     "record_start_time"=> $record_start_time,
            //         //     "record_end_time"=> $record_end_time,
            //         //     "start_time"=> $start_time,
            //         //     "end_time"=> $end_time,
            //         //     "ClassroomID"=> $row_1->ClassroomID,
            //         // ];
            //         // break 2;

            //     }
                
            // }
            
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
    
}
