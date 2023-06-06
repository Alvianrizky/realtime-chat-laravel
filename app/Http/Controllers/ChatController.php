<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupAdmin;
use App\Models\GroupUser;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = User::whereNot('id', Auth::user()->id)->get();

        return view('chat', compact('user'));
    }
    public function getUserChat()
    {
        $groupUser = GroupUser::where('user_id', Auth::user()->id)->with(['user', 'group'])->get();
        if($groupUser) {
            $groupId = array_column($groupUser->toArray(), 'group_id');
            $groupUser = GroupUser::whereNot('user_id', Auth::user()->id)->whereIn('group_id', $groupId)->groupBy('group_id')->with(['user', 'group'])->get();
        }

        return response()->json($groupUser);
    }

    public function listUser()
    {
        $groupUser = GroupUser::where('user_id', Auth::user()->id)->with(['user', 'group'])->get();
        $userId = [];
        if($groupUser) {
            $groupId = array_column($groupUser->toArray(), 'group_id');
            $group = GroupUser::whereNot('user_id', Auth::user()->id)->whereIn('group_id', $groupId)->get();
            $userId = array_column($group->toArray(), 'user_id');
        }

        $user = User::whereNot('id', Auth::user()->id);

        if($userId) {
            $user = $user->whereNotIn('id', $userId)->get();
        } else {
            $user = $user->get();
        }

        return response()->json($user);
    }

    public function storeUser(Request $request)
    {
        $group = Group::create([
            'type' => 'private',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $data = [];

        $data[] = [
            'user_id' =>  Auth::user()->id,
            'group_id' =>  $group->id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $data[] = [
            'user_id' =>  $request->input('user'),
            'group_id' =>  $group->id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        GroupUser::insert($data);

        $groupUser = GroupUser::where('user_id', Auth::user()->id)->with(['user', 'group'])->get();
        if ($groupUser) {
            $groupId = array_column($groupUser->toArray(), 'group_id');
            $groupUser = GroupUser::whereNot('user_id', Auth::user()->id)->whereIn('group_id', $groupId)->with(['user', 'group'])->get();
        }

        return response()->json($groupUser);
    }

    public function storeGroup(Request $request)
    {
        $group = Group::create([
            'name' => $request->input('name'),
            'type' => 'group',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $data = [];

        $data[] = [
            'user_id' =>  Auth::user()->id,
            'group_id' =>  $group->id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        foreach($request->input('user') as $value) {
            $data[] = [
                'user_id' =>  $value,
                'group_id' =>  $group->id,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        GroupUser::insert($data);
        GroupAdmin::insert([
            'user_id' =>  Auth::user()->id,
            'group_id' =>  $group->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $groupUser = GroupUser::where('user_id', Auth::user()->id)->with(['user', 'group'])->get();
        if ($groupUser) {
            $groupId = array_column($groupUser->toArray(), 'group_id');
            $groupUser = GroupUser::whereNot('user_id', Auth::user()->id)->whereIn('group_id', $groupId)->with(['user', 'group'])->get();
        }

        return response()->json($groupUser);
    }

    public function getMessage(Request $request)
    {
        $chat = Message::where('group_id', $request->input('group_id'))->with('user')->get();

        return response()->json($chat);
    }


    public function sendMessage(Request $request)
    {
        $data = [
            'user_id' => Auth::user()->id,
            'group_id' => $request->input('group_id'),
            'content' => $request->input('content'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $message = Message::create($data);
        $chat = Message::where('id', $message->id)->with('user')->first();

        return response()->json($chat);
    }
}
