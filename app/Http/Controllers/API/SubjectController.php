<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return DB::select('SELECT concat(md5(SubjectID)) SubjectID,SubjectDescription,SubjectCode,
                        SubjectMeetings,created_at FROM subjects ORDER BY SubjectCode ASC');
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
            'SubjectCode' => 'required|string|unique:subjects,SubjectCode',
            'SubjectDescription' => 'required|string|unique:subjects,SubjectDescription',
            'SubjectMeetings' => 'required|integer|min:1|max:2',
        ]);   
        
        $subjects = DB::insert('
            INSERT INTO subjects (SubjectCode,SubjectDescription,SubjectMeetings,created_at,updated_at) VALUES
                                    ("'.$request['SubjectCode'].'","'.$request['SubjectDescription'].'",
                                    "'.$request['SubjectMeetings'].'",now(),now())
            
        ');
        
        $subject_id = DB::getPdo()->lastInsertId();

        for($i = 1;$i <= $request->SubjectMeetings;$i++){
            DB::insert('
                INSERT INTO subject_meetings (SubjectID,created_at,updated_at) VALUES ("'.$subject_id.'",now(),now())
            ');
        }
        
        if ($subjects){
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
        $subject = DB::select('SELECT * FROM subjects WHERE md5(concat(SubjectID)) = "'.$id.'"');
        $subjects_id = $subject['0']->SubjectID;        

        $this->validate($request, [            
            'SubjectCode' => 'required|string|unique:subjects,SubjectCode,'.$subjects_id.',SubjectID',
            'SubjectDescription' => 'required|string|unique:subjects,SubjectDescription,'.$subjects_id.',SubjectID',
            'SubjectMeetings' => 'required|integer|min:1|max:2',
        ]); 
        
        $subjects = DB::update('
            UPDATE subjects SET
                SubjectCode = "'.$request->SubjectCode.'",
                SubjectDescription = "'.$request->SubjectDescription.'",
                SubjectMeetings = "'.$request->SubjectMeetings.'",
                updated_at = now()
                WHERE md5(concat(SubjectID)) = "'.$id.'"
                
        ');

        $count_sub_meetings = count(DB::select('SELECT * FROM subject_meetings WHERE SubjectID = '.$subjects_id.''));
        if($request->SubjectMeetings > $count_sub_meetings){
            DB::insert('
                INSERT INTO subject_meetings (SubjectID,created_at,updated_at) VALUES ("'.$subjects_id.'",now(),now())
            ');
        }
        elseif($request->SubjectMeetings < $count_sub_meetings){
            DB::delete('DELETE FROM subject_meetings WHERE md5(concat(SubjectID)) = "'.$id.'" LIMIT 1');
        }
        else{
            //none
        }

        if($subjects){
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

        $query = DB::delete('DELETE FROM subjects WHERE md5(concat(SubjectID)) = "'.$id.'"');
        DB::delete('DELETE FROM subject_meetings WHERE md5(concat(SubjectID)) = "'.$id.'"');
        
        if($query){
            return ["message" => "User Deleted"];
        }
        else{
            return ["message" => "Error"];
        }              
    }
}