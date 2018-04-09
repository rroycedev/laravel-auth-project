<?php

namespace App\Helpers;

use Adldap\Laravel\Facades\Adldap;
use App\LdapDbUser;
use App\User;
use ErrorException;
use Illuminate\Support\Facades\Auth;

class LdapHelper
{
    public static function getLdapGroups()
    {
        $entries = Adldap::search()->where("objectClass", "=", 'posixGroup')->get();

        $groups = array();

        foreach ($entries as $entry) {
            $id = $entry->getAttribute('gidnumber')[0];
            $name = $entry->getAttribute('cn')[0];

            //  Skip unit 'person' since this is an internal unit

            $groups[$id] = $name;
        }

        return $groups;
    }

    public static function getLdapOrganizationalUnits()
    {
        $entries = Adldap::search()->where('objectClass', '=', 'organizationalUnit')->get();

        $groups = array();

        foreach ($entries as $entry) {
            $name = $entry->getAttribute('ou')[0];

            //  Skip unit 'person' since this is an internal unit

            if ($name == "person") {
                continue;
            }
            $groups[] = $name;
        }

        return $groups;
    }

    public static function getLdapOrganizationalRoles()
    {
        $entries = Adldap::search()->where('objectClass', '=', 'organizationalRole')->get();

        $groups = array();

        foreach ($entries as $entry) {
            if (in_array('simpleSecurityObject', $entry->getAttribute('objectclass'))) {
                continue;
            }
            $name = $entry->getAttribute('cn')[0];

            $groups[] = $name;
        }

        return $groups;
    }

    public static function deleteUser($userName)
    {
        $user = Adldap::search()->where("uid", "=", $userName)->first();

        if (!$user) {
            return;
        }

        $user->delete();
    }

    public static function updateUser($username, $firstName, $lastName, $password, $email, $roleId)
    {
        $ldapuser = Adldap::search()->findBy("uid", $username);

        $ldapuser->SetAttribute("givenname", $firstName);
        $ldapuser->SetAttribute("sn", $lastName);
        $ldapuser->SetAttribute("mail", $email);
        $ldapuser->SetAttribute("gidNumber", $roleId);

        if ($password != "") {
            $ldapuser->SetAttribute('userpassword', $password);
        }

        $ldapuser->save();
    }

    public static function createUser($username, $firstName, $lastName, $email, $userPassword, $roleId)
    {
        $user = Adldap::search()->where("uid", $username)->first();

        if (!$user) {
            $dn = "cn=$username," . env('ADLDAP_BASEDN');

            $cn = array($username);

            $user = Adldap::make()->user(
                [
                    'uid' => $username,
                    'cn' => $cn,
                    'sn' => $lastName,
                    'mail' => $email,
                    'givenName' => $firstName,
                    'mail' => $email,
                    'gidNumber' => $roleId,
                    'homeDirectory' => '/home/' . $username,
                    'loginShell' => '/bin/bash',
                    'uidNumber' => LdapHelper::getNextUidNumber(),
                    'dn' => $dn,
                    'userPassword' => $userPassword,
                ]
            );

            $user->setAttribute('objectclass', array('inetOrgPerson', 'posixAccount'));
        } else {
            $user->SetAttribute("mail", $email);
            $user->SetAttribute('givenName', $firstName);
            $user->SetAttribute('sn', $lastName);
            $user->SetAttribute('gidNumber', $roleId);
            $user->SetAttribute('userPassword', $userPassword);
            $user->SetAttribute('homeDirectory', '/home/' . $username);
        }

        try {
            $user->save();
        } catch (ErrorException $ex) {
            echo 'EXCEPTION: ' . $ex->getMessage();
            dd($user);

            if (stripos($ex->getMessage(), "Add: Already exists") !== false) {
                throw new ErrorException("User already exists");
            }

            throw new ErrorException("Error creating user: " . $ex->getMessage());
        }

        return $user->GetAttribute('uidnumber')[0];
    }

    public static function createAdminUser($username, $userPassword)
    {
        $user = Adldap::search()->where("cn", $username)->first();

        if (!$user) {
            $dn = "cn=$username," . env('ADLDAP_BASEDN');

            $user = Adldap::make()->user(
                [
                    'cn' => $username,
                    'description' => 'LDAP administrator',
                    'dn' => $dn,
                    'userPassword' => $userPassword,
                ]
            );

            $user->setAttribute('objectclass', array('simpleSecurityObject', 'organizationalRole', 'top'));
        } else {
            $user->SetAttribute('userPassword', $userPassword);
        }

        try {
            $user->save();
        } catch (ErrorException $ex) {
            if (stripos($ex->getMessage(), "Add: Already exists") !== false) {
                throw new ErrorException("User already exists");
            }

            throw new ErrorException("Error creating user: " . $ex->getMessage());
        }
    }

    public static function getUsers($wantAppAdmin)
    {
        $entries = Adldap::search()->users()->get();

        $ldapUsers = array();

        foreach ($entries as $entry) {
            \Log::info(json_encode($entry, JSON_PRETTY_PRINT));

            $ldapUser = new LdapDbUser();

            $ldapUser->username = $entry->GetAttribute("uid")[0];
            $ldapUser->first_name = $entry->GetAttribute("givenname")[0];
            $ldapUser->last_name = $entry->GetAttribute("sn")[0];
            $ldapUser->email = $entry->GetAttribute("mail")[0];

            if (!$wantAppAdmin && $ldapUser->username == env('APP_ADMIN_USERNAME')) {
                continue;
            }

            $ldapUsers[] = $ldapUser;
        }

        return $ldapUsers;
    }

    public static function getLdapUser($userName)
    {
        return Adldap::search()->where("uid", "=", $userName)->first();
    }

    public static function getUser($userName)
    {
        $entry = Adldap::search()->where("uid", "=", $userName)->first();

        if (!$entry) {
            return null;
        }

        $ldapUser = new LdapDbUser();

        $ldapUser->uid = $entry->GetAttribute("uid")[0];
        $ldapUser->first_name = $entry->GetAttribute("givenname")[0];
        $ldapUser->last_name = $entry->GetAttribute("sn")[0];
        $ldapUser->role_id = $entry->GetAttribute("gidnumber")[0];

        $ldapUser->email = $entry->GetAttribute("mail")[0];

        return $ldapUser;
    }

    public static function createOu($ouName)
    {
        $ret = Adldap::search()->where("ou", $ouName)->first();

        if ($ret) {
            return;
        }

        $ou = Adldap::make()->ou();

        $ou->setDn("ou=" . env('ADLDAP_AUTH_APP_OU', 'App Roles') . "," . env('ADLDAP_BASEDN_WITHOUT_OU'));

        $ou->save();
    }

    public static function createRole($ouName, $groupName)
    {
        $group = Adldap::search()->groups()->where('cn', $groupName)->first();

        if ($group) {
            throw new \Exception("Role $groupName already exists");
        }

        $roles = LdapHelper::getRoles();

        $gidNumber = 1001;

        $found = false;

        if (count($roles) > 0) {
            $maxGidNumber = 0;

            foreach ($roles as $role) {
                if ($role["name"] == $groupName) {
                    return intval($role["id"]);
                }

                if (intval($role["id"]) > $maxGidNumber) {
                    $maxGidNumber = intval($role["id"]);
                }
            }

            $gidNumber = $maxGidNumber + 1;
        }

        $group = Adldap::make()->group();

        $group->setAttribute('gidNumber', $gidNumber);

        $group->setAttribute('dn', "cn=$groupName," . env('ADLDAP_BASEDN'));

        try {
            $group->save();
        } catch (\Exception $ex) {
            throw $ex;
        }

        return $gidNumber;
    }

    public static function getRole($roleName)
    {
        return Adldap::search()->groups()->where("cn", $roleName)->first();
    }

    public static function getRoles()
    {
        $entries = Adldap::search()->where("objectClass", "posixGroup")->get();

        if (!$entries || count($entries) == 0) {
            return array();
        }

        $roles = array();

        foreach ($entries as $entry) {
            $roleName = $entry->getAttribute("cn")[0];
            $roleId = $entry->getAttribute("gidnumber")[0];

            $roles[] = array("id" => $roleId, "name" => $roleName);
        }

        return $roles;
    }

    public static function getRoleName($gidNumber)
    {
        $filter = array("objectClass" => 'posixGroup', "gidnumber" => $gidNumber);

        $entry = Adldap::search()->where($filter)->first();

        if (!$entry) {
            return "Unknown role $gidNumber";
        }

        return $entry->getAttribute("cn")[0];
    }

    public static function getAuthUser()
    {
        $user = Auth::user();

        if ($user) {
            $user->getRoleName();
        }

        return $user;
    }

    public static function getNextUidNumber()
    {
        $entries = Adldap::search()->where('objectClass', 'posixAccount')->get();

        $maxUidNumber = 1000;

        foreach ($entries as $entry) {
            $uidNumber = intval($entry->getAttribute("uidnumber")[0]);

            if ($uidNumber > $maxUidNumber) {
                $maxUidNumber = $uidNumber;
            }
        }

        return $maxUidNumber + 1;
    }

    public static function updateCurrentUserPassword($password)
    {
        LdapHelper::updatePassword(Auth::user()->getUsername(), $password);

        $user = Auth::user();

        $user->SetAttribute('password', $password);

        $user->save();
    }

    public static function updatePassword($username, $password)
    {
        $ldapuser = Adldap::search()->findBy("uid", $username);

        $ldapuser->SetAttribute("userpassword", $password);

        $ldapuser->save();
    }

    public static function correctPassword($username, $password)
    {
        return Adldap::auth()->attempt($username, $password);
    }
}
