# laravel-auth-project
A laravel template project that allows authentication via a database or LDAP server.  This project can be used right out of the box without having to develop authentication related functionality



Installation
============

1. Clone the repository https://github.com/rroycedev/laravel-auth-project.git to your project root directory

2. From the project root directory type the following:

        composer update
        composer run-script post-clone

3. Change .env configuration file for your installation (See section "Configuration" below)

4. Run the composer script to create and seed the database with the tables needed for authentiaction.

        For database authentication:
        
                php artisan migrate:fresh
                php artisan db:seed

        For LDAP authentication

                php artisan migrate:fresh
                php artisan command:init-ldap


Configuration
=============

All configuration changes are made by editing the .env file.

For both database and LDAP authentication, you must first specify a local database server configuration:

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=homestead
        DB_USERNAME=homestead
        DB_PASSWORD=secret

For LDAP authentication:

        1. Comment out the following lines:

                AUTH_USER_PROVIDER_DRIVER=eloquent
                AUTH_USER_MODEL="App\User"

        2. Uncomment the following lines:

                AUTH_USER_PROVIDER_DRIVER=adldap
                AUTH_USER_MODEL="App\LdapDbUser"

For database authentication:

        1. Uncomment the following lines:

                AUTH_USER_PROVIDER_DRIVER=eloquent
                AUTH_USER_MODEL="App\User"

        2. Comment out the following lines:

                AUTH_USER_PROVIDER_DRIVER=adldap
                AUTH_USER_MODEL="App\LdapDbUser"

For LDAP authentication you must also change the values for the following .env variables
for your LDAP installation:

        ADLDAP_AUTH_APP_OU="Application Org Unit"

        ADLDAP_BASEDN="ou=${ADLDAP_AUTH_APP_OU},dc=rroyce,dc=com"
        ADLDAP_ACCOUNT_PREFIX="cn="
        ADLDAP_ACCOUNT_SUFFIX=",ou=${ADLDAP_AUTH_APP_OU},${ADLDAP_BASEDN}"
        ADLDAP_CONTROLLERS=10.0.0.101
        ADLDAP_PORT=389
        ADLDAP_AUTO_CONNECT=false
        ADLDAP_TIMEOUT=5
        ADLDAP_ADMIN_ACCOUNT_PREFIX="cn="
        ADLDAP_ADMIN_ACCOUNT_SUFFIX=",dc=rroyce,dc=com"
        ADLDAP_ADMIN_USERNAME=admin
        ADLDAP_ADMIN_PASSWORD=
        ADLDAP_USE_SSL=false
        ADLDAP_USE_TLS=false
        ADLDAP_LOGIN_FALLBACK=false

