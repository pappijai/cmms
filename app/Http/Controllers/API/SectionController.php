<?php

namespace App\Http\Controllers\APi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SectionController extends Controller
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
        return $sections;
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
        $year_today = date("Y");

        $this->validate($request, [            
            'SectionName' => 'required|string',
            'SectionYear' => 'required|integer|min:1900|max:'.$year_today,
            'CourseID' => 'required|integer',
        ]); 

        $section = DB::insert('
            INSERT INTO sections (SectionName,SectionYear,CourseID,created_at,updated_at) VALUES
                                    ("'.$request['SectionName'].'","'.$request['SectionYear'].'",
                                    "'.$request['CourseID'].'",now(),now())
            
        ');
        if ($section){
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
        $section = DB::select('SELECT * FROM sections WHERE md5(concat(SectionID)) = "'.$id.'"');
        $section_id = $section['0']->SectionID;
        $year_today = date("Y");

        $this->validate($request, [            
            'SectionName' => 'required|string|unique:sections,SectionName,'.$section_id.',SectionID,SectionYear,'.$request->SectionYear.',CourseID,'.$request->CourseID,
            'SectionYear' => 'required|integer|min:1900|max:'.$year_today,
            'CourseID' => 'required|integer',
        ]);     
        
        $section = DB::update('
            UPDATE sections SET
                SectionName = "'.$request->SectionName.'",
                SectionYear = "'.$request->SectionYear.'",
                CourseID = "'.$request->CourseID.'",
                updated_at = now()
                WHERE md5(concat(SectionID)) = "'.$id.'"
                
        ');

        if($section){
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
        // $query = DB::delete('DELETE FROM sections WHERE md5(concat(SectionID)) = "'.$id.'"');
        // if($query){
        //     return ["message" => "Course Deleted"];
        // }
        // else{
        //     return ["message" => "Error"];
        // }   
    }

    public function courses(){
        return DB::select("SELECT * FROM courses ORDER BY CourseDescription ASC");
    }
}
