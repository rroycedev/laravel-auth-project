<?php
namespace App\Http\Controllers\Auth;

use App\Helpers\AuthHelper;
use App\Helpers\LdapHelper;
use App\Http\Controllers\Controller;
use App\Rules\ValidateUsername;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function broker($name = null)
    {
        return Password::broker('users_eloquent');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        if ('eloquent' === AuthHelper::AuthDriverName()) {
            return view('auth.passwords.email');
        }

        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.

        $credentials = AuthHelper::GetCredentialsFromRequest($request);

        switch (AuthHelper::AuthDriverName()) {
            case 'eloquent':
                $response = $this->broker()->sendResetLink($credentials);
                break;
            case 'adldap':
                $response = $this->sendLdapResetLink($credentials);
                break;
            default:
                throw new \Exception('Invalid driver [' . AuthHelper::AuthDriverName() . ']');
        }

        return Password::RESET_LINK_SENT === $response
        ? $this->sendResetLinkResponse($response)
        : $this->sendResetLinkFailedResponse($request, $response);
    }

    public function sendLdapResetLink(array $credentials)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.

        $usernameColname = AuthHelper::getCredentialsUsernameField();

        $username = $credentials[$usernameColname];

        $user = LdapHelper::getLdapUser($username);

        if (null === $user) {
            return AuthHelper::INVALID_USER;
        }

        // Once we have the reset token, we are ready to send the message out to this
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.

        $user->sendPasswordResetNotification(
            $this->broker()->createToken($user)
        );

        return AuthHelper::RESET_LINK_SENT;
    }

    /**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function validateUsername(Request $request)
    {
        return $request->validate(array(
            'username' => array('required', new ValidateUsername()),
        ));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $response
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors(
            array('username' => trans($response))
        );
    }
}
