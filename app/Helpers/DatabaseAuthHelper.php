<?php
namespace App\Helpers;

use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseAuthHelper
{
    public static function getRoles()
    {
        $results = DB::table('role')->orderBy('name')->get();

        $retResults = array();

        foreach ($results as $result) {
            $retResults[] = $result;
        }

        return $retResults;
    }

    public static function getUsers()
    {
        $results = DB::table('users')
            ->where('users.is_system_object', 0)->get();

        return $results;
    }

    public static function createUser($username, $firstName, $lastName, $userPassword, $emailAddress, $roleId)
    {
        $user = User::where('username', $username)->first();

        if ($user) {
            throw new Exception('User already exists');
        }

        $user = new User();

        $user->username = $username;
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->password = bcrypt($userPassword);
        $user->email = $emailAddress;
        $user->role_id = $roleId;

        try {
            $user->save();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function updateUser($username, $firstName, $lastName, $userPassword, $emailAddress, $roleId)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            throw new Exception('User does not exist');
        }

        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $emailAddress;
        $user->role_id = $roleId;

        if ($userPassword != "") {
            $user->password = bcrypt($userPassword);
        }
        
        try {
            $user->save();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public static function deleteUser($userName)
    {
        $user = User::where('username', $userName)->first();

        if (!$user) {
            return;
        }

        $user->delete();
    }

    public static function getUserbyUsername($username)
    {
        return User::where('username', $username)->first();
    }

    public static function getRoleName($roleId)
    {
        $ret = Role::where('id', $roleId)->first();

        if (!$ret) {
            return "Unknown role $roleId";
        }

        return $ret->name();
    }

    private static function mapDatabaseToVueResult($result)
    {
        $mapping = config('dbtovuemapping.tovue');

        $newResult = array();

        foreach ($mapping as $key => $value) {
            $newResult[$value] = $result->$key;
        }

        return $newResult;
    }

    public static function correctPassword($username, $password)
    {
        return Auth::validate(array("username" => $username, "password" => $password));
    }

    public static function updateCurrentUserPassword($password)
    {
        $user = Auth::user();

        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();
    }
}
