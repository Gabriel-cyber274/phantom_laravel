<?php

namespace App\Http\Controllers;
use App\Models\Anonymous;
use App\Models\Avatar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Models\rooms;
use App\Models\User;
use App\Models\Invite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// use App\Mail\MyCustomMail;
// use Illuminate\Support\Facades\Mail;

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
            'password'=> 'required|string|min:8',
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
        $userLink = Anonymous::where('user_id', $user->id)->get();
        

        if(count($userLink) == 0) {
            $response = [
                'user'=> $user,
                'token'=> $token,
                'hasUrl'=>false,
                'message'=> 'logged in',
                'success' => true
            ];
    
            return response($response, 201);
        }else {
            $response = [
                'user'=> $user,
                'token'=> $token,
                'hasUrl'=>true,
                'message'=> 'logged in',
                'success' => true
            ];
    
            return response($response, 201);
        }


    }

    public function Register (Request $request) {
        $fields = Validator::make($request->all(),[
            'name'=> 'required|string',
            'email'=> 'required|string|unique:users,email',
            'password'=> 'required|string|min:8',
            'location'=>'nullable|string',
            'question'=> 'required|string',
            'answer'=> 'required|string',
        ]);

        if($fields->fails()) {
            $response = [
                'errors'=> $fields->errors(),
                'success' => false
            ];

            return response($response);
        }

        $avatar = Avatar::get();
        $randomCharacters = Str::random(3);

        if(count($avatar) !== 0) {
            $user = User::create([
                'name'=> $request['name'],
                'email'=> $request['email'],
                'password' => bcrypt($request['password']),
                'gender'=> $request['gender'],
                'location'=> $request['location'],
                'tutorial'=> false,
                'question'=> $request['question'],
                'answer'=> $request['answer'],
                'avatar_id'=> random_int(0,$avatar->first()->available),
            ]);
    
            $user->update([
                'nickname'=> 'user'.$randomCharacters.$user->id,
            ]);
            
            $response = [
                'user'=> $user,
                'message'=> 'successful signup',
                'success' => true
            ];

            
            $data = [
                'user'=> $user
            ];
            
            // Mail::to('gabrielimoh30@gmail.com')->send(new MyCustomMail($data));

    
            return response($response);
        }
        else {
            $user = User::create([
                'name'=> $request['name'],
                'email'=> $request['email'],
                'password' => bcrypt($request['password']),
                'gender'=> $request['gender'],
                'location'=> $request['location'],
                'tutorial'=> false,
                'question'=> $request['question'],
                'answer'=> $request['answer'],
                'avatar_id'=> random_int(0,3),
            ]);
    
            $user->update([
                'nickname'=> 'user'.$randomCharacters.$user->id,
            ]);
            
            $response = [
                'user'=> $user,
                'message'=> 'successful signup',
                'success' => true
            ];
            
            $data = [
                'user'=> $user
            ];
            
            // Mail::to($user->email)->send(new MyCustomMail($data));
    
            return response($response);
        }


    }

    public function Logout (Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message'=> 'logged out'
        ];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tutorial()
    {
        $id = auth()->user()->id;
        $user = User::where('id', $id)->get()->first();
        $user->update([
            'tutorial'=> true,
        ]);

        $response = [
            'user'=> $user,
            'message'=> 'tutorial completed',
            'success' => true,
        ];

        return response($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userInfo()
    {
        //
        $id = auth()->user()->id;
        $user = User::where('id', $id)->get()->first();
        
        $response = [
            'user'=> $user,
            'message'=> 'user info retrieved',
            'success' => true,
        ];

        return response($response);
    }


    public function changeAvatar (Request $request) {
        $fields = Validator::make($request->all(),[
            'avatar_id'=> 'required|integer',
        ]);

        $id = auth()->user()->id;
        $user = User::where('id', $id)->get()->first();
        $user->update([
            'avatar_id'=> $request->avatar_id,
        ]);
        
        $response = [
            'user'=> $user,
            'message'=> 'avatar changed',
            'success' => true,
        ];

        return response($response);
    }

    public function addAvatar (Request $request) {
        $fields = Validator::make($request->all(),[
            'available'=> 'required|integer',
        ]);

        $avatar = Avatar::get();

        if(count($avatar) == 0) {
            $newAvatar = Avatar::create([
                'available' => $request->available,
            ]);
            $response = [
                'avatars'=> $newAvatar,
                'message'=> 'avatars updated',
                'success' => true,
            ];
    
            return response($response);
        }else {
            $change = $avatar->first()->update([
                'available'=> $request->available
            ]);

            $response = [
                'avatars'=> $change,
                'message'=> 'avatars updated',
                'success' => true,
            ];
    
            return response($response);
        }

    }


    public function inviteUser (Request $request) {
        $fields = $request->validate([
            'user_id' => 'required|integer',
        ]);

        $id = auth()->user()->id;
        $checkInvite = Invite::where('user_id', $request->user_id)->where('invited_id', $id)->get();
        $checkRoom1 = rooms::where('user_id', $id)->get();
        $checkRoom2 = rooms::where('creator_id', $id)->get();

        if($request->user_id == $id) {
            $response = [
                'message'=> 'you cant invite yourself',
                'success' => false,
            ];
    
            return response($response);
        }
        else if(count($checkRoom2) !== 0 && $request->user_id > $id && auth()->user()->tutorial == 1  || count($checkRoom1) !== 0 && $request->user_id > $id && auth()->user()->tutorial == 1) {
            $response = [
                'message'=> 'cant invite this user',
                'success' => false,
            ];
    
            return response($response);
        }
        else if(count($checkInvite) == 0 && count($checkRoom2) == 0 && count($checkRoom1) == 0 && auth()->user()->tutorial == 0 && $request->user_id < $id) {
            $invite= Invite::create([
                'user_id'=> $request->user_id,
                'invited_id' => $id
            ]);

            $response = [
                'invite'=> $invite,
                'message'=> 'invite created',
                'success' => true,
            ];
    
            return response($response);
        }
        else {
            $response = [
                'message'=> 'invite exists',
                'success' => false,
            ];
    
            return response($response);
        }



    }

    
    public function forgotPasswordCheck (Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'question'=> 'required|string',
            'answer'=> 'required|string',
        ]);   

        $user = User::where('email', $request->email)->get();

        if(count($user) == 0) {
            $response = [
                'message'=> "email doesn't belong to a user",
                'success' => false
            ];
    
            return response($response);
        }
        else if ($user->first()->question !== $request->question && $user->first()->answer === $request->answer || $user->first()->question === $request->question && $user->first()->answer !== $request->answer || $user->first()->question !== $request->question && $user->first()->answer !== $request->answer) {
            $response = [
                'message'=> "incorrect question or answer",
                'success' => false
            ];
    
            return response($response);
        }
        else if(count($user) !== 0 && $user->first()->question == $request->question && $user->first()->answer === $request->answer) {
            $response = [
                'message'=> "correct",
                'success' => true
            ];
    
            return response($response);
        }
        else {
            $response = [
                'message'=> "incorrect",
                'success' => false
            ];
    
            return response($response);
        }
        
    }

    public function changePassword (Request $request) {
        $fields = Validator::make($request->all(),[
            'email' => 'required|string',
            'password'=> 'required|string|min:8',
        ]);

        
        if($fields->fails()) {
            $response = [
                'errors'=> $fields->errors(),
                'success' => false
            ];

            return response($response);
        }
        
        $user = User::where('email', $request->email)->get();

        if(count($user)!== 0) {
            $user->first()->update([
                'password'=> bcrypt($request->password),
            ]);
            $response = [
                'message'=> "password changed successfully",
                'success' => true
            ];

            return response($response);
        }
        else {
            $response = [
                'message'=> "email provided does not belong to a user",
                'success' => false
            ];

            return response($response);
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
