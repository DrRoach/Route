<?php

class splitTest extends \PHPUnit_Framework_TestCase
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

        $data = $route->split();

        $this->assertEquals('Home', $data['controller']);
        $this->assertEquals('index', $data['function']);
        $this->assertEquals(0, sizeof($data['params']));

        $url = '/friends/add/billy123';
        $route = new \route\Route($url);

        $data = $route->split();

        $this->assertEquals('Friends', $data['controller']);
        $this->assertEquals('add', $data['function']);
        $this->assertEquals('billy123', $data['params'][0]);

        $url = '/news/15';
        $route = new \route\Route($url);

        $data = $route->split();

        $this->assertEquals('News', $data['controller']);
        $this->assertEquals('index', $data['function']);
        $this->assertEquals('15', $data['params'][0]);
    }
}
