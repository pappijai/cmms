<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Floorplan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Image;
use Pdf;

class FloorplanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = DB::select('SELECT FloorplanID,FloorplanName,FloorplanPhoto FROM floorplans LIMIT 1');
        return [
            "FloorplanID"=> $data[0]->FloorplanID,
            "FloorplanName"=> $data[0]->FloorplanName,
            "FloorplanPhoto"=> $data[0]->FloorplanPhoto
        ];

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

        $this->validate($request, [
            'FloorplanName' => 'required|string|max:50',
        ]);

        $data = DB::select('SELECT * FROM floorplans WHERE FloorplanID = "'.$request->FloorplanID.'"');

        $currentPhoto = $data[0]->FloorplanPhoto;
        if($request->FloorplanPhoto != $currentPhoto){
            $name = time().'.'.explode('/', explode(':',substr($request->FloorplanPhoto, 0,strpos
            ($request->FloorplanPhoto, ';')))[1])[1];

            Image::make($request->FloorplanPhoto)->save(public_path('/img/floorplan/').$name);
            $request->merge(['FloorplanPhoto' => $name]);

            $userPhoto = public_path('img/floorplan/').$currentPhoto;

            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }
        }    

        DB::update('UPDATE floorplans SET
                    FloorplanName = "'.$request['FloorplanName'].'",
                    FloorplanPhoto = "'.$request['FloorplanPhoto'].'"
                    WHERE FloorplanID = "'.$request['FloorplanID'].'"
        ');
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

    public function get_floorplan(){
        return DB::select('SELECT FloorplanID,FloorplanName,FloorplanPhoto FROM floorplans LIMIT 1');
    }

    public function get_floors($id){
        return DB::select('SELECT a.*,b.* FROM floors a INNER JOIN buildings b ON a.BldgID = b.BldgID WHERE md5(concat(a.BldgID)) = "'.$id.'"');
    }

    public function get_floors_coordinates($id){
        return DB::select('SELECT a.*,b.* FROM floors a INNER JOIN classrooms b ON a.BFID = b.ClassroomFloor WHERE a.BFID = "'.$id.'"');
    }

    public function get_classroom_schedules($id){
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

        $classroom_schedule = $this->get_classroom_schedules($id);
        $output = '';

        if(empty($classroom_schedule)){
            $output .= '
                <title>Room '.$classroom[0]->ClassroomCode.' Schedules</title>
                <h3 align="center";>'.$classroom[0]->ClassroomCode.' Room Schedules</h3>
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
                <title>Room '.$classroom[0]->ClassroomCode.' Schedules</title>
                <h3 align="center";>'.$classroom[0]->ClassroomCode.' Room  Schedules</h3>
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

}
