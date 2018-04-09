<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuthHelper
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const RESET_LINK_SENT = 'passwords.sent';

    /**
     * Constant representing a successfully reset password.
     *
     * @var string
     */
    const PASSWORD_RESET = 'passwords.reset';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'passwords.user';

    /**
     * Constant representing an invalid password.
     *
     * @var string
     */
    const INVALID_PASSWORD = 'passwords.password';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'passwords.token';

    public static function authDriverName()
    {
        return env('AUTH_USER_PROVIDER_DRIVER', 'eloquent');
    }

    public static function usernameColumnName()
    {
        if ('eloquent' === self::AuthDriverName()) {
            return 'username';
        }

        return 'uid';
    }

    public static function getCredentialsFromRequest($request)
    {
        $allFormVariables = $request->all();

        $email = $allFormVariables['email'];

        $password = '';

        if (array_key_exists('password', $allFormVariables)) {
            $password = $allFormVariables['password'];
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // We're working with an email.
            Config::set('adldap_auth.usernames.eloquent', 'email');

            $credentials = array(
                'email' => $email,
                'password' => $password,
            );
        } else {
            // We're working with a username.

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $usernameColname = 'email';
            } else {
                $usernameColname = self::getCredentialsUsernameField();
            }

            Config::set('adldap_auth.usernames.eloquent', $usernameColname);

            $credentials = array(
                "$usernameColname" => $email,
                'password' => $password,
            );
        }

        return $credentials;
    }

    public static function getCredentialsUsernameField()
    {
        if ('eloquent' === self::AuthDriverName()) {
            $usernameColname = 'username';
        } else {
            $usernameColname = 'uid';
        }

        return $usernameColname;
    }

    public static function getLoginCredentialsFromRequest($request)
    {
        $allFormVariables = $request->all();

        $email = $allFormVariables['email'];
        $password = '';

        if (array_key_exists('password', $allFormVariables)) {
            $password = $allFormVariables['password'];
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $usernameColname = 'email';
        } else {
            $usernameColname = self::getCredentialsUsernameField();
        }

        Config::set('adldap_auth.usernames.eloquent', $usernameColname);

        $credentials = array(
            "$usernameColname" => $email,
            'password' => $password,
        );

        return $credentials;
    }

    public static function getResetCredentialsFromRequest($request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            // We're working with an email.
            Config::set('adldap_auth.usernames.eloquent', 'email');

            $credentials = array(
                'email' => $request->email,
                'password' => $request->password,
            );
        } else {
            // We're working with a username.

            $usernameColname = AuthHelper::usernameColumnName();

            Config::set('adldap_auth.usernames.eloquent', $usernameColname);

            $credentials = array(
                "$usernameColname" => $request->email,
                'password' => $request->password,
            );
        }

        $credentials['password_confirmation'] = $request->password_confirmation;
        $credentials['token'] = $request->token;

        return $credentials;
    }

    public static function getLoggedInUser()
    {
        $user = Auth::user();

        if ($user) {
            $user->getRoleInfo();
        }

        return $user;
    }
}
