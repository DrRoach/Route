<?php

class dirCheckTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    // tests
    public function testMe()
    {
        require_once __DIR__ . '/../../Route.php';

        $url = '/';
        $route = new \route\Route($url);

        $this->assertTrue($route->dirCheck());
    }
}
