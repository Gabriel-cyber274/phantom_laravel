<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RoomMessage;
use App\Models\rooms;
use App\Models\Anonymous;
use Illuminate\Support\Str;

use function League\Flysystem\filter;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkRoom($userid)
    {
        //
        $id = auth()->user()->id;
        $room = rooms::where('creator_id', $id)->where('user_id', $userid)->get();

        if(count($room) == 0) {
            $response = [
                'message'=> "room doesn't exit",
                'success' => false
            ];

            return response($response);
        }else {
            $response = [
                'room' => $room->first(),
                'message'=> "room retrieved",
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
    public function getreceivedRooms()
    {
        //
        $id = auth()->user()->id;
        $myRooms = rooms::has('messages')->with('messages')->where('user_id', $id)->get();
        $roomids = [];

        foreach($myRooms as $room) {
            $roomids[]= RoomMessage::where('room_id', $room->id)->where('seen', 0)->get();
        }


        $myUnread = collect($roomids)->first();

        $result1 = [];
        foreach($myRooms as $room) {
            $main = [
                'id'=> $room->id,
                'user_id'=>$room->user_id,
                'room_name'=> $room->room_name,
                'creator_id'=> $room->creator_id,
                'avatar'=> User::where('id', $room->creator_id)->get()->first()->avatar_id,
                'created_at'=> $room->created_at,
                'updated_at'=> $room->updated_at,
                'unread'=> count($myUnread),
                'messages'=> $room->messages[count($room->messages)-1]
            ];
            $result1[]= $main;
        }


        $sortedL = collect($result1)->sortByDesc('messages');
        $finalL  = [];
        foreach($sortedL->values()->all() as $data){
            $finalL[] = $data;
        }
        

        $response = [
            'rooms'=> $finalL,
            'message'=> 'Rooms Retrieved',
            'success' => true
        ];

        return response($response);
    }


    public function getsentRooms()
    {
        //
        $id = auth()->user()->id;
        $myRooms = rooms::has('messages')->with('messages')->where('creator_id', $id)->get();


        $roomids = [];

        foreach($myRooms as $room) {
            $roomids[]= RoomMessage::where('room_id', $room->id)->where('seen', 0)->get();
        }


        $myUnread = collect($roomids)->first();

        
        $user_ids = [];

        foreach($myRooms as $room) {
            $user_ids[]= $room->user_id;
        }

        
        $users = User::with(['AnonymousLinks'])->find($user_ids);


        $result1 = [];
        for ($i=0; $i < count($myRooms); $i++) { 
            $main = [
                'id'=> $myRooms[$i]->id,
                'user_id'=>$myRooms[$i]->user_id,
                'room_name'=> $users[$i]->nickname,
                'creator_id'=> $myRooms[$i]->creator_id,
                'avatar'=> User::where('id', $myRooms[$i]->user_id)->get()->first()->avatar_id,
                'created_at'=> $myRooms[$i]->created_at,
                'updated_at'=> $myRooms[$i]->updated_at,
                'unread'=> count($myUnread),    
                'messages'=> $myRooms[$i]->messages[count($myRooms[$i]->messages)-1]
            ];
            $result1[]= $main;
        }



        $sortedL = collect($result1)->sortByDesc('messages');
        $finalL  = [];
        foreach($sortedL->values()->all() as $data){
            $finalL[] = $data;
        }


        $response = [
            'rooms'=> $finalL,
            'message'=> 'Rooms Retrieved',
            'success' => true
        ];



        return response($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createRoom(Request $request)
    {
        //
        $fields = $request->validate([
            'linkName'=> 'required|string',
            'user_id' => 'required|integer',
        ]);

        $id = auth()->user()->id;
        $randomCharacters = Str::random(4);
        if(count(Anonymous::where('user_id', $request->user_id)->get()) == 0) {
            $response = [
                'message'=> "user doesn't have a link",
                'success' => false
            ];

            return response($response);
        }
        else if(Anonymous::where('user_id', $request->user_id)->get()->first()->name !== $request->linkName) {
            $response = [
                'message'=> 'Invalid Link',
                'success' => false
            ];

            return response($response);
        }
        else if (count(rooms::where('creator_id', $id)->where('user_id', $request->user_id)->get()) !== 0) {
            $response = [
                'room'=> rooms::where('creator_id', $id)->where('user_id', $request->user_id)->get()->first(),
                'message'=> 'Room exists',
                'success' => true
            ];

            return response($response);   
        }
        else if ($id == $request->user_id) {
            $response = [
                'message'=> "you cant chat with yourself",
                'success' => false
            ];

            return response($response);
        }
        else {
            $createRoom = rooms::create([
                'user_id'=> $request->user_id,
                'room_name'=> 'user'.$randomCharacters,
                'creator_id'=> $id,
                'creator_avatar'=> auth()->user()->avatar_id,
                'block'=> false,
                'report'=> false,
                'reveal'=> false,
                'links'=> false
            ]);

            $response = [
                'room'=> $createRoom,
                'message'=> 'room created',
                'success' => true
            ];

            return response($response);

        }
    }

    public function blockUser (Request $request) {
        $fields = $request->validate([
            'room_id' => 'required|integer',
        ]);

        $id = auth()->user()->id;
        $room = rooms::where('id', $request->room_id)->get();

        if(count($room) == 0) {
            $response = [
                'message'=> 'invalid room',
                'success' => false
            ];

            return response($response);
        }else if ($room->first()->user_id !== $id && $room->first()->creator_id !== $id) {
            $response = [
                'message'=> "you dont belong here",
                'success' => false
            ];

            return response($response);
        }
        else if($room->first()->user_id == $id) {
            $room->first()->update([
                'block'=> true,
            ]);

            $response = [
                'message'=> "room blocked successfully",
                'success' => true
            ];

            return response($response);
        }
        else {
            $response = [
                'message'=> "you can't perform this operation",
                'success' => false
            ];

            return response($response);
        }

    }

    public function reportUser (Request $request) {
        $fields = $request->validate([
            'room_id' => 'required|integer',
        ]);
        
        $id = auth()->user()->id;
        $room = rooms::where('id', $request->room_id)->get();

        if(count($room) == 0) {
            $response = [
                'message'=> 'invalid room',
                'success' => false
            ];

            return response($response);
        }else if ($room->first()->user_id !== $id && $room->first()->creator_id !== $id) {
            $response = [
                'message'=> "you dont belong here",
                'success' => false
            ];

            return response($response);
        }
        else if($room->first()->user_id == $id) {
            $room->first()->update([
                'report'=> true,
            ]);

            $response = [
                'message'=> "user reported successfully",
                'success' => true
            ];

            return response($response);
        }
        else {
            $response = [
                'message'=> "you can't perform this operation",
                'success' => false
            ];

            return response($response);
        }

    }

    public function allowLinks (Request $request) {
        $fields = $request->validate([
            'room_id' => 'required|integer',
        ]);
        
        $id = auth()->user()->id;
        $room = rooms::where('id', $request->room_id)->get();

        if(count($room) == 0) {
            $response = [
                'message'=> 'invalid room',
                'success' => false
            ];

            return response($response);
        }else if ($room->first()->user_id !== $id && $room->first()->creator_id !== $id) {
            $response = [
                'message'=> "you dont belong here",
                'success' => false
            ];

            return response($response);
        }
        else if($room->first()->user_id == $id) {
            $room->first()->update([
                'links'=> true,
            ]);

            $response = [
                'message'=> "links allowed successfully",
                'success' => true
            ];

            return response($response);
        }
        else {
            $response = [
                'message'=> "you can't perform this operation",
                'success' => false
            ];

            return response($response);
        }

    }


    public function revealProfile (Request $request) {
        $fields = $request->validate([
            'room_id' => 'required|integer',
        ]);
        
        $id = auth()->user()->id;
        $room = rooms::where('id', $request->room_id)->get();

        if(count($room) == 0) {
            $response = [
                'message'=> 'invalid room',
                'success' => false
            ];

            return response($response);
        }else if ($room->first()->user_id !== $id && $room->first()->creator_id !== $id) {
            $response = [
                'message'=> "you dont belong here",
                'success' => false
            ];

            return response($response);
        }
        else if($room->first()->creator_id == $id) {
            $room->first()->update([
                'reveal'=> true,
            ]);

            $response = [
                'message'=> "identify revealed successfully",
                'success' => true
            ];

            return response($response);
        }
        else {
            $response = [
                'message'=> "you can't perform this operation",
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
