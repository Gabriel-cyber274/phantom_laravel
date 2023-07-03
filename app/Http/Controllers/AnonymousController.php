<?php

namespace App\Http\Controllers;

use App\Models\Anonymous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\Invite;
use App\Models\rooms;
use App\Models\User;
use App\Models\AnonymousMessage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AnonymousController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allMyLinks()
    {
        //
        $id = auth()->user()->id;
        $myLinks = Anonymous::with('user')->where('user_id', $id)->get();

        $response = [
            'phantom'=> $myLinks,
            'message'=> 'my links retrieved',
            'success' => true
        ];

        return response($response);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function goodReviews()
    {
        $id = auth()->user()->id;
        $myLocationUsers = User::with('invite')->where('location', auth()->user()->location)->get();

        $result1 = [];

        foreach($myLocationUsers as $user) {
            if(count($user->invite) > 0 && count(rooms::where('user_id', $user->id)->get()) > 0  && $user->invite[count($user->invite)-1]->created_at < rooms::where('user_id', $user->id)->get()->last()->created_at) {
                $main = [
                    'id'=> $user->id,
                    'name'=>$user->name,
                    'avatar_id'=> $user->avatar_id,
                    'points'=> count($user->invite)+count(rooms::where('user_id', $user->id)->get()),
                    'created_at'=> $user->invite[count($user->invite)-1]->created_at,
                    'updated_at'=> $user->invite[count($user->invite)-1]->created_at,
                ];
                
                $result1[]=$main;
            }
            else if (count($user->invite) > 0 && count(rooms::where('user_id', $user->id)->get()) > 0  && rooms::where('user_id', $user->id)->get()->last()->created_at < $user->invite[count($user->invite)-1]->created_at ) {
                $main = [
                    'id'=> $user->id,
                    'name'=>$user->name,
                    'avatar_id'=> $user->avatar_id,
                    'points'=> count($user->invite)+count(rooms::where('user_id', $user->id)->get()),
                    'created_at'=> rooms::where('user_id', $user->id)->get()->last()->created_at,
                    'updated_at'=> rooms::where('user_id', $user->id)->get()->last()->created_at,
                ];
                
                $result1[]=$main;
            }
            else if (count($user->invite) == 0 && count(rooms::where('user_id', $user->id)->get()) > 0) {
                $main = [
                    'id'=> $user->id,
                    'name'=>$user->name,
                    'avatar_id'=> $user->avatar_id,
                    'points'=> count($user->invite)+count(rooms::where('user_id', $user->id)->get()),
                    'created_at'=> rooms::where('user_id', $user->id)->get()->last()->created_at,
                    'updated_at'=> rooms::where('user_id', $user->id)->get()->last()->created_at,
                ];
                
                $result1[]=$main;
            }else if (count(rooms::where('user_id', $user->id)->get()) == 0 && count($user->invite) > 0) {
                $main = [
                    'id'=> $user->id,
                    'name'=>$user->name,
                    'avatar_id'=> $user->avatar_id,
                    'points'=> count($user->invite)+count(rooms::where('user_id', $user->id)->get()),
                    'created_at'=> $user->invite[count($user->invite)-1]->created_at,
                    'updated_at'=> $user->invite[count($user->invite)-1]->created_at,
                ];
                
                $result1[]=$main;
            }else {
                $main = [
                    'id'=> $user->id,
                    'name'=>$user->name,
                    'avatar_id'=> $user->avatar_id,
                    'points'=> count($user->invite)+count(rooms::where('user_id', $user->id)->get()),
                    'created_at'=> $user->created_at,
                    'updated_at'=> $user->created_at,
                ];
                
                $result1[]=$main;
            }
        }

        
        $sortedL = collect($result1)->sortByDesc('points');
        $finalL  = [];
        foreach($sortedL->values()->all() as $data){
            $finalL[] = $data;
        }

        // $weeklyL = collect($finalL)->where('created_at', '==', now()->subHours(167))->all();
        // $monthL = collect($finalL)->where('created_at', '==', now()->subHours(690))->all();

        $weeklyL = collect($finalL)->whereBetween('created_at', [
            Carbon::today()->subWeek()->startOfWeek(),
            Carbon::today()->subWeek()->endOfWeek(),
        ])->all();

        $monthL = collect($finalL)->whereBetween('created_at', [
            Carbon::today()->subMonth()->startOfMonth(),
            Carbon::today()->subMonth()->endOfMonth(),
        ])->all();
        

        $response = [
            'all'=> $finalL,
            'weekly'=> $weeklyL,
            'monthly'=>$monthL,
            'message'=> 'Leaderboard Retrieved',
            'success' => true
        ];

        return response($response);

    }

    
    public function getLink()
    {
        //
        $url = 'http://localhost:3000/chat/user';

        $id = Auth()->user()->id;
        $userLink = Anonymous::where('user_id', $id)->get();
        $randomCharacters = Str::random(15);
        $index = strpos(auth()->user()->name, ' ');
        

        if(count($userLink) !== 0 && $userLink->first()->created_at <= now()->subHours(161) && $index) {
            $userLink->first()->update([
                'name' => $randomCharacters,
            ]);
    
            $response = [
                'link'=>$url.'/'.$id.'/'.substr(auth()->user()->name,0, $index).'/'.auth()->user()->nickname.'/'.auth()->user()->avatar_id.'/'.$userLink->first()->name.'/'.$userLink->first()->id,
                'message'=>'link retrieved',
                'success' => true
            ];
            
            return response($response);
        }
        else if(count($userLink) !== 0 && $userLink->first()->created_at <= now()->subHours(161) && !$index) {
            $userLink->first()->update([
                'name' => $randomCharacters,
            ]);
    
            $response = [
                'link'=>$url.'/'.$id.'/'.auth()->user()->name.'/'.auth()->user()->nickname.'/'.auth()->user()->avatar_id.'/'.$userLink->first()->name.'/'.$userLink->first()->id,
                'message'=>'link retrieved',
                'success' => true
            ];
            
            return response($response);
        }
        else if(count($userLink) !== 0 && !($userLink->first()->created_at <= now()->subHours(161)) && !$index) {
            $response = [
                'link'=>$url.'/'.$id.'/'.auth()->user()->name.'/'.auth()->user()->nickname.'/'.auth()->user()->avatar_id.'/'.$userLink->first()->name.'/'.$userLink->first()->id,
                'message'=>'link retrieved',
                'success' => true
            ];
            
            return response($response);
            // return substr(auth()->user()->name,0, $index);
        }
        else if(count($userLink) !== 0 && !($userLink->first()->created_at <= now()->subHours(161)) && $index) {
            $response = [
                'link'=>$url.'/'.$id.'/'.substr(auth()->user()->name,0, $index).'/'.auth()->user()->nickname.'/'.auth()->user()->avatar_id.'/'.$userLink->first()->name.'/'.$userLink->first()->id,
                'message'=>'link retrieved',
                'success' => true
            ];
            
            return response($response);
        }
        else {
            $response = [
                'message'=>"you don't have a link",
                'success' => false
            ];
            
            return response($response);
        }
    }

    public function linkName($id) {
        // $id = Auth()->user()->id;
        $userLink = Anonymous::where('id', $id)->get();

        if(count($userLink) !== 0) {
            $response = [
                'name' => $userLink->first()->name,
                'message'=>"link name retrieved",
                'success' => true,
            ];
            
            return response($response);
        }else {
            $response = [
                'message'=>"link name is null",
                'success' => false,
            ];
            
            return response($response); 
        }

    }


    public function createLink(Request $request)
    {
        //
        $fields = $request->validate([
            'url'=> 'required|string'
        ]);
        
        
        $id = auth()->user()->id;
        $userLink = Anonymous::where('user_id', $id)->get();
        $randomCharacters = Str::random(15);
        $index = strpos(auth()->user()->name, ' ');

        if(count($userLink)=== 0 && !$index) {
            $createLink = Anonymous::create([
                'name'=> $randomCharacters,
                'user_id'=> $id,
            ]);
            
           
            $response = [
                'phantom_link'=> $request->url.'/'.$id.'/'.auth()->user()->name.'/'.auth()->user()->nickname.'/'.auth()->user()->avatar_id.'/'.$createLink->name.'/'.$createLink->id,
                'message'=> 'link created',
                'success' => true
            ];

            return response($response);
        }
        else if(count($userLink)=== 0 && $index) {
            $createLink = Anonymous::create([
                'name'=> $randomCharacters,
                'user_id'=> $id,
            ]);

            $response = [
                'phantom_link'=> $request->url.'/'.$id.'/'.substr(auth()->user()->name,0, $index).'/'.auth()->user()->nickname.'/'.auth()->user()->avatar_id.'/'.$createLink->name.'/'.$createLink->id,
                'message'=> 'link created',
                'success' => true
            ];

            return response($response);
        }
        else {
            $response = [
                'message'=>'you already have a link',
                'success' => false
            ];
            
            return response($response);
        }
    }
    


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Anonymous  $anonymous
     * @return \Illuminate\Http\Response
     */
    public function show(Anonymous $anonymous)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Anonymous  $anonymous
     * @return \Illuminate\Http\Response
     */
    public function edit(Anonymous $anonymous)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Anonymous  $anonymous
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Anonymous $anonymous)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Anonymous  $anonymous
     * @return \Illuminate\Http\Response
     */
    public function destroy(Anonymous $anonymous)
    {
        //
        
    }
}
