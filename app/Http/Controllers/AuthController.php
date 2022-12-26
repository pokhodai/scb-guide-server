<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse {

        $input = $request->all();

        $rules = array(
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required|string|unique:users',
            'password' => 'required|string'
        );

        $messages = array(
            'email.required|string|email|max:255|unique:users' => "",
            'name.required|string|unique:users' => "",
        );

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $email = $input['email'];
        $name = $input['name'];
        $password = Hash::make($input['password']);
        $api_token = Str::random(80);

        User::forceCreate([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'api_token' => $api_token,
        ]);

        return $this->sendSuccess("Registration success");
    }
}
