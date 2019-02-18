<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Classroom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return DB::select('SELECT concat(md5(a.ClassroomID)) ClassroomID, a.ClassroomCode, 
                        a.ClassroomName, b.CTID,b.CTName, a.ClassroomIn, a.ClassroomOut, c.BldgID,c.BldgName, 
                        d.BFID,d.BFName, a.created_at 
                        FROM classrooms a INNER JOIN classroom_types b ON a.ClassroomType = CTID 
                        INNER JOIN buildings c ON a.ClassroomBldg = c.BldgID 
                        INNER JOIN floors d ON a.ClassroomFloor = d.BFID ORDER BY a.ClassroomCode ASC');
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
            'CTID' => 'required|integer',
            'BldgID' => 'required|integer',
            'BFID' => 'required|integer',
            'ClassroomName' => 'required|string|unique:classrooms,ClassroomName,Null,id,ClassroomBldg,'.$request->BldgID.',ClassroomFloor,'.$request->BFID.'',
            'ClassroomCode' => 'required|string|unique:classrooms,ClassroomCode,Null,id,ClassroomBldg,'.$request->BldgID.',ClassroomFloor,'.$request->BFID.'',            
            'ClassroomIn' => 'required|date_format:H:i',
            'ClassroomOut' => 'required|date_format:H:i|after:ClassroomIn',
        ]);        

        $classrooms = DB::insert('
            INSERT INTO classrooms (ClassroomCode,ClassroomName,ClassroomType,ClassroomIn,
                                    ClassroomOut,ClassroomBldg,ClassroomFloor,created_at,updated_at) VALUES
                                    ("'.$request['ClassroomCode'].'","'.$request['ClassroomName'].'",
                                    "'.$request['CTID'].'","'.$request['ClassroomIn'].'",
                                    "'.$request['ClassroomOut'].'","'.$request['BldgID'].'",
                                    "'.$request['BFID'].'",now(),now())
            
        ');
        if ($classrooms){
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
        $classroom = DB::select('SELECT * FROM classrooms WHERE md5(concat(ClassroomID)) = "'.$id.'"');
        $classroom_id = $classroom['0']->ClassroomID;

        $this->validate($request, [            
            'CTID' => 'required|integer',
            'BldgID' => 'required|integer',
            'BFID' => 'required|integer',
            'ClassroomName' => 'required|string|unique:classrooms,ClassroomName,'.$classroom_id.',ClassroomID,ClassroomBldg,'.$request->BldgID.',ClassroomFloor,'.$request->BFID.'',
            'ClassroomCode' => 'required|string|unique:classrooms,ClassroomCode,'.$classroom_id.',ClassroomID,ClassroomBldg,'.$request->BldgID.',ClassroomFloor,'.$request->BFID.'',            
            'ClassroomIn' => 'required',
            'ClassroomOut' => 'required|after:ClassroomIn',
        ]);   

        $classrooms = DB::update('
            UPDATE classrooms SET
                ClassroomCode = "'.$request->ClassroomCode.'",
                ClassroomName = "'.$request->ClassroomName.'",
                ClassroomType = "'.$request->CTID.'",
                ClassroomIn = "'.$request->ClassroomIn.'",
                ClassroomOut = "'.$request->ClassroomOut.'",
                ClassroomBldg = "'.$request->BldgID.'",
                ClassroomFloor = "'.$request->BFID.'",
                updated_at = now()
                WHERE md5(concat(ClassroomID)) = "'.$id.'"
                
        ');

        if($classrooms){
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

        $query = DB::delete('DELETE FROM classrooms WHERE md5(concat(ClassroomID)) = "'.$id.'"');
        if($query){
            return ["message" => "User Deleted"];
        }
        else{
            return ["message" => "Error"];
        }        
    }

    public function classroomTypeInfo(){
        return DB::select('SELECT * FROM classroom_types');
    }

    public function floorsInfo($id){
        return DB::select('SELECT * FROM floors WHERE BldgID = '.$id);
    }
}
