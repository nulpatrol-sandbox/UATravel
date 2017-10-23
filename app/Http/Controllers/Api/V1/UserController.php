<?php

namespace App\Http\Controllers\Api\V1;

use Auth;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * User login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data = [
            "status" => 0,
        ];

        $validator = Validator::make($request->all(), [
            "email" => "required|string|email|max:255",
            "password" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            $data = [
                "status" => 0,
                "errors" => $validator->errors(),
            ];
        } else {
            /*
             * Auth::once method to log a user into the application for a single request.
             * No sessions or cookies will be utilized.
             */
            if (Auth::once(["email" => $request->input("email"), "password" => $request->input("password")])) {
                $data = [
                    "status" => 1,
                    "token" => Auth::user()->api_token,
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Register new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:6|confirmed"
        ]);

        if ($validator->fails()) {
            $data = [
                "status" => 0,
                "errors" => $validator->errors(),
            ];
        } else {
            $api_token = str_random(60);

            User::create([
                'name' => $request->input("name"),
                'email' => $request->input("email"),
                'password' => bcrypt($request->input("password")),
                'api_token' => $api_token,
            ]);

            $data = [
                "status" => 1,
                "token" => $api_token,
            ];
        }

        return response()->json($data);
    }

    /**
     * Send email for reset password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
        ]);

        if ($validator->fails()) {
            $data = [
                "status" => 0,
                "errors" => $validator->errors(),
            ];
        } else {
            $response = $this->broker()->sendResetLink(
                $request->only('email')
            );

            if ($response == Password::RESET_LINK_SENT) {
                $data = [
                    "status" => 1,
                    "data" => [
                        "message" => $response,
                    ],
                ];
            } else {
                $data = [
                    "status" => 0,
                    "errors" => [
                        "message" => $response,
                    ],
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Reset password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            $data = [
                "status" => 0,
                "errors" => $validator->errors(),
            ];
        } else {
            $response = $this->broker()->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            if ($response == Password::PASSWORD_RESET) {
                $data = [
                    "status" => 1,
                    "data" => [
                        "message" => $response,
                    ],
                ];
            } else {
                $data = [
                    "status" => 0,
                    "errors" => [
                        "message" => $response,
                    ],
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Get info about current logged user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCurrent()
    {
        return response()->json([
            "status" => 1,
            "data" => Auth::user(),
        ]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param $password
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => str_random(60),
        ])->save();
    }
}