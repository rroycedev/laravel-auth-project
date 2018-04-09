<?php
namespace App\Console\Commands;

use App\Helpers\LdapSchemaManager;
use Illuminate\Console\Command;

class InitLdap extends Command
{
    protected $signature = 'command:init-ldap';
    protected $description = 'Initializes LDAP with authentication related objects like roles and permissions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $username = $this->ask("LDAP Username for organizational unit '" .
            env('ADLDAP_AUTH_APP_OU') . "' [empty to exit]");

        if ($username == "") {
            $this->info("init-ldap: Exiting due to no username specified");
            return;
        }

        $password = $this->secret("Password for LDAP user $username");

        $this->info("init-ldap: Initializing LDAP with authentication objects....");

        try {
            LdapSchemaManager::initLdapObjects($this);
        } catch (\Exception $ex) {
            $this->error("init-ldap: Error: " . $ex->getMessage());
            return;
        }

        $this->info("init-ldap: Initializing LDAP administrator for organizational unit '" .
            env('ADLDAP_AUTH_APP_OU') . "'....");

        try {
            LdapSchemaManager::initLdapAdminUser($username, $password, $this);
        } catch (\Exception $ex) {
            $this->error("init-ldap: Error: " . $ex->getMessage());
            return;
        }

        $this->info("init-ldap: LDAP has been successfully initialized");
    }
}
