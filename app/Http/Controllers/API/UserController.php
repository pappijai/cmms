<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
Use Image;

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
        //
        if(\Gate::allows('isAdmin')){
            return DB::select('SELECT md5(id) id,name,email,type,photo,created_at FROM users ORDER BY name ASC');
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
        //

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password'
        ]);
        
        $users = DB::insert('
            INSERT INTO users (name,email,password,type,photo,created_at,updated_at) VALUES
            ("'.$request['name'].'","'.$request['email'].'","'.Hash::make($request['password']).'",
            "'.$request['type'].'","'.$request['photo'].'",now(),now())
        
        ');
        if ($users){
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
        $user = DB::select('SELECT * FROM users WHERE md5(concat(id)) = "'.$id.'"');
        $user_id = $user['0']->id;

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users,email,'.$user_id,
            'password' => 'sometimes|string|min:6',
            'password_confirmation' => 'sometimes|string|min:6|same:password'
        ]);
        
        if($request->password == ""){
            $users = DB::update('
               UPDATE users SET
               name = "'.$request->name.'",
               email = "'.$request->email.'",
               type = "'.$request->type.'",
               photo = "'.$request->photo.'",
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // deleting user
        if(\Gate::allows('isAdmin')){
            $query = DB::delete('DELETE FROM users WHERE md5(concat(id)) = "'.$id.'"');
            if($query){
                return ["message" => "User Deleted"];
            }
            else{
                return ["message" => "Error"];
            }
        }
    }
}
