<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'year_from' => 'required|digits:4|integer|min:1900|max:'.(date('Y')),
            'semester' => 'required',
            'SectionID' => 'required',
        ]); 

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

    public function get_report($year_from, $semester, $section_id){
        if($semester == 'All'){
            if($section_id == 'All'){
                return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,g.CourseYears,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STYearFrom = "'.$year_from.'"
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem,g.CourseYears
                                ORDER BY g.CourseCode,f.SectionYear ASC
                        ');
            }
            else{

                return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,g.CourseYears,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STYearFrom = "'.$year_from.'" AND
                                md5(concat(a.SectionID)) = "'.$section_id.'"
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem,g.CourseYears
                                ORDER BY g.CourseCode,f.SectionYear ASC
                        ');
            }
        }
        else{
            if($section_id == 'All'){
                return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,g.CourseYears,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STSem = "'.$semester.'" AND
                                a.STYearFrom = "'.$year_from.'" AND
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem,g.CourseYears
                                ORDER BY g.CourseCode,f.SectionYear ASC
                            ');
            }
            else{
                return DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,g.CourseYears,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STSem = "'.$semester.'" AND
                                a.STYearFrom = "'.$year_from.'" AND
                                md5(concat(a.SectionID)) = "'.$section_id.'"
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem,g.CourseYears
                                ORDER BY g.CourseCode,f.SectionYear ASC
                            ');
            }
        }
    }

    public function print_report($year_from, $semester, $section_id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_report_table_to_html($year_from, $semester, $section_id));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('professor_schedule.pdf');    
    }

    public function convert_report_table_to_html($year_from, $semester, $section_id){
        $year_today = date('Y');
        $month_today = date('m');  
        $output = '';
        $year_to = $year_from + 1;

        if($semester == 'All'){
            if($section_id == 'All'){
                $data = DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STYearFrom = "'.$year_from.'"
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem
                                ORDER BY g.CourseCode,f.SectionYear ASC
                        ');
            }
            else{

                $data = DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STYearFrom = "'.$year_from.'" AND
                                md5(concat(a.SectionID)) = "'.$section_id.'"
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem
                                ORDER BY g.CourseCode,f.SectionYear ASC
                        ');
            }
        }
        else{
            if($section_id == 'All'){
                $data = DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STSem = "'.$semester.'" AND
                                a.STYearFrom = "'.$year_from.'" AND
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem
                                ORDER BY g.CourseCode,f.SectionYear ASC
                            ');
            }
            else{
                $data = DB::select('SELECT md5(concat(a.STID)) STID, b.SubjectDescription,f.SectionYear,a.STYearFrom,a.STYearTo,a.STSem,
                                f.SectionName,c.ProfessorName,g.CourseCode,
                                GROUP_CONCAT(d.STSDay," - ",e.ClassroomCode," - ",d.STSTimeStart," - ",d.STSTimeEnd SEPARATOR " | ") AS Schedule 
                                FROM subject_taggings a 
                                INNER JOIN subjects b ON a.SubjectID = b.SubjectID 
                                INNER JOIN professors c ON a.ProfessorID = c.ProfessorID
                                LEFT JOIN subject_tagging_schedules d ON a.STID = d.STID
                                INNER JOIN classrooms e ON d.ClassroomID = e.ClassroomID
                                INNER JOIN sections f ON a.SectionID = f.SectionID
                                INNER JOIN courses g ON f.CourseID = g.CourseID
                                WHERE a.STSem = "'.$semester.'" AND
                                a.STYearFrom = "'.$year_from.'" AND
                                md5(concat(a.SectionID)) = "'.$section_id.'"
                                GROUP BY b.SubjectDescription,a.STID,c.ProfessorName,f.SectionYear,f.SectionName,g.CourseCode,a.STYearFrom,a.STYearTo,a.STSem
                                ORDER BY g.CourseCode,f.SectionYear ASC
                            ');
            }
        }


        if(empty($data)){
            $output .= '
                <h3 align="center";>SY '.$year_from.' - '.$year_to.' '.$semester.'</h3>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            
                            <th style="border: 1px solid;padding: 12px;">Semester</th>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Subject</th>
                            <th style="border: 1px solid;padding: 12px;">Section</th>
                            <th style="border: 1px solid;padding: 12px;width:18%;">Professor</th>
                            <th style="border: 1px solid;padding: 12px;width:30%;">Schedule</th>
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
                <h3 align="center";>SY '.$year_from.' - '.$year_to.' '.$semester.'</h3>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>
                            
                            <th style="border: 1px solid;padding: 12px;">Semester</th>
                            <th style="border: 1px solid;padding: 12px;width:25%;">Subject</th>
                            <th style="border: 1px solid;padding: 12px;">Section</th>
                            <th style="border: 1px solid;padding: 12px;width:18%;">Professor</th>
                            <th style="border: 1px solid;padding: 12px;width:30%;">Schedule</th>
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
                        <td style="border: 1px solid;padding: 5px;">'.$row->STSem.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->SubjectDescription.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->CourseCode.' '.$section_name.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->ProfessorName.'</td>
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
    
    public function get_classrooms($bfid){
        return DB::select('SELECT a.ClassroomID, a.ClassroomCode, 
                        a.ClassroomName, b.CTID,b.CTName, a.ClassroomIn, a.ClassroomOut,a.ClassroomCoordinates, c.BldgID,c.BldgName, 
                        d.BFID,d.BFName, a.created_at 
                        FROM classrooms a INNER JOIN classroom_types b ON a.ClassroomType = CTID 
                        INNER JOIN buildings c ON a.ClassroomBldg = c.BldgID 
                        INNER JOIN floors d ON a.ClassroomFloor = d.BFID 
                        WHERE a.ClassroomFloor = "'.$bfid.'"
                        ORDER BY a.ClassroomCode ASC');
    }

    public function classroom_report(Request $request){
        $this->validate($request, [            
            'BldgID' => 'required',
            'BFID' => 'required',
            'Day' => 'required',
            'TimeIn' => 'required',
        ]); 

        if($request->TimeIn != 'ALL'){
            $this->validate($request, [            
                'TimeOut' => 'required'
            ]);
        }
    }

    public function get_classroom_report($bldg_id,$bfid,$day,$time_in,$time_out){
        if($bldg_id == 'All'){
            $classrooms = DB::select('SELECT b.BldgName,c.BFName,a.ClassroomName,a.ClassroomID,a.ClassroomCode
                            FROM classrooms a INNER JOIN buildings b ON a.ClassroomBldg = b.BldgID
                            INNER JOIN floors c ON a.ClassroomFloor = c.BFID
                            ORDER BY b.BldgName,c.BFName,a.ClassroomName ASC
            ');

            if($time_in == "ALL"){
                $schedules = DB::select('SELECT * FROM subject_tagging_schedules WHERE STSStatus = "Active"');
            }
            else{
                $schedules = DB::select('SELECT * FROM subject_tagging_schedules 
                                            WHERE STSTimeStart >= "'.$time_in.'" AND 
                                            STSTimeStart <= "'.$time_out.'" AND
                                            STSTimeStart <> "'.$time_out.'" AND
                                            STSStatus = "Active"');
            }

            $reports = array();

            foreach($classrooms as $row){
                $no_sched = true;
                foreach($schedules as $row_1){
                    if($row->ClassroomID == $row_1->ClassroomID && $row_1->STSDay == $day){
                        $no_sched = false;
                        break;
                    }
                }

                if($no_sched == true){
                    $reports[] = $row;
                }
            }

            return $reports;

            
        }
        else{
            if($bfid == 'All'){
                $classrooms = DB::select('SELECT b.BldgName,c.BFName,a.ClassroomName,a.ClassroomID,a.ClassroomCode
                            FROM classrooms a INNER JOIN buildings b ON a.ClassroomBldg = b.BldgID
                            INNER JOIN floors c ON a.ClassroomFloor = c.BFID
                            WHERE md5(concat(a.ClassroomBldg)) = "'.$bldg_id.'"
                            ORDER BY b.BldgName,c.BFName,a.ClassroomName ASC
                ');

                if($time_in == "ALL"){
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules WHERE STSStatus = "Active"');
                }
                else{
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules 
                                                WHERE STSTimeStart >= "'.$time_in.'" AND 
                                                STSTimeStart <= "'.$time_out.'" AND
                                                STSTimeStart <> "'.$time_out.'" AND
                                                STSStatus = "Active"');
                }
    

                $reports = array();

                foreach($classrooms as $row){
                    $no_sched = true;
                    foreach($schedules as $row_1){
                        if($row->ClassroomID == $row_1->ClassroomID && $row_1->STSDay == $day){
                            $no_sched = false;
                            break;
                        }
                    }
    
                    if($no_sched == true){
                        $reports[] = $row;
                    }
                }
    
                return $reports;
            }
            else{
                $classrooms = DB::select('SELECT b.BldgName,c.BFName,a.ClassroomName,a.ClassroomID,a.ClassroomCode
                            FROM classrooms a INNER JOIN buildings b ON a.ClassroomBldg = b.BldgID
                            INNER JOIN floors c ON a.ClassroomFloor = c.BFID
                            WHERE md5(concat(a.ClassroomBldg)) = "'.$bldg_id.'" AND
                            a.ClassroomFloor = "'.$bfid.'"
                            ORDER BY b.BldgName,c.BFName,a.ClassroomName ASC
                ');

                if($time_in == "ALL"){
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules WHERE STSStatus = "Active"');
                }
                else{
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules 
                                                WHERE STSTimeStart >= "'.$time_in.'" AND 
                                                STSTimeStart <= "'.$time_out.'" AND
                                                STSTimeStart <> "'.$time_out.'" AND
                                                STSStatus = "Active"');
                }
    

                $reports = array();

                foreach($classrooms as $row){
                    $no_sched = true;
                    foreach($schedules as $row_1){
                        if($row->ClassroomID == $row_1->ClassroomID && $row_1->STSDay == $day){
                            $no_sched = false;
                            break;
                        }
                    }
    
                    if($no_sched == true){
                        $reports[] = $row;
                    }
                }
    
                return $reports;
            }
        }
    }

    public function print_classroom_report($bldg_id,$bfid,$day,$time_in,$time_out){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($this->convert_classroom_report_table_to_html($bldg_id,$bfid,$day,$time_in,$time_out));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('professor_schedule.pdf');  
    }

    public function convert_classroom_report_table_to_html($bldg_id,$bfid,$day,$time_in,$time_out){
        $output = '';

        if($bldg_id == 'All'){
            $classrooms = DB::select('SELECT b.BldgName,c.BFName,a.ClassroomName,a.ClassroomID,a.ClassroomCode
                            FROM classrooms a INNER JOIN buildings b ON a.ClassroomBldg = b.BldgID
                            INNER JOIN floors c ON a.ClassroomFloor = c.BFID
                            ORDER BY b.BldgName,c.BFName,a.ClassroomName ASC
            ');

            if($time_in == "ALL"){
                $schedules = DB::select('SELECT * FROM subject_tagging_schedules WHERE STSStatus = "Active"');
            }
            else{
                $schedules = DB::select('SELECT * FROM subject_tagging_schedules 
                                            WHERE STSTimeStart >= "'.$time_in.'" AND 
                                            STSTimeStart <= "'.$time_out.'" AND
                                            STSTimeStart <> "'.$time_out.'" AND
                                            STSStatus = "Active"');
            }
            
            $reports = array();

            foreach($classrooms as $row){
                $no_sched = true;
                foreach($schedules as $row_1){
                    if($row->ClassroomID == $row_1->ClassroomID && $row_1->STSDay == $day){
                        $no_sched = false;
                        break;
                    }
                }

                if($no_sched == true){
                    $reports[] = $row;
                }
            }

            
        }
        else{
            if($bfid == 'All'){
                $classrooms = DB::select('SELECT b.BldgName,c.BFName,a.ClassroomName,a.ClassroomID,a.ClassroomCode
                            FROM classrooms a INNER JOIN buildings b ON a.ClassroomBldg = b.BldgID
                            INNER JOIN floors c ON a.ClassroomFloor = c.BFID
                            WHERE md5(concat(a.ClassroomBldg)) = "'.$bldg_id.'"
                            ORDER BY b.BldgName,c.BFName,a.ClassroomName ASC
                ');

                if($time_in == "ALL"){
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules WHERE STSStatus = "Active"');
                }
                else{
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules 
                                                WHERE STSTimeStart >= "'.$time_in.'" AND 
                                                STSTimeStart <= "'.$time_out.'" AND
                                                STSTimeStart <> "'.$time_out.'" AND
                                                STSStatus = "Active"');
                }

                $reports = array();

                foreach($classrooms as $row){
                    $no_sched = true;
                    foreach($schedules as $row_1){
                        if($row->ClassroomID == $row_1->ClassroomID && $row_1->STSDay == $day){
                            $no_sched = false;
                            break;
                        }
                    }
    
                    if($no_sched == true){
                        $reports[] = $row;
                    }
                }
    
            }
            else{
                $classrooms = DB::select('SELECT b.BldgName,c.BFName,a.ClassroomName,a.ClassroomID,a.ClassroomCode
                            FROM classrooms a INNER JOIN buildings b ON a.ClassroomBldg = b.BldgID
                            INNER JOIN floors c ON a.ClassroomFloor = c.BFID
                            WHERE md5(concat(a.ClassroomBldg)) = "'.$bldg_id.'" AND
                            a.ClassroomFloor = "'.$bfid.'"
                            ORDER BY b.BldgName,c.BFName,a.ClassroomName ASC
                ');

                if($time_in == "ALL"){
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules WHERE STSStatus = "Active"');
                }
                else{
                    $schedules = DB::select('SELECT * FROM subject_tagging_schedules 
                                                WHERE STSTimeStart >= "'.$time_in.'" AND 
                                                STSTimeStart <= "'.$time_out.'" AND
                                                STSTimeStart <> "'.$time_out.'" AND
                                                STSStatus = "Active"');
                }

                $reports = array();

                foreach($classrooms as $row){
                    $no_sched = true;
                    foreach($schedules as $row_1){
                        if($row->ClassroomID == $row_1->ClassroomID && $row_1->STSDay == $day){
                            $no_sched = false;
                            break;
                        }
                    }
    
                    if($no_sched == true){
                        $reports[] = $row;
                    }
                }
    
            }
        }

        if(empty($reports)){
            $output .= '
                <h3 align="center">Available Classrooms</h3>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>                            
                            <th style="border: 1px solid;padding: 12px;">Day</th>
                            <th style="border: 1px solid;padding: 12px;">Building</th>
                            <th style="border: 1px solid;padding: 12px;">Floor</th>
                            <th style="border: 1px solid;padding: 12px;">Classroom Code</th>
                            <th style="border: 1px solid;padding: 12px;">Classroom Name</th>
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

                <h3 align="center">Available Classrooms</h3>
                <table width="100%" style="border-collapse: collapse; border 0px;">
                    <thead style="background-color:black;color: #fff;">
                        <tr>                            
                            <th style="border: 1px solid;padding: 12px;">Day</th>
                            <th style="border: 1px solid;padding: 12px;">Building</th>
                            <th style="border: 1px solid;padding: 12px;">Floor</th>
                            <th style="border: 1px solid;padding: 12px;">Classroom Code</th>
                            <th style="border: 1px solid;padding: 12px;">Classroom Name</th>
                        </tr>                        
                    </thead>
                    <tbody>
            ';

            foreach($reports as $row){
                $output.= '
                    <tr>
                        <td style="border: 1px solid;padding: 5px;">'.$day.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->BldgName.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->BFName.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->ClassroomCode.'</td>
                        <td style="border: 1px solid;padding: 5px;">'.$row->ClassroomName.'</td>
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
