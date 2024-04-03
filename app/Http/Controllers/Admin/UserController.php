<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Jobs\SendMailJob;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        $data = $request->all();
        $password = $this->generate_string();
        $data['password'] = bcrypt($password);
        $data['is_active'] = 0;
        User::create($data);
        $dataSendMail['password'] = $password;
        $dataSendMail['full_name'] = $data['full_name'];
        $dataSendMail['email'] = $data['email'];
        $dataSendMail['link'] = env('APP_CLIENT_URL') . "/change-password";
        SendMailJob::dispatch($data['email'], 'Thank you for joining the app Course. This is your account confirmation message', $dataSendMail, 'mail.create_user');
        return response()->json(['message' => 'Create user successfully'], 200);
    }

    public function getData(Request $request)
    {
        $user = User::where("id_role", 0)->orderBy('id', 'DESC');
        if($request->is_block != ""){
            $user = $user->where('is_block', $request->is_block);
        }
        if($request->email != ""){
            $user = $user->where('email', 'like' , '%' . $request->email . '%');
        }
        $user = $user->paginate(5);
        if (count($user) > 0) {
            return response()->json([
                'users'  => $user,
            ], 200);
        }
        return response()->json([
            'error'  => "There are no accounts in the system!",
        ], 400);
    }

    public function updateStatus($id)
    {
        $user = User::where("id_role", 0)->where("id", $id)->first();
        if ($user) {
            $user->is_block = !$user->is_block;
            $user->save();

            return response()->json([
                'message'  => "Update status successfully",
            ], 200);
        }
        return response()->json([
            'error'  => 'An error has occurred',
        ], 400);
    }

    public function destroy($id)
    {
        $user = User::where("id_role", 0)->where("id", $id)->first();
        if ($user) {
            $user->delete();
            return response()->json([
                'message'  => "Delete user successfully",
            ], 200);
        }
        return response()->json([
            'error'  => 'An error has occurred',
        ], 400);
    }

    public function generate_string($strength = 10) {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }
}
