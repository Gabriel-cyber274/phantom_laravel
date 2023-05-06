<?php

namespace App\Http\Controllers;

use App\Models\AnonymousMessage;
use Illuminate\Http\Request;
use App\Models\Anonymous;

class AnonymousMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMessages($anon_id)
    {
        //
        $id = auth()->user()->id;
        $Anonymous = Anonymous::with('messages', 'user')->where('id', $anon_id)->where('user_id', $id)->get();


        if(count($Anonymous) === 0) {
            $response = [
                'message'=> 'you cant view this',
                'success' => false
            ];
            return response($response);
        }else {
            $sorted = collect($Anonymous->first()->messages)->sortByDesc('id');
            $final  = [];
            foreach($sorted->values()->all() as $data){
                $final[] = $data;
            }
            $response = [
                'messages'=> $final,
                'message'=> 'retrieved successfully',
                'success' => true
            ];
            return response($response);
        }
        


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
    public function sendMessage(Request $request)
    {
        //
        $fields = $request->validate([
            'message'=> 'required|string',
            'review'=> 'required|string',
            'hint'=> 'required|boolean',
            'hint_text'=> 'required|string',
            'anonymous_id'=> 'required|integer'
        ]);

        $id = auth()->user()->id;

        $Anonymous = Anonymous::where('id', $request->anonymous_id)->get()->first();
        
        if($Anonymous->user_id === $id) {
            $response = [
                'message'=> "you can't send messages to yourself",
                'success' => true
            ];

            return response($response);
        }else {
            $message = AnonymousMessage::create([
                'message'=> $request->message,
                'review'=> $request->review,
                'hint' => $request->hint,
                'hint_text'=> $request->hint_text,
                'anonymous_id'=> $request->anonymous_id
            ]);
    
            $Anonymous->messages()->attach($message);
    
    
            
            $response = [
                'message'=> 'message sent',
                'success' => true
            ];
    
            return response($response);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AnonymousMessage  $anonymousMessage
     * @return \Illuminate\Http\Response
     */
    public function show(AnonymousMessage $anonymousMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AnonymousMessage  $anonymousMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(AnonymousMessage $anonymousMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AnonymousMessage  $anonymousMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AnonymousMessage $anonymousMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AnonymousMessage  $anonymousMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(AnonymousMessage $anonymousMessage)
    {
        //
    }
}
