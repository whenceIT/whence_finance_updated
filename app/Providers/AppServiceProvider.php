<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('greater_than_field', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value > $min_value;
        });

        Validator::replacer('greater_than_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });
        Validator::extend('smaller_than_field', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value < $min_value;
        });

        Validator::replacer('smaller_than_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });
        Schema::defaultStringLength(191);

        /**
         * Blade directive: @hasRole('role.goa', 'role.risk')
         * Checks the current user's ID against one or more config('role.xxx') arrays.
         */
        \Blade::if('hasRole', function (string ...$keys) {
            $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();
            if (!$user) return false;
            $merged = \App\Helpers\GeneralHelper::mergedRoleIds(...$keys);
            return in_array((string) $user->id, $merged, true);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
