<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Floor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;
use Image;

class FloorController extends Controller
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
        //
        return DB::select('SELECT md5(a.BFID) BFID,a.BldgID,a.BFName,a.BFPhoto,a.created_at,b.BldgName 
                          FROM floors a INNER JOIN buildings b ON a.BldgID = b.BldgID 
                          ORDER BY b.BldgName ASC, a.BFName ASC');
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
        $id = $request->BldgID;
        $this->validate($request, [
            'BldgID' => 'required|integer',
            'BFName' => 'required|unique:floors,BFName,NULL,id,BldgID,'.$id.'',
            'BFPhoto' => 'required',
        ]);        

        $name = time().'.'.explode('/', explode(':',substr($request->BFPhoto, 0,strpos
        ($request->BFPhoto, ';')))[1])[1];

        Image::make($request->BFPhoto)->save(public_path('/img/floorplan/').$name);
        $request->merge(['BFPhoto' => $name]);

        $floors = DB::insert('
            INSERT INTO floors (BldgID,BFName,BFPhoto,created_at,updated_at) VALUES
            ("'.$request['BldgID'].'","'.$request['BFName'].'","'.$request['BFPhoto'].'",now(),now())
            
        ');
        if ($floors){
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
        $floors = DB::select('SELECT * FROM floors WHERE md5(concat(BFID)) = "'.$id.'"');
        $floors_id = $floors['0']->BFID;
        $bldg_id = $request->BldgID;

        $this->validate($request, [
            'BldgID' => 'required|integer',
            'BFName' => 'required|unique:floors,BFName,'.$floors_id.',BFID,BldgID,'.$bldg_id.'',
        ]);   

        $currentPhoto = $floors[0]->BFPhoto;
        if($request->BFPhoto != $currentPhoto){
            $name = time().'.'.explode('/', explode(':',substr($request->BFPhoto, 0,strpos
            ($request->BFPhoto, ';')))[1])[1];

            Image::make($request->BFPhoto)->save(public_path('/img/floorplan/').$name);
            $request->merge(['BFPhoto' => $name]);

            $userPhoto = public_path('img/floorplan/').$currentPhoto;

            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }
        }    

        $floors = DB::update('
            UPDATE floors SET
                BldgID = "'.$request->BldgID.'",
                BFName = "'.$request->BFName.'",
                BFPhoto = "'.$request->BFPhoto.'",
                updated_at = now()
                WHERE md5(concat(BFID)) = "'.$id.'"
                
        ');

        if($floors){
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
        $floors = DB::select('SELECT * FROM floors WHERE md5(concat(BFID)) = "'.$id.'"');

        $userPhoto = public_path('img/floorplan/').$floors[0]->BFPhoto;

        if(file_exists($userPhoto)){
            @unlink($userPhoto);
        }

        $query = DB::delete('DELETE FROM floors WHERE md5(concat(BFID)) = "'.$id.'"');
        if($query){
            return ["message" => "User Deleted"];
        }
        else{
            return ["message" => "Error"];
        }        
    }

    public function buildingInfo(){
        return DB::select('SELECT * FROM buildings');
    }
}
