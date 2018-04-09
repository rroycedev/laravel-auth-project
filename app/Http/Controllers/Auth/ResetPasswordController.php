<?php
namespace App\Http\Controllers\Auth;

use App\Helpers\AuthHelper;
use App\Helpers\LdapHelper;
use App\Http\Controllers\Controller;
use App\Rules\ValidateUsername;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
     */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')
            ->with(array('token' => $token, 'username' => ''));
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules());

        $token = $request->input('token');
        $password = $request->input('password');
        $password_confirmation = $request->input('password_confirmation');

        $credentials = AuthHelper::getResetCredentialsFromRequest($request);

        $ret = $this->broker()->reset(
            $credentials,
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if (Password::PASSWORD_RESET !== $ret) {
            return back()->withErrors(
                array('email' => trans($ret))
            );
        }

        return $this->sendResetResponse(trans(Password::PASSWORD_RESET));
    }

    public function validate(
        Request $request,
        array $rules,
        array $messages = array(),
        array $customAttributes = array()
    ) {
        $validationFactory = $this->getValidationFactory()
            ->make($request->all(), $rules, $messages, $customAttributes);

        $validationFactory->validate();

        return $this->extractInputFromRules($request, $rules);
    }

    /**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function validateUsername(Request $request)
    {
        return $request->validate(array(
            'username' => array('required', new ValidateUsername()),
        ));
    }

    protected function resetPassword($user, $password)
    {
        switch (AuthHelper::AuthDriverName()) {
            case 'eloquent':
                return $this->databaseResetPassword($user, $password);
                break;
            case 'adldap':
                return $this->ldapResetPassword($user, $password);
                break;
        }
    }

    protected function databaseResetPassword($user, $password)
    {
        $username = $user->GetAttributes('username');

        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        return AuthHelper::PASSWORD_RESET;
    }

    protected function ldapResetPassword($user, $password)
    {
        $username = $user->getUsername();

        LdapHelper::updatePassword($username, $password);

        event(new PasswordReset($user));

        $msg = array(
            'email' => 'Password has been reset successfully',
        );

        return AuthHelper::PASSWORD_RESET;
    }

    protected function rules()
    {
        return array(
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        );
    }

    protected function sendResetResponse($response)
    {
        return Redirect::to('/login')->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $response
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return back()->withErrors(
            array('username' => trans($response))
        );
    }
}
