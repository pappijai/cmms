<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Building;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BuildingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // returning building infor

        return DB::select('SELECT md5(BldgID) BldgID,BldgName,created_at FROM buildings ORDER BY BldgName ASC');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Checking inputs
        $this->validate($request, [
            'BldgName' => 'required|max:50|unique:buildings'
        ]);

        // inserting data to database
        $buildings = DB::insert('
            INSERT INTO buildings (BldgName,created_at,updated_at) VALUES
            ("'.$request['BldgName'].'",now(),now())
            
        ');
        if ($buildings){
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
        // getting the id that not encrypted
        $building = DB::select('SELECT * FROM buildings WHERE md5(concat(BldgID)) = "'.$id.'"');
        $building_id = $building['0']->BldgID;

        //
        $this->validate($request, [
            'BldgName' => 'required|max:50|unique:buildings,BldgName,'.$building_id.',BldgID'
        ]);

        $buildings = DB::update('
            UPDATE buildings SET
                BldgName = "'.$request->BldgName.'",
                updated_at = now()
                WHERE md5(concat(BldgID)) = "'.$id.'"
                
        ');

        if($buildings){
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
        $query = DB::delete('DELETE FROM buildings WHERE md5(concat(BldgID)) = "'.$id.'"');
        if($query){
            return ["message" => "User Deleted"];
        }
        else{
            return ["message" => "Error"];
        }
        
    }
}
