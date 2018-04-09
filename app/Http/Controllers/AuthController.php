<?php
namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\DatabaseAuthHelper;
use App\Helpers\LdapHelper;
use ErrorException;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    protected function userProviderDriverName()
    {
        return AuthHelper::AuthDriverName();
    }

    protected function handleCreateEloquentUser($request)
    {
        $formInput = $request->all();

        $username = $formInput['username'];
        $firstName = $formInput['first_name'];
        $lastName = $formInput['last_name'];
        $userPassword = $formInput['userpassword'];
        $email = $formInput['email'];
        $roleId = $formInput['role_id'];

        try {
            DatabaseAuthHelper::createUser($username, $firstName, $lastName, $userPassword, $email, $roleId);

            $request->session()->put('message', "User $username has been created successfully");
            $request->session()->put('messagetype', 'success');
        } catch (ErrorException $ex) {
            $request->session()->put('message', $ex->getMessage());
            $request->session()->put('messagetype', 'error');
        }

        return Redirect::to('/user/create');
    }

    protected function handleUpdateEloquentUser($request)
    {
        $formInput = $request->all();

        $username = $formInput['username'];
        $firstName = $formInput['first_name'];
        $lastName = $formInput['last_name'];
        $userPassword = $formInput['userpassword'];
        $email = $formInput['email'];
        $roleId = $formInput['role_id'];

        try {
            DatabaseAuthHelper::updateUser($username, $firstName, $lastName, $userPassword, $email, $roleId);

            $request->session()->put('message', "User $username has been updated successfully");
            $request->session()->put('messagetype', 'success');
        } catch (ErrorException $ex) {
            $request->session()->put('message', $ex->getMessage());
            $request->session()->put('messagetype', 'error');
        }

        return Redirect::to("/user/$username/edit");
    }

    protected function handleCreateOu($request, $ouName)
    {
        \Log::info('AuthController: Creating LDAP ou');

        LdapHelper::createOu('User Roles');

        $request->session()->put('message', "Group 'Administrators' created successfully");
        $request->session()->put('messagetype', 'success');

        return Redirect::to('/user/create');
    }

    protected function handleCreateRole($request, $roleName)
    {
        \Log::info('AuthController: Creating LDAP role');

        LdapHelper::createGroup($roleName);

        $request->session()->put('message', "Role '$roleName' created successfully");
        $request->session()->put('messagetype', 'success');

        return Redirect::to('/user/create');
    }

    protected function handleGetRoles($request)
    {
        $roles = LdapHelper::getRoles();

        return Redirect::to('/user/create');
    }

    protected function handleCreateLdapUser($request)
    {
        $roles = $this->availableRoles();

        $formInput = $request->all();

        $username = $formInput['username'];
        $firstName = $formInput['first_name'];
        $lastName = $formInput['last_name'];
        $userPassword = $formInput['userpassword'];
        $email = $formInput['email'];
        $roleId = $formInput['role_id'];

        try {
            LdapHelper::createUser($username, $firstName, $lastName, $email, $userPassword, $roleId);

            $request->session()->put('message', "User $username has been created successfully");
            $request->session()->put('messagetype', 'success');
        } catch (ErrorException $ex) {
            $request->session()->put('message', $ex->getMessage());
            $request->session()->put('messagetype', 'error');
            throw $ex;
        }

        return Redirect::to('/user/create');
    }

    protected function handleUpdateLdapUser($request)
    {
        $formInput = $request->all();

        $username = $formInput['username'];
        $firstName = $formInput['first_name'];
        $lastName = $formInput['last_name'];
        $userPassword = $formInput['userpassword'];
        $email = $formInput['email'];
        $roleId = $formInput['role_id'];

        try {
            LdapHelper::updateUser($username, $firstName, $lastName, $userPassword, $email, $roleId);

            $request->session()->put('message', "User $username has been updated successfully");
            $request->session()->put('messagetype', 'success');
        } catch (ErrorException $ex) {
            $request->session()->put('message', $ex->getMessage());
            $request->session()->put('messagetype', 'error');
        }

        return Redirect::to("/user/$username/edit");
    }

    protected function getUserListData($wantAppAdmin)
    {
        $driverName = $this->userProviderDriverName();

        switch ($driverName) {
            case 'eloquent':
                $data = DatabaseAuthHelper::getUsers($wantAppAdmin);
                break;
            case 'adldap':
                $data = LdapHelper::getUsers($wantAppAdmin);
                break;
            default:
                \Log::info("getUserListData: Unknown driver [$driverName]");
                break;
        }

        return $data;
    }

    protected function availableRoles()
    {
        switch ($this->userProviderDriverName()) {
            case 'eloquent':
                return DatabaseAuthHelper::getRoles();
            case 'adldap':
                return LdapHelper::getRoles();
        }
    }

    protected function getUser($uid)
    {
        switch ($this->userProviderDriverName()) {
            case 'eloquent':
                $user = DatabaseAuthHelper::getUserbyUsername($uid);
                if ($user) {
                    $user->getRoleInfo();
                }
                break;
            case 'adldap':
                $user = LdapHelper::getUser($uid);
                if ($user) {
                    $user->getRoleInfo();
                }
                break;
        }

        return $user;
    }

    protected function createUser($request)
    {
        switch ($this->userProviderDriverName()) {
            case 'eloquent':
                return $this->handleCreateEloquentUser($request);
            case 'adldap':
                return $this->handleCreateLdapUser($request);
        }
    }

    protected function updateUser($request)
    {
        switch ($this->userProviderDriverName()) {
            case 'eloquent':
                return $this->handleUpdateEloquentUser($request);
            case 'adldap':
                return $this->handleUpdateLdapUser($request);
        }
    }

    protected function deleteUser($uid)
    {
        switch ($this->userProviderDriverName()) {
            case 'eloquent':
                DatabaseAuthHelper::deleteUser($uid);
                break;
            case 'adldap':
                LdapHelper::deleteUser($uid);
                break;
        }
    }

    protected function changeCurrentUserPassword($newPassword)
    {
        switch (AuthHelper::AuthDriverName()) {
            case 'eloquent':
                DatabaseAuthHelper::updateCurrentUserPassword($newPassword);
                break;
            case 'adldap':
                LdapHelper::updateCurrentUserPassword($newPassword);
                break;
        }
    }

    protected function validUserPassword($username, $password)
    {
        switch ($this->userProviderDriverName()) {
            case 'eloquent':
                return DatabaseAuthHelper::correctPassword($username, $password);
                break;
            case 'adldap':
                return LdapHelper::correctPassword($username, $password);
                break;
        }
    }
}
