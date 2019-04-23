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
                         created_at FROM subjects ORDER BY created_at DESC');
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
        ]);   
        
        $subjects = DB::insert('
            INSERT INTO subjects (SubjectCode,SubjectDescription,created_at,updated_at) VALUES
                                    ("'.$request['SubjectCode'].'","'.$request['SubjectDescription'].'",now(),now())
            
        ');
        
        // $subject_id = DB::getPdo()->lastInsertId();

        // for($i = 1;$i <= $request->SubjectMeetings;$i++){
        //     DB::insert('
        //         INSERT INTO subject_meetings (SubjectID,CTID,created_at,updated_at) VALUES ("'.$subject_id.'","",now(),now())
        //     ');
        // }
        // $data = [
        //     'subject_id' => md5($subject_id)
        // ];

        return "okay";       
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
        ]); 
        
        $subjects = DB::update('
            UPDATE subjects SET
                SubjectCode = "'.$request->SubjectCode.'",
                SubjectDescription = "'.$request->SubjectDescription.'",
                updated_at = now()
                WHERE md5(concat(SubjectID)) = "'.$id.'"
                
        ');

        // $count_sub_meetings = count(DB::select('SELECT * FROM subject_meetings WHERE SubjectID = '.$subjects_id.''));
        // if($request->SubjectMeetings > $count_sub_meetings){
        //     DB::insert('
        //         INSERT INTO subject_meetings (SubjectID,CTID,created_at,updated_at) VALUES ("'.$subjects_id.'","",now(),now())
        //     ');
        // }
        // elseif($request->SubjectMeetings < $count_sub_meetings){
        //    DB::delete('DELETE FROM subject_meetings WHERE md5(concat(SubjectID)) = "'.$id.'" LIMIT 1');
        // }
        // else{
        //     //none
        // }

        // $data = [
        //     'subject_id' => md5($subjects_id)
        // ]; 

        return "okay";         
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

        // $query = DB::delete('DELETE FROM subjects WHERE md5(concat(SubjectID)) = "'.$id.'"');
        // DB::delete('DELETE FROM subject_meetings WHERE md5(concat(SubjectID)) = "'.$id.'"');
        
        // if($query){
        //     return ["message" => "User Deleted"];
        // }
        // else{
        //     return ["message" => "Error"];
        // }              

        $count_subject = DB::select('SELECT * FROM course_subject_offereds where md5(concat(SubjectID)) = "'.$id.'"');

        if(!empty($count_subject)){
            return ["type"=>"error","message"=>"This Subject has sub record"];
        }
        else{
            
            $query = DB::delete('DELETE FROM subjects WHERE md5(concat(SubjectID)) = "'.$id.'"');
            // DB::delete('DELETE FROM subject_meetings WHERE md5(concat(SubjectID)) = "'.$id.'"');
            if($query){
                return ["type"=>"success","message"=>"Subject Deleted Successfully"];
            }
            else{
                return ["type"=>"error","message"=>"Error Deleting"];
            }
            
        }        
    }

    // selecting all subject meetings per subject
    public function getsubjectmeetings($id){
        return DB::select('SELECT md5(concat(SMID)) SMID, SubjectID, CTID, SubjectHours FROM subject_meetings WHERE md5(concat(SubjectID)) = "'.$id.'"');
    }

    // update function for subject meetings #1
    public function updatesubjectmeetings1(Request $request, $id){
        $this->validate($request, [            
            'CTID' => 'required|integer',
            'SubjectHours' => 'required|integer',
        ]);       

        $subject_meetings = DB::update('
        UPDATE subject_meetings SET
            CTID = "'.$request->CTID.'",
            SubjectHours = "'.$request->SubjectHours.'",
            updated_at = now()
            WHERE md5(concat(SMID)) = "'.$id.'"
            
        ');
        
    }

    // update function for subject meetings #2
    public function updatesubjectmeetings2(Request $request, $id){
        $this->validate($request, [            
            'CTID' => 'required|integer',
            'SubjectHours' => 'required|integer',
        ]); 

        $subject_meetings = DB::update('
        UPDATE subject_meetings SET
            CTID = "'.$request->CTID.'",
            SubjectHours = "'.$request->SubjectHours.'",
            updated_at = now()
            WHERE md5(concat(SMID)) = "'.$id.'"
            
        ');        
    }

    public function subjectsforcourse(){
        return DB::select('SELECT SubjectID,SubjectDescription,SubjectCode,
                        SubjectMeetings,created_at FROM subjects ORDER BY SubjectDescription ASC');
    }
}
