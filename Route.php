<?php namespace route;

class Route
{
    private $_page = null;
    private $_controller = null;
    private $_function = null;
    private $_params = [];

    /**
     * Store the URL
     *
     * Store the URL of the page and create Route object
     *
     * @param $page
     */
    public function __construct($page)
    {
        //Store the current page
        $this->_page = $page;
    }

    public function dirCheck()
    {
        if (is_dir(__DIR__ . '/Controllers') == false) {
            throw new \Exception('Please make sure that you create the \'Controllers\' directory.');
        }

        if (is_dir(__DIR__ . '/Templates') == false) {
            throw new \Exception('Please make sure that you create the \'Templates\' directory.');
        }

        return true;
    }

    /**
     * Extract required information from URL
     *
     * Split the URL into a controller, function to be ran from that
     * controller and all parameters to be passed onto that function
     *
     * @return null
     */
    public function split()
    {
        //Split the URL on `/`
        $url = explode('/', $this->_page);

        //Get the name of the controller
        $controller = $this->_getController($url);

        //Get the name of the controller function to be called
        $function = $this->_getFunction($url);

        //Get all of the parameters passed in the URL
        $params = [];
        $size = sizeof($url);
        for ($i = 3; $i < $size; $i++) {
            $params[] = $url[$i];
        }

        //Set all data that needs to be stored
        $this->_controller = $controller;
        $this->_function = $function;
        $this->_params = $params;

        return compact('controller', 'function', 'params');
    }

    public function checkFilesExist()
    {
        if (!$this->_checkControllerExists($this->_controller)) {
            throw new \Exception('The controller ' . $this->_controller . ' could not be found.');
        }

        if (!$this->_checkTemplateExists($this->_controller, $this->_function)) {
            throw new \Exception('The template ' . $this->_controller . '/' . $this->_function . ' could not be found.');
        }

        return true;
    }

    public function loadController()
    {
        //Require the controller file
        require_once __DIR__ . '/Controllers/' . $this->_controller . 'Controller.php';

        //Store the controller and method to be called
        $controller = $this->_controller . 'Controller';
        $function = $this->_function;

        //Run the required function in the controller
        $data = $controller::$function();

        return $data;
    }

    public function loadTemplate($params)
    {
        //Populate all variables for use in template
        extract($params);
        //Load the template file
        require_once __DIR__ . '/Templates/' . strtolower($this->_controller) . '/' . strtolower($this->_function) . '.php';

        return true;
    }

    /**
     * Extract the controller from URL
     *
     * Get the name of the controller to be loaded from the URL
     *
     * @param $url
     * @return string
     */
    private function _getController($url)
    {
        (empty($url[1]) ? $controller = 'home' : $controller = $url[1]);
        return ucfirst(strtolower($controller));
    }

    /**
     * Extract the controller function to be ran from the URL
     *
     * Get the name of the function to be ran once inside of the controller
     *
     * @param $url
     * @return string
     */
    private function _getFunction($url)
    {
        return (empty($url[2]) ? 'index' : $url[2]);
    }

    private function _checkControllerExists($controller)
    {
        return file_exists(__DIR__ . '/Controllers/' . $controller . 'Controller.php');
    }

    private function _checkTemplateExists($controller, $function)
    {
        return file_exists(__DIR__ . '/Templates/' . strtolower($controller) . '/' . $function . '.php');
    }
}