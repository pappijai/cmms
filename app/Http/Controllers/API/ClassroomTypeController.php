<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\ClassroomType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClassroomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return DB::select('SELECT md5(CTID) CTID,CTName,created_at 
                         FROM classroom_types ORDER BY CTName ASC');
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
            'CTName' => 'required|string|unique:classroom_types',
        ]);
        
        $classroomtypes = DB::insert('
            INSERT INTO classroom_types (CTName,created_at,updated_at) VALUES
            ("'.$request['CTName'].'",now(),now())
            
        ');
        if ($classroomtypes){
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

        $classroom_types = DB::select('SELECT * FROM classroom_types WHERE md5(concat(CTID)) = "'.$id.'"');
        $classroom_types_id = $classroom_types['0']->CTID;

        $this->validate($request, [
            'CTName' => 'required|string|unique:classroom_types,CTName,'.$classroom_types_id.',CTID'
        ]);

        $classroom_types = DB::update('
            UPDATE classroom_types SET
                CTName = "'.$request->CTName.'",
                updated_at = now()
                WHERE md5(concat(CTID)) = "'.$id.'"
                
        ');

        if($classroom_types){
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
        $query = DB::delete('DELETE FROM classroom_types WHERE md5(concat(CTID)) = "'.$id.'"');
        if($query){
            return ["message" => "User Deleted"];
        }
        else{
            return ["message" => "Error"];
        }
    }
}
