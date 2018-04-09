<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
        * Bootstrap any application services.
        *
        * @return void
        */
    public function boot()
    {
        //
    }

    /**
        * Register any application services.
        *
        * @return void
        */
    public function register()
    {
        if (env('AUTH_LOG_SQL_QUERIES', false)) {
            \DB::listen(
                function ($query) {
                    $bindings = "(";
                    $firstTime = true;
                    foreach ($query->bindings as $bindValue) {
                        if (!$firstTime) {
                            $bindings .= ", ";
                        } else {
                            $firstTime = false;
                        }

                        $bindings .= "'" . $bindValue . "'";
                    }

                    $bindings .= ")";
                    \Log::info('SQL> ' . $query->sql . "  Bindings [$bindings]");
                }
            );
        }
    }
}
