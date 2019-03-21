<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Floorplan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Image;
use Pdf;

class FloorplanController extends Controller
{

    public function index()
    {
        $floorplan = DB::select('SELECT FloorplanID,FloorplanName,FloorplanPhoto FROM floorplans LIMIT 1');
        $building = DB::select('SELECT md5(BldgID) BldgID,BldgName,BldgCoordinates,created_at FROM buildings ORDER BY BldgName ASC');

        $data['floorplan'] = $floorplan;
        $data['building'] = $building;
        return view('view_floorplan')->with('data', $data);
    }

    public function get_floors($id){
        $floors = DB::select('SELECT a.*,b.* FROM floors a INNER JOIN buildings b ON a.BldgID = b.BldgID WHERE md5(concat(a.BldgID)) = "'.$id.'"');
        $bldgName = $floors[0]->BldgName;
        $data['floors'] = $floors;
        $data['bldgName'] = $bldgName;
        $data['bldgid'] = $id;
        return view('get_floors')->with('data', $data);
    }

    public function get_floor_classroom($id,$floor_name,$floor_photo,$bldgid,$bldgname){
        $floor_coordinates =  DB::select('SELECT a.*,b.* FROM floors a INNER JOIN classrooms b ON a.BFID = b.ClassroomFloor WHERE a.BFID = "'.$id.'"');    
        $data['floor_coordinates'] = $floor_coordinates;  
        $data['floor_name'] = $floor_name;    
        $data['floor_photo'] = $floor_photo;   
        $data['bldgid'] = $bldgid;   
        $data['bldgname'] = $bldgname;   
        return view('get_floors_classrooms')->with('data', $data);
 
    }

    public function get_floors_classrooms_schedule($id,$floor_id,$floor_name,$floor_photo,$bldgid,$bldgname,$classroom_name){
        $year_today = date('Y');
        $month_today = date('m');
        $schedule = DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,c.ProfessorName,f.SectionName,
                        f.SectionYear,g.CourseCode,d.STSDay,CONCAT(d.STSTimeStart," - ",d.STSTimeEnd) AS Schedule,
                        e.ClassroomCode,e.ClassroomName
                        FROM subject_taggings a 
                        INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                        INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                        INNER JOIN subject_tagging_schedules d ON a.STID = d.STID
                        INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                        INNER JOIN sections f ON f.SectionID = a.SectionID
                        INNER JOIN courses g ON g.CourseID = f.CourseID
                        WHERE e.ClassroomID = "'.$id.'" AND
                        a.STStatus = "Active"
                        ORDER BY d.STSTimeStart ASC
                    ');

        $data['schedule'] = $schedule;
        $data['floor_id'] = $floor_id;    
        $data['classroom_id'] = $id;    
        $data['classroom_name'] = $classroom_name;    
        $data['floor_name'] = $floor_name;    
        $data['floor_photo'] = $floor_photo;   
        $data['bldgid'] = $bldgid;   
        $data['bldgname'] = $bldgname;  
        $data['year_today'] = $year_today;  
        $data['month_today'] = $month_today;  
        return view('get_floors_classrooms_schedule')->with('data', $data);
    }

    public function print_schedule($id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_schedule_table_to_html($id));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('schedule.pdf');        
    }

    public function convert_schedule_table_to_html($id){
        $year_today = date('Y');
        $month_today = date('m');

        $classroom = DB::select('SELECT * FROM classrooms WHERE ClassroomID = "'.$id.'"');        

        $classroom_schedule = $this->get_classroom_schedule($id);
        $output = '';

        if(empty($classroom_schedule)){
            $output .= '
                <title>Room '.$classroom[0]->ClassroomName.' Schedules</title>
                <h3 align="center";>'.$classroom[0]->ClassroomName.' Room Schedules</h3>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Monday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Tuesday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Wednesday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Thursday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Friday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Saturday</th>
                        </tr>                        
                    </thead>
                    <tbody>
            ';
            
            $output .= '
                    </tbody>
                </table>
            ';
        }
        else{
            $output .= '
                <title>Room '.$classroom[0]->ClassroomName.' Schedules</title>
                <h3 align="center";>'.$classroom[0]->ClassroomName.' Room  Schedules</h3>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Monday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Tuesday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Wednesday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Thursday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Friday</th>
                            <th style="border: 1px solid;padding: 12px;width:16.66%;">Saturday</th>
                        </tr>                        
                    </thead>
                    <tbody>
            ';
            foreach($classroom_schedule as $row){
                $output .= '
                    <tr>';
    
    /////////////////////////Monday////////////////////////////
                $output .= '
                        <td style="border: 1px solid;padding: 5px;">
                ';
                        if($month_today >= 5 && $month_today <= 9 && $row->STSDay == 'Monday'){
                            $section_year = $year_today - $row->SectionYear + 1;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        elseif($row->STSDay == 'Monday'){
                            $section_year = $year_today - $row->SectionYear;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        else{
                            
                        }
                $output .='
                        </td>
                ';
    
                
    /////////////////////////Tuesday////////////////////////////
                $output .= '
                        <td style="border: 1px solid;padding: 5px;">
                ';
                        if($month_today >= 5 && $month_today <= 9 && $row->STSDay == 'Tuesday'){
                            $section_year = $year_today - $row->SectionYear + 1;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        elseif($row->STSDay == 'Tuesday'){
                            $section_year = $year_today - $row->SectionYear;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        else{
                            
                        }
                $output .='
                        </td>
                ';
    
                
    /////////////////////////Wednesday////////////////////////////
                $output .= '
                        <td style="border: 1px solid;padding: 5px;">
                ';
                        if($month_today >= 5 && $month_today <= 9 && $row->STSDay == 'Wednesday'){
                            $section_year = $year_today - $row->SectionYear + 1;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        elseif($row->STSDay == 'Wednesday'){
                            $section_year = $year_today - $row->SectionYear;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        else{
                            
                        }
                $output .='
                        </td>
                ';
    
    /////////////////////////Thursday////////////////////////////
                $output .= '
                        <td style="border: 1px solid;padding: 5px;">
                ';
                        if($month_today >= 5 && $month_today <= 9 && $row->STSDay == 'Thursday'){
                            $section_year = $year_today - $row->SectionYear + 1;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        elseif($row->STSDay == 'Thursday'){
                            $section_year = $year_today - $row->SectionYear;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        else{
                            
                        }
                $output .='
                        </td>
                ';
    
    /////////////////////////Friday////////////////////////////
                $output .= '
                        <td style="border: 1px solid;padding: 5px;">
                ';
                        if($month_today >= 5 && $month_today <= 9 && $row->STSDay == 'Friday'){
                            $section_year = $year_today - $row->SectionYear + 1;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        elseif($row->STSDay == 'Friday'){
                            $section_year = $year_today - $row->SectionYear;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        else{
                            
                        }
                $output .='
                        </td>
                ';
    
    /////////////////////////Saturday////////////////////////////
                $output .= '
                        <td style="border: 1px solid;padding: 5px;">
                ';
                        if($month_today >= 5 && $month_today <= 9 && $row->STSDay == 'Saturday'){
                            $section_year = $year_today - $row->SectionYear + 1;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        elseif($row->STSDay == 'Saturday'){
                            $section_year = $year_today - $row->SectionYear;
                            $output .= $row->SubjectDescription.'<br/>';
                            $output .= $row->Schedule.'<br/>';
                            $output .= $row->ProfessorName.'<br/>';
                            $output .= $row->CourseCode.' '.$section_year.' - '.$row->SectionName.'<br/>';
                        }
                        else{
                            
                        }
                $output .='
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

    public function get_classroom_schedule($id){
        return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,c.ProfessorName,f.SectionName,
                        f.SectionYear,g.CourseCode,d.STSDay,CONCAT(d.STSTimeStart," - ",d.STSTimeEnd) AS Schedule,
                        e.ClassroomCode
                        FROM subject_taggings a 
                        INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                        INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                        INNER JOIN subject_tagging_schedules d ON a.STID = d.STID
                        INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                        INNER JOIN sections f ON f.SectionID = a.SectionID
                        INNER JOIN courses g ON g.CourseID = f.CourseID
                        WHERE e.ClassroomID = "'.$id.'" AND
                        a.STStatus = "Active"
                        ORDER BY d.STSTimeStart ASC
                ');
    }

    public function view_schedule(){
        $year_today = date('Y');
        $month_today = date('m');
        $sem = '';
        $year_from = '';
        $year_to = '';

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

        $data['sections'] = DB::select("SELECT md5(concat(a.SectionID)) SectionID, a.SectionName,a.SectionYear,a.CourseID,b.CourseDescription,b.CourseYears,b.CourseCode
                            FROM sections a INNER JOIN courses b ON a.CourseID = b.CourseID WHERE a.SectionStatus = 'Active' ORDER BY b.CourseDescription, a.SectionYear ASC");
       
        $data['month_today'] = $month_today;
        $data['year_today'] = $year_today;
        $data['sem'] = $sem;
        $data['year_from'] = $year_from;
        $data['year_to'] = $year_to;
        return view('view_schedule')->with('data', $data);
    }

    public function get_schedule($section_id,$sem,$year,$year_from,$year_to){
        $subjects = DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,c.ProfessorName,GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
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
        $output = '';

        $output.= $id;

        echo json_encode($output);
    }
}
