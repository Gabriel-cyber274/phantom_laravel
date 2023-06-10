<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RoomMessage;
use App\Models\rooms;
use App\Models\Voicenote;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Response;
use App\Models\Anonymous;
use Illuminate\Support\Str;


class RoomMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function read($roomid) {
        $id = auth()->user()->id;
        $room = RoomMessage::where('room_id', $roomid)->get();
        $allUread = RoomMessage::where('room_id', $roomid)->where('user_id', $id)->where('seen', 0)->get();


        if($room->last()->user_id !== $id) {
            $response = [
                'message'=> 'messages retrieved',
                'success' => true
            ];
    
            return response($response);
        }else {
            foreach($allUread as $message) {
                $message->update([
                    'seen'=> true
                ]);
            }
    
            $response = [
                'message'=> 'messages updated',
                'success' => true
            ];
    
            return response($response); 
        }

    }

    public function getMessages($roomid)
    {
        //
        $id = auth()->user()->id;

        $messages = RoomMessage::with('voicenote')->where('room_id', $roomid)->get();
        $messagesCheck1 = RoomMessage::where('room_id', $roomid)->where('sender_id', $id)->get();
        $messagescheck2 = RoomMessage::where('room_id', $roomid)->where('user_id', $id)->get();

        $room = rooms::where('id', $roomid)->get()->first();


        
        if(count($messagesCheck1)!== 0 || count($messagescheck2)!== 0) {
            $response = [
                'messages'=> $messages,
                'room_info' => $room,
                'message'=> 'messages retrieved',
                'success' => true
            ];
    
            return response($response); 
        }
        else {
            $response = [
                'message'=> 'you have no chat with this person',
                'success' => false
            ];
    
            return response($response);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        //
        $fields = $request->validate([
            // 'message'=> 'required|string',
            'user_id' => 'required|integer',
            'type'=> 'required|string',
            'room_id'=> 'required|integer'
        ]);

        
        $id = auth()->user()->id;
        $room = rooms::where('id', $request->room_id)->get()->first();

        if($id == $request->user_id) {
            $response = [
                'message'=> "can't send this message",
                'success' => false
            ];
    
            return response($response);
        }
        else if ($room->user_id !== $id && $room->creator_id !== $id) {
            $response = [
                'message'=> "you can't send this message",
                'success' => false
            ];
    
            return response($response);
        }
        else if ($room->block == 1 && $id == $room->creator_id || $room->block && $id == $room->creator_id) {
            $response = [
                'message'=> "this room has been blocked",
                'success' => false
            ];
    
            return response($response);
        }
        else if ($room->block == 1 && $id == $room->user_id || $room->block && $id == $room->user_id) {
            $response = [
                'message'=> "you blocked this room",
                'success' => false
            ];
    
            return response($response);
        }
        else {
            $message = RoomMessage::create([
                'user_id'=> $request->user_id,
                'message'=> $request->message,
                'type'=> $request->type,
                'sender_id'=> $id,
                'room_id'=> $request->room_id,
                'seen'=> false
            ]);
    
    
            $room->messages()->attach($message);
    
            $response = [
                'sent'=> $message,
                'message'=> 'message sent',
                'success' => true
            ];
    
            return response($response);
        }
    }


    public function reply(Request $request) {
        $fields = $request->validate([
            // 'message'=> 'required|string',
            'user_id' => 'required|integer',
            'type'=> 'required|string',
            'room_id'=> 'required|integer',
            'message_id' => 'required|integer',
        ]);

        $reply = RoomMessage::where('id', $request->message_id)->get();

        $id = auth()->user()->id;
        $room = rooms::where('id', $request->room_id)->get()->first();

        if(count($reply) == 0) {
            $response = [
                'message'=> "message doesn't exist",
                'success' => false
            ];
    
            return response($response);
        }
        else if ($room->user_id !== $id && $room->creator_id !== $id) {
            $response = [
                'message'=> "you can't send this message",
                'success' => false
            ];
    
            return response($response);
        }
        else if ($room->block == 1 && $id == $room->creator_id || $room->block && $id == $room->creator_id) {
            $response = [
                'message'=> "this room has been blocked",
                'success' => false
            ];
    
            return response($response);
        }
        else if ($room->block == 1 && $id == $room->user_id || $room->block && $id == $room->user_id) {
            $response = [
                'message'=> "you blocked this room",
                'success' => false
            ];
    
            return response($response);
        }
        else {
            $message = RoomMessage::create([
                'user_id'=> $request->user_id,
                'message'=> $request->message,
                'type'=> $request->type,
                'sender_id'=> $id,
                'room_id'=> $request->room_id,
                'seen'=> false,
                'reply_id'=> $reply->first()->sender_id,
                'reply_message'=> $reply->first()->message,
                'message_id'=> $reply->first()->id
            ]);
    
    
            $room->messages()->attach($message);
    
            $response = [
                'sent'=> $message,
                'message'=> 'message sent',
                'success' => true
            ];
    
            return response($response);
        }

    }


    public function voiceNote(Request $request) {
        $fields = $request->validate([
            'message_id' => 'required|integer',
            'file'=> 'required'
        ]);

        $file = new Voicenote;
        
        if($request->file()) { 
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('voicenotes', $fileName, 'public');
            $file->name = time().'_'.$request->file->getClientOriginalName();
            $file->message_id = $request->message_id;
            $file->file_path = '/storage/' . $filePath;
            $file->save();
            
            $response = [
                'path'=>$file->file_path,
                'message'=> "file uploaded",
                'success' => true
            ];
    
            return response($response);
        }else {
            $response = [
                'message'=> "file upload failed",
                'success' => false
            ];
    
            return response($response);
        }
        
    }

    // public function getImages($fileName) {
    //     $path = public_path('voicenotes/'.$fileName);

    //     if(!file_exists($path)) {
    //         abort(404);
    //     }

    //     $file = file_get_contents($path);
    //     $type = mime_content_type($path);

    //     return Response::make($file, 200, [
    //         'Content-Type'=>$type,
    //         'Content-Disposition'=> 'inline, filename="'. $fileName . '"',
    //     ]);
    // }
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
     * @param  \Illuminate\Http\Request  $requestuest
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
