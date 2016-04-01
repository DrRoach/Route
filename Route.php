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
     * @return array
     */
    public function split()
    {
        //Split the URL on `/`
        $url = explode('/', $this->_page);

        //Get the name of the controller
        $controller = $this->_getController($url);

        //Get the name of the controller function to be called
        $function = $this->_getFunction($url);

        //If $function is array, store the correct data
        if (is_array($function)) {
            $params[] = $function['param'];
            $function = $function['function'];
        }

        //Get all of the parameters passed in the URL
        //Create $params if it doesn't already exist
        if (empty($params)) {
            $params = [];
        }
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

    public function checkCustomRouting()
    {
        //Get array of custom routes
        $customRoutes = CustomRoutes::all();

        $page = $this->_removeTrailingSlash($this->_page);
        foreach ($customRoutes as $url => $route) {
            $url = $this->_removeTrailingSlash($url);

            //Don't do any advanced routing if the URL in the CustomRoutes file is '/'
            if ($url != '/') {
                //Perform regex given in custom routing
                preg_match('[' . $url . ']i', $page, $advancedRoute);

                //If a match was found in the regex, set the URL to it
                if (!empty($advancedRoute[0])) {
                    //Store the custom URL to be checked against the loaded URL
                    $url = $advancedRoute[0];
                    //Store any selected data from advanced regex
                    $advancedUrl = !empty($advancedRoute[1]) ? $advancedRoute[1] : '';
                }
            }

            if (strtolower($page) == strtolower($url)) {
                //Apply custom routing
                $customRoute = $this->_extractCustomRoutes($route, (!empty($advancedUrl) ? $advancedUrl : ''));

                $this->_controller = $customRoute['controller'];
                $this->_function = $customRoute['function'];

                //Don't check any more routes as desired route has been found
                break;
            }
        }
    }

    /**
     * Remove trailing slashes from URL.
     *
     * Remove any trailing slashes from $url. We make sure that $url is bigger than 1
     * so that '/' doesn't turn into ''.
     *
     * @param $url
     *
     * @return String
     */
    private function _removeTrailingSlash($url)
    {
        if (substr($url, -1) == '/' && strlen($url) > 1) {
            $url = substr($url, 0, strlen($url) - 1);
        }

        return $url;
    }

    private function _extractCustomRoutes($route, $url = '')
    {
        //Regex to grab the controller and function from the custom routing
        preg_match('/(?<controller>.*)Controller::(?<function>.*)\(\)/', $route, $match);

        //Check to make sure a controller was found
        if (empty($match['controller'])) {
            throw new \Exception('No controller could be found in your routing.');
        }

        if (empty($match['function'])) {
            throw new \Exception('No function could be found in your routing.');
        }

        //Perform regex to see if any advanced routing was used on the controller
        preg_match('/\$(?<section>[0-9])+/', $match['controller'], $advancedRoute);

        //If advanced routing was used, make sure that any required data was stored
        if (!empty($advancedRoute[0])) {
            $match['controller'] = $url;
        }

        //Perform regex to see if any advanced routing was used on the function
        preg_match('/\$(?<section>[0-9])+/', $match['function'], $advancedRoute);

        //If advanced routing was used, make sure that any required data was stored
        if (!empty($advancedRoute[0])) {
            $match['function'] = $url;
        }

        //Return custom routing
        return [
            'controller' => $match['controller'],
            'function' => $match['function']
        ];
    }

    public function checkFilesExist()
    {
        if (!$this->_checkControllerExists($this->_controller)) {
            if ($this->_404Exists()) {
                $this->_load404();
            } else {
                throw new \Exception('The controller ' . $this->_controller . ' could not be found.');
            }
        }

        if (!$this->_checkTemplateExists($this->_controller, $this->_function)) {
            if ($this->_404Exists()) {
                $this->_load404();
            } else {
                throw new \Exception('The template ' . $this->_controller . '/' . $this->_function . ' could not be found.');
            }
        }

        return true;
    }

    private function _404Exists()
    {
        return file_exists('404.php');
    }

    private function _load404()
    {
        require_once '404.php';
        exit;
    }

    public function loadController()
    {
        //Require the controller file
        require_once __DIR__ . '/Controllers/' . $this->_controller . 'Controller.php';

        //Store the controller and method to be called
        $controller = $this->_controller . 'Controller';
        $function = $this->_function;

        //Run the required function in the controller
        $data = $controller::$function($this->_params);

        return $data;
    }

    public function loadTemplate($params = [])
    {
        //Check to see if params is null before using it
        if (is_null($params)) {
            $params = [];
        }

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
        empty($url[1]) ? $controller = '' : $controller = $url[1];
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
        //Check to see if the second part of the URL is a number, if it is, make the function 'index' and the first
        //param the number
        if (!empty($url[2]) && is_numeric($url[2])) {
            return [
                'function' => 'index',
                'param' => $url[2]
            ];
        } else {
            return (empty($url[2]) ? 'index' : $url[2]);
        }
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
