<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class Authcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Login (Request $request) {
        $fields = Validator::make($request->all(), [
            'email'=> 'required|string',
            'password'=> 'required|string',
        ]);
        
        if($fields->fails()) {
            $response = [
                'errors'=> $fields->errors(),
                'success' => false
            ];

            return response($response);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'incorrect credentials',
                'success' => false
            ]);
        }
        // else if(is_null($user->email_verified_at)) {
        //     return response([
        //         'message' => 'email not verified',
        //         'success' => false
        //     ], 401);
        // }

        $token = $user->createToken('myToken')->plainTextToken;

        $response = [
            'user'=> $user,
            'token'=> $token,
            'message'=> 'logged in',
            'success' => true
        ];

        return response($response, 201);

    }

    public function Register (Request $request) {
        $fields = Validator::make($request->all(),[
            'name'=> 'required|string',
            'email'=> 'required|string|unique:users,email',
            'password'=> 'required|string|min:8',
            // 'gender'=>'required|string',
            // 'department'=>'nullable|string',
            // 'faculty'=>'nullable|string',
            'location'=>'nullable|string',
            // 'student'=>'required|boolean',
        ]);

        if($fields->fails()) {
            $response = [
                'errors'=> $fields->errors(),
                'success' => false
            ];

            return response($response);
        }

        $user = User::create([
            'name'=> $request['name'],
            'email'=> $request['email'],
            'password' => bcrypt($request['password']),
            'gender'=> $request['gender'],
            // 'department'=> $request['department'],
            // 'faculty'=> $request['faculty'],
            'location'=> $request['location'],
            // 'student'=> $request['student']
        ]);

        
        $response = [
            'user'=> $user,
            'message'=> 'successful signup',
            'success' => true
        ];

        return response($response);


    }

    public function Logout (Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message'=> 'logged out'
        ];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
}
