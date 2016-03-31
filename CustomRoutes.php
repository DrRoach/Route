<?php namespace route;

class CustomRoutes
{
    public static function all()
    {
        /**
         * All custom routes should follow the same format:
         * The key of the array is the URL to look for, this can be anything that you like. Any
         * trailing backslashes on both the URL in this file and the URL visited by the user
         * will be ignored. The value of the array is the Controller and function that should be
         * loaded. Notice how you write it the same way you would a static function call.
         * Example:
         * '/friends/delete' => 'EnemiesController::add()'
         */
        return [
            '/' => 'HomeController::index()'
        ];
    }
}