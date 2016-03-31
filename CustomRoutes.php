<?php namespace route;

class CustomRoutes
{
    public static function all()
    {
        return [
            '/' => 'HomeController::index()'
        ];
    }
}