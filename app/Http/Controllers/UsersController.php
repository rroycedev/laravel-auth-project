<?php
namespace App\Http\Controllers;

use App\Helpers\LdapHelper;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UsersController extends AuthController
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

    protected $adldap;

    protected $orgUnits = array();
    protected $orgRoles = array();

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function readUsers()
    {
        $data = LdapHelper::getUsers();

        echo json_encode($data);
    }

    public function editIndex()
    {
        $user = Auth::user();

        if ($user) {
            $user->getRoleInfo();
        }

        $data = $this->getUserListData(true);

        return view('user.list', array('users' => $data));
    }

    public function deleteIndex()
    {
        $user = Auth::user();

        if ($user) {
            $user->getRoleInfo();
        }

        $data = $this->getUserListData(false);

        return view('user.list', array('users' => $data));
    }

    public function create(Request $request)
    {
        $message = Session::get('message');
        $messagetype = Session::get('messagetype');

        $data = array('data' => array('roles' => $this->availableRoles(), 'message' => $message,
            'messagetype' => $messagetype));

        Session::put('message', '');
        Session::put('messagetype', '');

        return view('user.create', $data);
    }

    public function edit($uid)
    {
        $user = $this->getUser($uid);

        if ($user) {
            $user->getRoleInfo();
        }

        $message = Session::get('message');
        $messagetype = Session::get('messagetype');

        $data = array('data' => array('user' => $user, 'roles' => $this->availableRoles(), 'message' => $message,
            'messagetype' => $messagetype));

        Session::put('message', '');
        Session::put('messagetype', '');

        return view('user.edit', $data);
    }

    public function store(Request $request)
    {
        return $this->createUser($request);
    }

    public function update(Request $request)
    {
        $formInput = $request->all();

        $userPassword = $formInput['userpassword'];
        $confirmedPassword = $formInput["reentered_password"];

        if ($userPassword != "") {
            if ($userPassword != $confirmedPassword) {
                return back()->withError(array('reentered_password' => "Re-entered password does match"));
            }
        }

        return $this->updateUser($request);
    }

    public function doCreate(Request $request)
    {
        dd($request);
    }

    public function destroy($uid)
    {
        try {
            $this->deleteUser($uid);

            echo json_encode(array('success' => true, 'msg' => ''));
        } catch (\Exception $ex) {
            echo json_encode(array('success' => false, 'msg' => $ex->getMessage()));
        }
    }

    public function createRole(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->getRoleInfo();
        }

        $message = Session::get('message');
        $messagetype = Session::get('messagetype');

        $data = array('data' => array('role' => null, 'message' => $message, 'messagetype' => $messagetype));

        Session::put('message', '');
        Session::put('messagetype', '');

        return view('user.createrole', $data);
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
    public function showChangePasswordForm(Request $request)
    {
        return view('auth.passwords.change');
    }

    public function changePswd(Request $request)
    {
        $formInput = $request->all();

        $username = Auth::user()->getUsername();
        $password = $formInput["password"];
        $newPassword = $formInput["newpassword"];
        $passwordConfirmation = $formInput["password_confirmation"];

        if ($newPassword != $passwordConfirmation) {
            return back()->with('status', "Password confirmation does not match the new password");
        }

        if (!$this->validUserPassword($username, $password)) {
            return back()->with('status', "Current password is not correct");
        }

        $this->changeCurrentUserPassword($newPassword);

        Auth::logout();

        return Redirect::to('/login')->with('status', 'Password has been changed successfully');
    }
}
