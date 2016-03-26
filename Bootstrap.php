<?php namespace route;

//Start sessions
session_start();

//Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', true);

//Get the current page URL
$page = $_SERVER['REQUEST_URI'];

//Require the Route file
require_once 'Route.php';

//Create the new Route object
$route = new \route\Route($page);

//Make sure that 'Controllers' and 'Templates' folders exist
$route->dirCheck();
//Split the URL into controller and template
$route->split();
//Make sure that the controller and method exist
$route->checkFilesExist();
//Load the controller
$data = $route->loadController();
//Load the template
$route->loadTemplate($data);