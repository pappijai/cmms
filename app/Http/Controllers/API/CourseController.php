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
        $course_year = $course['0']->CourseYears;

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

        if($request->CourseYears < $course_year ){
            for($i = $request->CourseYears+1;$i < $course_year+1;$i++){
                DB::delete('DELETE FROM course_subject_offereds 
                            WHERE md5(concat(CourseID)) = "'.$id.'" AND CSOYear = "'.$i.'"');
            }

        }

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
        DB::delete('DELETE FROM course_subject_offereds WHERE md5(concat(CourseID)) = "'.$id.'"');
        if($query){
            return ["message" => "Course Deleted"];
        }
        else{
            return ["message" => "Error"];
        }         
    }

    // Store a Subjects per Course
    public function create_course_subject_first(Request $request){
        //
        $course = DB::select('SELECT * FROM courses WHERE md5(concat(CourseID)) = "'.$request->CourseID.'"');
        $course_id = $course['0']->CourseID;

        $this->validate($request, [            
            'SubjectID' => 'required|integer|
                            unique:course_subject_offereds,SubjectID,Null,id,CourseID,'.$course_id.',CSOYear,'.$request->CSOYear.',CSOSem,"'.$request->CSOSem.'"',
        ]); 
        
        $data = count(DB::select('SELECT * FROM course_subject_offereds
                        WHERE CourseID = "'.$course_id.'" AND CSOYear = "'.$request['CSOYear'].'" AND CSOSem = "'.$request['CSOSem'].'"
        
        '));
        if($data < 9){
            $course_subject_offereds = DB::insert('
                INSERT INTO course_subject_offereds (SubjectID,CourseID,CSOYear,CSOSem,created_at,updated_at) VALUES
                                        ("'.$request['SubjectID'].'","'.$course_id.'","'.$request['CSOYear'].'",
                                        "'.$request['CSOSem'].'",now(),now())
                
            ');
    
            if ($course_subject_offereds){
                return 'good';
            }
            else{
                return 'failed';
            }

        }
        else{
            return ["error_message" => "9 Subjects Only Per Semester"];
        }


    }

    public function courses_subjects_per_year_sem($course_id,$year,$sem){

        return DB::select('SELECT md5(concat(a.CSOID)) CSOID,b.SubjectDescription FROM course_subject_offereds a 
                        INNER JOIN subjects b ON a.SubjectID = b.SubjectID
                        WHERE md5(concat(a.CourseID)) = "'.$course_id.'" AND a.CSOYear = "'.$year.'" AND a.CSOSem = "'.$sem.'"        
                ');
    }

    public function delete_course_subject($id){
       //
       $query = DB::delete('DELETE FROM course_subject_offereds WHERE md5(concat(CSOID)) = "'.$id.'"');
       if($query){
           return ["message" => "Course Subject Deleted"];
       }
       else{
           return ["message" => "Error"];
       } 
    }
}
