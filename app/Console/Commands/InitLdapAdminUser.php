<?php

namespace App\Console\Commands;

use App\Helpers\LdapHelper;
use Illuminate\Console\Command;

class InitLdapAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:init-ldap-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes the LDAP admin system object for the OU';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $username = $this->ask('Username [empty to exit]');

        if ($username == "") {
            return;
        }

        $password = $this->secret('Password');

        try {
            LdapHelper::initLdapAdminUser($username, $password, $this);
        } catch (\Exception $ex) {
            $this->error("init-ldap-admin: Error: " . $ex->getMessage());
        }
    }
}
