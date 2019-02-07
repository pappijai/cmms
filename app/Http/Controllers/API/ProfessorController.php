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
        $query = DB::delete('DELETE FROM professors WHERE md5(concat(ProfessorID)) = "'.$id.'"');
        if($query){
            return ["message" => "Course Deleted"];
        }
        else{
            return ["message" => "Error"];
        }            
    }
}
