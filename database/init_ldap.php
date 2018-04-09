<?php
require "vendor/autoload.php";

use App\Helpers\LdapHelper;

echo "Initializing LDAP roles....\n";

LdapHelper::initLdapRoles();
