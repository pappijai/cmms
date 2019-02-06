<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return DB::select("SELECT md5(concat(CourseID)) CourseID, CourseCode, CourseDescription,CourseYears, created_at 
                        FROM courses ORDER BY CourseCode ASC");
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
            'CourseCode' => 'required|string|unique:courses,CourseCode',
            'CourseDescription' => 'required|string|unique:courses,CourseDescription',
            'CourseYears' => 'required|integer|min:1|max:5',
        ]); 

        $course = DB::insert('
            INSERT INTO courses (CourseCode,CourseDescription,CourseYears,created_at,updated_at) VALUES
                                    ("'.$request['CourseCode'].'","'.$request['CourseDescription'].'",
                                    "'.$request['CourseYears'].'",now(),now())
            
        ');
        if ($course){
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
        $course = DB::select('SELECT * FROM courses WHERE md5(concat(CourseID)) = "'.$id.'"');
        $course_id = $course['0']->CourseID;
        
        $this->validate($request, [            
            'CourseCode' => 'required|string|unique:courses,CourseCode,'.$course_id.',CourseID',
            'CourseDescription' => 'required|string|unique:courses,CourseDescription,'.$course_id.',CourseID',
            'CourseYears' => 'required|integer|min:1|max:5'
        ]); 

        $courses = DB::update('
            UPDATE courses SET
                CourseCode = "'.$request->CourseCode.'",
                CourseDescription = "'.$request->CourseDescription.'",
                CourseYears = "'.$request->CourseYears.'",
                updated_at = now()
                WHERE md5(concat(CourseID)) = "'.$id.'"
                
        ');

        if($courses){
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
        $query = DB::delete('DELETE FROM courses WHERE md5(concat(CourseID)) = "'.$id.'"');
        if($query){
            return ["message" => "Course Deleted"];
        }
        else{
            return ["message" => "Error"];
        }         
    }
}
