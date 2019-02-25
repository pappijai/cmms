<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Floorplan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Image;

class FloorplanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = DB::select('SELECT FloorplanID,FloorplanName,FloorplanPhoto FROM floorplans LIMIT 1');
        return [
            "FloorplanID"=> $data[0]->FloorplanID,
            "FloorplanName"=> $data[0]->FloorplanName,
            "FloorplanPhoto"=> $data[0]->FloorplanPhoto
        ];

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

        $this->validate($request, [
            'FloorplanName' => 'required|string|max:50',
        ]);

        $data = DB::select('SELECT * FROM floorplans WHERE FloorplanID = "'.$request->FloorplanID.'"');

        $currentPhoto = $data[0]->FloorplanPhoto;
        if($request->FloorplanPhoto != $currentPhoto){
            $name = time().'.'.explode('/', explode(':',substr($request->FloorplanPhoto, 0,strpos
            ($request->FloorplanPhoto, ';')))[1])[1];

            Image::make($request->FloorplanPhoto)->save(public_path('/img/floorplan/').$name);
            $request->merge(['FloorplanPhoto' => $name]);

            $userPhoto = public_path('img/floorplan/').$currentPhoto;

            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }
        }    

        DB::update('UPDATE floorplans SET
                    FloorplanName = "'.$request['FloorplanName'].'",
                    FloorplanPhoto = "'.$request['FloorplanPhoto'].'"
                    WHERE FloorplanID = "'.$request['FloorplanID'].'"
        ');
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
    }

    public function get_floorplan(){
        return DB::select('SELECT FloorplanID,FloorplanName,FloorplanPhoto FROM floorplans LIMIT 1');
    }

    public function get_floors($id){
        return DB::select('SELECT a.*,b.* FROM floors a INNER JOIN buildings b ON a.BldgID = b.BldgID WHERE md5(concat(a.BldgID)) = "'.$id.'"');
    }

    public function get_floors_coordinates($id){
        return DB::select('SELECT a.*,b.* FROM floors a INNER JOIN classrooms b ON a.BFID = b.ClassroomFloor WHERE a.BFID = "'.$id.'"');
    }

}
