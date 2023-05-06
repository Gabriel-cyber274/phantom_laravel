<?php

namespace App\Http\Controllers;

use App\Models\Anonymous;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\AnonymousMessage;


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
        //
        $me = auth()->user();
        // $departmentAll = [];        
        // $departmentWeekly = [];
        // $facultyAll = [];
        // $facultyWeekly = [];
        $allTimeL = [];
        $weeklyL = [];
        $monthly = [];


        if(!is_null($me->location)) {
            $userMainL = User::where('location', $me->location)->get();
            $anonL = [];
            $destruct = [];
            $destruct2=[];
            $messagesL = [];
            $mainL = [];
            $fullDetailsD = [];
            
            foreach($userMainL as $userMain) {
                $anonymous = Anonymous::with('messages')->where('user_id', $userMain->id)->get();
                if(count($anonymous) > 0) {
                    $anonL[] = $anonymous;
                }
            }


            foreach(collect($anonL) as $anon2){
                $destruct[]= collect($anon2)->first();
            }
            
            foreach($destruct as $des) {
                foreach($des->messages as $res) {
                    $AnonymousMessage = AnonymousMessage::where('id',$res->id)->where('review', 'good')->get();
                    if(count($AnonymousMessage) > 0) {
                        $destruct2[]= $AnonymousMessage;
                    }
                }
            }

            
            foreach(collect($destruct2) as $des){
                $messagesL[]= collect($des)->first();
            }

            foreach (collect($messagesL) as $fill) {
                $anonymous = Anonymous::find($fill->anonymous_id);
                $user= User::where('id', $anonymous->user_id)->get()->first();
                $result = [
                    'name'=> $anonymous->name,
                    'user_name'=> $user->name,
                    'user_id'=>$anonymous->user_id,
                    'total'=> count($anonymous->messages),
                    'good'=> count(collect($messagesL)->where('anonymous_id', $anonymous->id)->all()),
                ];
                $mainL[]= $result;
            }

            
            $sortedL = collect($mainL)->sortByDesc('good');
            $finalL  = [];
            foreach($sortedL->values()->all() as $data){
                $finalL[] = $data;
            }

            $weeklyL = collect($finalL)->where('created_at', '==', now()->subHours(167))->all();
            $monthL = collect($finalL)->where('created_at', '==', now()->subHours(690))->all();

            
            $allTimeL[] = $finalL;
            $weeklyL[] = $weeklyL;
            $monthly[] = $monthL;

        }

        // if(!is_null($me->department)) {
        //     // department
        //     $userMainLD = User::where('department', $me->department)->get();
        //     $anonD = [];
        //     $destruct = [];
        //     $destruct2=[];
        //     $messagesD = [];
        //     $mainLD = [];
        //     $fullDetailsD = [];
            
        //     foreach($userMainLD as $userMain) {
        //         $anonymous = Anonymous::with('messages')->where('user_id', $userMain->id)->get();
        //         if(count($anonymous) > 0) {
        //             $anonD[] = $anonymous;
        //         }
        //     }


        //     foreach(collect($anonD) as $anon2){
        //         $destruct[]= collect($anon2)->first();
        //     }
            
        //     foreach($destruct as $des) {
        //         foreach($des->messages as $res) {
        //             $AnonymousMessage = AnonymousMessage::where('id',$res->id)->where('review', 'good')->get();
        //             if(count($AnonymousMessage) > 0) {
        //                 $destruct2[]= $AnonymousMessage;
        //             }
        //         }
        //     }

            
        //     foreach(collect($destruct2) as $des){
        //         $messagesD[]= collect($des)->first();
        //     }

        //     foreach (collect($messagesD) as $fill) {
        //         $anonymous = Anonymous::find($fill->anonymous_id);
        //         $user= User::where('id', $anonymous->user_id)->get()->first();
        //         $result = [
        //             'name'=> $anonymous->name,
        //             'user_name'=> $user->name,
        //             'user_id'=>$anonymous->user_id,
        //             'total'=> count($anonymous->messages),
        //             'good'=> count(collect($messagesD)->where('anonymous_id', $anonymous->id)->all()),
        //         ];
        //         $mainLD[]= $result;
        //     }

            
        //     $sortedD = collect($mainLD)->sortByDesc('good');
        //     $finalD  = [];
        //     foreach($sortedD->values()->all() as $data){
        //         $finalD[] = $data;
        //     }

        //     $weeklyD = collect($finalD)->where('created_at', '==', now()->subHours(167))->all();

        //     $departmentAll[] = $finalD;
        //     $departmentWeekly[]= $weeklyD;
        // }

        // if(!is_null($me->faculty)) {
        //     $userMainLD = User::where('faculty', $me->faculty)->get();
        //     $anonF = [];
        //     $destruct = [];
        //     $destruct2=[];
        //     $messagesF = [];
        //     $mainLF = [];
        //     $fullDetailsD = [];
            
        //     foreach($userMainLD as $userMain) {
        //         $anonymous = Anonymous::with('messages')->where('user_id', $userMain->id)->get();
        //         if(count($anonymous) > 0) {
        //             $anonF[] = $anonymous;
        //         }
        //     }


        //     foreach(collect($anonF) as $anon2){
        //         $destruct[]= collect($anon2)->first();
        //     }
            
        //     foreach($destruct as $des) {
        //         foreach($des->messages as $res) {
        //             $AnonymousMessage = AnonymousMessage::where('id',$res->id)->where('review', 'good')->get();
        //             if(count($AnonymousMessage) > 0) {
        //                 $destruct2[]= $AnonymousMessage;
        //             }
        //         }
        //     }

            
        //     foreach(collect($destruct2) as $des){
        //         $messagesF[]= collect($des)->first();
        //     }

        //     foreach (collect($messagesF) as $fill) {
        //         $anonymous = Anonymous::find($fill->anonymous_id);
        //         $user= User::where('id', $anonymous->user_id)->get()->first();
        //         $result = [
        //             'name'=> $anonymous->name,
        //             'user_name'=> $user->name,
        //             'user_id'=>$anonymous->user_id,
        //             'total'=> count($anonymous->messages),
        //             'good'=> count(collect($messagesF)->where('anonymous_id', $anonymous->id)->all()),
        //         ];
        //         $mainLF[]= $result;
        //     }

            
        //     $sortedF = collect($mainLF)->sortByDesc('good');
        //     $finalF  = [];
        //     foreach($sortedF->values()->all() as $data){
        //         $finalF[] = $data;
        //     }

        //     $weeklyF = collect($finalF)->where('created_at', '==', now()->subHours(167))->all();

        //     $facultyAll[] = $finalF;
        //     $facultyWeekly[]= $weeklyF;
        // }


        $response = [
            'locationAll' => $allTimeL,
            'locationWeekly' => $weeklyL,
            'locationMonthly' => $monthly,
            // 'facultyAll'=> $facultyAll,
            // 'facultyWeekly' => $facultyWeekly,
            // 'departmentAll' => $departmentAll,
            // 'departmentWeekly'=> $departmentWeekly,
            'message'=> 'retrieved successfully',
            'success' => true
        ];

        return response($response);
    }


    public function createLink(Request $request)
    {
        //
        $fields = $request->validate([
            'name'=> 'required|string',
            'url'=> 'required|string'
        ]);

        $id = auth()->user()->id;
        $createLink = Anonymous::create([
            'name'=> $request->name,
            'user_id'=> $id,
        ]);
       
        $response = [
            'phantom_link'=> $request->url.'/'.$createLink->id.'/'.$request->name.'/'.auth()->user()->name.'/'.$id,
            'message'=> 'link created',
            'success' => true
        ];


        return response($response);
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
