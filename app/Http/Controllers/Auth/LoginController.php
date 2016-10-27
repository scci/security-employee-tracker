<?php
namespace SET\Http\Controllers\Auth;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SET\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use SET\User;

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
    protected $redirectTo = '/';

    public function username()
    {
        return 'username';
    }
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);

        if(config('auth.guards.web.provider') == 'adldap') {
            Adldap::connect();
        }
    }

    public function authenticated(Request $request, User $user)
    {
        if ($user->status == 'active') {
            return redirect()->intended($this->redirectPath());
        }

        Auth::logout();
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => "Your account is currently not active.",
            ]);
    }
}
