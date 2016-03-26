<?php

class HomeController {
    public static function index()
    {
        $hello = 'Hello World!';

        return compact('hello');
    }
}
