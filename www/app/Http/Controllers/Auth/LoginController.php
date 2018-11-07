<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    //only handles user login from API
    protected function sessionCreate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credential = $request->only('email', 'password');

        if (Auth::attempt($credential)) {
            return response()->json([
                'status'  => 'OK',
                'token'  => Auth::user()->api_token,
                'user_id'  => Auth::id(),
            ]);
        }

        return response()->json([
            'status'  => 'ERROR',
            'message'  => 'INVALID_CRDENTIALS',
        ],500);
    }




    protected function sessionDestroy()
    {
        $user = \Auth::guard('api')->user();

        if ($user) {
            $user->api_token = str_random(60); //so that previous does not work
            $user->save();

            return response()->json([
        'status' => 'OK'
    ]);
    }
    }


}
