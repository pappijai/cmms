<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Professor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfessorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return DB::select('SELECT md5(concat(ProfessorID)) ProfessorID,ProfessorName,created_at FROM professors ORDER BY ProfessorName ASC');
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
        $this->validate($request, [            
            'ProfessorName' => 'required|string|unique:professors,ProfessorName',
        ]); 

        $professor = DB::insert('
            INSERT INTO professors (ProfessorName,created_at,updated_at) VALUES
                                    ("'.$request['ProfessorName'].'",now(),now())
            
        ');
        if ($professor){
            return 'good';
        }
        else{
            return 'failed';
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
        $professor = DB::select('SELECT * FROM professors WHERE md5(concat(ProfessorID)) = "'.$id.'"');
        $professor_id = $professor['0']->ProfessorID;


        $this->validate($request, [            
            'ProfessorName' => 'required|string|unique:professors,ProfessorName,'.$professor_id.',ProfessorID',
        ]); 

        $professor = DB::update('
            UPDATE professors SET
                ProfessorName = "'.$request->ProfessorName.'",
                updated_at = now()
                WHERE md5(concat(ProfessorID)) = "'.$id.'"
                
        ');        

        if($professor){
            return ["message" => "Updated Successfully"];
        }
        else{
            return ["message" => "Error"];
        }   
        
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
        // $query = DB::delete('DELETE FROM professors WHERE md5(concat(ProfessorID)) = "'.$id.'"');
        // if($query){
        //     return ["message" => "Course Deleted"];
        // }
        // else{
        //     return ["message" => "Error"];
        // }   

        $count_professor = DB::select('SELECT * FROM subject_taggings where md5(concat(ProfessorID)) = "'.$id.'"');

        if(!empty($count_professor)){
            return ["type"=>"error","message"=>"This Professor has sub record"];
        }
        else{
            
            $query = DB::delete('DELETE FROM professors WHERE md5(concat(ProfessorID)) = "'.$id.'"');
            if($query){
                return ["type"=>"success","message"=>"Classroom Deleted Successfully"];
            }
            else{
                return ["type"=>"error","message"=>"Error Deleting"];
            }
            
        }         
    }

    public function get_professor_schedule($professor_id){
        return DB::select('SELECT f.SectionName,f.SectionYear,i.CourseCode,b.SubjectDescription,GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                            FROM subject_taggings a 
                            INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                            LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                            INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                            INNER JOIN sections f ON a.SectionID = f.SectionID
                            INNER JOIN courses i ON f.CourseID = i.CourseID
                            WHERE md5(concat(a.ProfessorID)) = "'.$professor_id.'" AND 
                            a.STStatus = "Active"
                            GROUP BY b.SubjectDescription,i.CourseCode,f.SectionName,f.SectionYear
                            ORDER BY i.CourseCode,f.SectionYear ASC
                        ');

    }

    public function print_professor_schedule($professor_id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_schedule_table_to_html($professor_id));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('professor_schedule.pdf');          
    }
    
    public function convert_schedule_table_to_html($professor_id){
        $year_today = date('Y');
        $month_today = date('m');  
        $output = '';

        $professor = DB::select('SELECT * FROM professors WHERE md5(concat(ProfessorID)) = "'.$professor_id.'"');

        $data = DB::select('SELECT f.SectionName,f.SectionYear,i.CourseCode,b.SubjectDescription,GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                            FROM subject_taggings a 
                            INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                            LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                            INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                            INNER JOIN sections f ON a.SectionID = f.SectionID
                            INNER JOIN courses i ON f.CourseID = i.CourseID
                            WHERE md5(concat(a.ProfessorID)) = "'.$professor_id.'" AND 
                            a.STStatus = "Active"
                            GROUP BY b.SubjectDescription,i.CourseCode,f.SectionName,f.SectionYear
                            ORDER BY i.CourseCode,f.SectionYear ASC
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


        if(empty($data)){
            $output .= '
                <h3 align="center";>'.$professor[0]->ProfessorName.' Schedules</h3>
                <h5 align="center";>'.$sem.'  SY '.$year_from.' - '.$year_to.'</h5>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Course Yr & Sec</th>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Subject Name</th>
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
                <h3 align="center";>'.$professor[0]->ProfessorName.' Schedules</h3>
                <h5 align="center";>'.$sem.'  SY '.$year_from.' - '.$year_to.'</h5>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Course Yr & Sec</th>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Subject Name</th>
                            <th style="border: 1px solid;padding: 12px;width:50%;">Schedule</th>
                        </tr>                        
                    </thead>
                    <tbody>
            ';

            foreach($data as $row){
                if($month_today >=6 && $month_today <=10 ){
                    $year = $year_today - $row->SectionYear + 1;
                    $section_name = $year.' - '.$row->SectionName;
                }
                else{
                    $year = $year_today - $row->SectionYear;
                    $section_name = $year.' - '.$row->SectionName;
                }   

                $output.= '
                    <tr>
                        <td style="border: 1px solid;padding: 5px;">'.$row->CourseCode.' '.$section_name.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->SubjectDescription.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->Schedule.'</td>
                    </tr>
                ';
            }

            $output .='
                    </tbody>
                </table>
            ';

        }

        return $output;                   
    }
}
