<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Image;
use Log;
use Artisan;
use Alert;
use Response;
use Exception;
use Carbon\Carbon;

class UserController extends Controller
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

        // returning users information
        if(\Gate::allows('isSuperAdmin')){
            $id = auth('api')->user()->id;
            return DB::select('SELECT md5(id) id,name,email,type,photo,created_at FROM users WHERE id <> "'.$id.'" ORDER BY name ASC');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Gate::allows('isSuperAdmin')){
            // checking user inputs
            $this->validate($request, [
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:50|unique:users',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6|same:password'
            ]);
            
            // inserting data to database
            $users = DB::insert('
                INSERT INTO users (name,email,password,type,photo,created_at,updated_at) VALUES
                ("'.$request['name'].'","'.$request['email'].'","'.Hash::make($request['password']).'",
                "'.$request['type'].'","people.png",now(),now())
            
            ');
            if ($users){
                return 'good';
            }
            else{
                return 'failed';
            }
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
        if(\Gate::allows('isSuperAdmin')){
            
            // getting the id that not encrypted
            $user = DB::select('SELECT * FROM users WHERE md5(concat(id)) = "'.$id.'"');
            $user_id = $user['0']->id;
    
            // validating inputs
            $this->validate($request, [
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:50|unique:users,email,'.$user_id,
                'password' => 'sometimes|string|min:6',
                'password_confirmation' => 'sometimes|string|min:6|same:password'
            ]);
            
            // checking if the password is empty
            if($request->password == ""){
                $users = DB::update('
                   UPDATE users SET
                   name = "'.$request->name.'",
                   email = "'.$request->email.'",
                   type = "'.$request->type.'",
                   updated_at = now()
                   WHERE md5(concat(id)) = "'.$id.'"
                
                ');
            }
            else{
                $password = Hash::make($request->password);
        
                $users = DB::update('
                   UPDATE users SET
                   name = "'.$request->name.'",
                   email = "'.$request->email.'",
                   password = "'.$password.'",
                   type = "'.$request->type.'",
                   photo = "'.$request->photo.'",
                   updated_at = now()
                   WHERE md5(concat(id)) = "'.$id.'"
                
                ');
    
            }
    
            //$user->update($request->all());
            
            return ["message" => "Updated successfuly"];        
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
        // deleting user
        if(\Gate::allows('isSuperAdmin')){
            
            $query = DB::delete('DELETE FROM users WHERE md5(concat(id)) = "'.$id.'"');
            if($query){
                return ["message" => "User Deleted"];
            }
            else{
                return ["message" => "Error"];
            }
        }
    }

    public function get_count_data($table){
        return count(DB::select('SELECT * FROM '.$table));
    }

    public function get_count_section_for_tagging(){
        return count(DB::select("SELECT md5(concat(a.SectionID)) SectionID, a.SectionName,a.SectionYear,a.CourseID,b.CourseDescription,b.CourseYears 
                            FROM sections a INNER JOIN courses b ON a.CourseID = b.CourseID WHERE a.SectionStatus = 'Active' ORDER BY b.CourseDescription, a.SectionYear ASC"));
    }

    public function get_profile(){
        return auth('api')->user();
    }
    
    public function update_profile(Request $request, $id){
        $user = auth('api')->user();

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:6'
        ]);

        $currentPhoto = $user->photo;
        if($request->photo != $currentPhoto){
            $name = time().'.'.explode('/', explode(':',substr($request->photo, 0,strpos
            ($request->photo, ';')))[1])[1];
            
            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/profile/').$currentPhoto;

            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }
        }
        else{
            $request->merge(['photo' => $currentPhoto]);

        }


        if($request->password == ""){
            $users = DB::update('
               UPDATE users SET
               name = "'.$request->name.'",
               email = "'.$request->email.'",
               photo = "'.$request->photo.'",
               updated_at = now()
               WHERE id = "'.$id.'"
            
            ');
        }
        else{
            $password = Hash::make($request->password);
    
            $users = DB::update('
               UPDATE users SET
               name = "'.$request->name.'",
               email = "'.$request->email.'",
               password = "'.$password.'",
               photo = "'.$request->photo.'",
               updated_at = now()
               WHERE id = "'.$id.'"
            
            ');

        }

        return ["message" => "Success"];
    }

    public function get_backups(){
        if (!count(config('backup.backup.destination.disks'))) {
            dd(trans('backpack::backup.no_disks_configured'));
        }
        $backups = [];
        foreach (config('backup.backup.destination.disks') as $disk_name) {
            $disk = Storage::disk($disk_name);
            $adapter = $disk->getDriver()->getAdapter();
            $files = $disk->allFiles();
            // make an array of backup files, with their filesize and creation date
            foreach ($files as $k => $f) {
                // only take the zip files into account
                if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                    $backups[] = [
                        'file_path'     => $f,
                        'file_name'     => str_replace(config('app.name').'/', '', $f),
                        'file_size'     => $disk->size($f),
                        'last_modified' => $this->get_date($disk->lastModified($f)),
                        'disk'          => $disk_name,
                        'download'      => ($adapter instanceof local) ? true : false,
                        ];
                }
            }
        }
        // reverse the backups, so the newest one would be on top
        $backups = array_reverse($backups);
        return $backups; 
    }

    public function get_date($date){
        return Carbon::createFromTimeStamp($date)->formatLocalized('%d %B %Y %H:%M');
    }

    public function create_backup(){
        try {
            // start the backup process
            Artisan::call('backup:run',['--only-db'=>true]);
            $output = Artisan::output();
            // log the results
            Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n" . $output);
            // return the results as a response to the ajax call
            Alert::success('New backup created');
            return 'good';
        } catch (Exception $e) {
            //Flash::error($e->getMessage());
            return 'error';
        }
    }

    public function download_backup(Request $request)
    //public function download_backup($file_name)
    {
        // $file = config('app.name') . '/' . $request->file_name;
        // $file_two = $request->file_name;
        // $disk = Storage::disk(config('app.destination.disks')[0]);
        // $storage_path = $disk->getDriver()->getAdapter()->getPathPrefix();
        // if ($disk->exists($file)) {
        //     $fs = Storage::disk(config('app.destination.disks')[0])->getDriver();
        //     $stream = $fs->readStream($file);
        //     return \Response::stream(function () use ($stream) {
        //         fpassthru($stream);
        //     }, 200, [
        //         "Content-Type" => $fs->getMimetype($file),
        //         "Content-Length" => $fs->getSize($file),
        //         "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
        //     ]);
        // } else {
        //     if ($disk->exists($file_two)) {
        //         $fs = Storage::disk(config('destination.disks')[0])->getDriver();
        //         $stream = $fs->readStream($file_two);
                
        //         return \Response::stream(function () use ($stream) {
        //             fpassthru($stream);
        //         }, 200, [
        //             "Content-Type" => $fs->getMimetype($file_two),
        //             "Content-Length" => $fs->getSize($file_two),
        //             "Content-disposition" => "attachment; filename=\"" . basename($file_two) . "\"",
        //         ]);
        //     }
        //     else{
        //         abort(404, "The backup file doesn't exist.");
        //         return 'error';

        //     }
        // }
        $file = config('app.name') . '/' . $request->file_name;
        $disk = Storage::disk(config('app.destination.disks')[0]);
        if ($disk->exists($file)) {
            $fs = Storage::disk(config('app.destination.disks')[0])->getDriver();
            $stream = $fs->readStream($file);
                 
            return \Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $fs->getMimetype($file),
                "Content-Length" => $fs->getSize($file),
                "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
            ]);
        } else {
            abort(404, "The backup file doesn't exist.");
        }

    }

    public function delete_backup(Request $request)
    {
        $disk = Storage::disk(config('app.destination.disks')[0]);
        $disk_two = Storage::disk(config('destination.disks')[0]);
        if ($disk->exists(config('app.name') . '/' . $request->file_name)) {
            $disk->delete(config('app.name') . '/' . $request->file_name);
            return 'success';
        } else {
            if($disk_two->exists($request->file_name)){
                $disk->delete($request->file_name);
                return 'success';
            }
            else{
                abort(404, "The backup file doesn't exist.");
            }
        }
    }

    public function trylang(){
        return "hey";
    }

}
