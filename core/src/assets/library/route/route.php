<?php




class PhpRoute
{

    private $isRouteExists;

    public function __construct()
    {
    }

    function get($route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            return $this->route($route, $path_to_include);
        }
        return null;
    }

    function post($route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $this->route($route, $path_to_include);
        }
        return null;
    }

    function put($route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            return $this->route($route, $path_to_include);
        }
        return null;
    }

    function patch($route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            $this->route($route, $path_to_include);
        }
    }

    function delete($route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            return $this->route($route, $path_to_include);
        }
        return null;
    }

    function any($route, $path_to_include)
    {
        return $this->route($route, $path_to_include);
    }

    function route($route, $path_to_include)
    {
        $callback = $path_to_include;
        if (!is_callable($callback)) {
            if (!strpos($path_to_include, '.')) {
                $path_to_include .= '.php';
            }
        }

        $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $request_url = rtrim($request_url, '/');
        $request_url = strtok($request_url, '?');
        $route_parts = explode('/', $route);

        $request_url_parts = explode('/', $request_url);
        array_shift($route_parts);
        array_shift($request_url_parts);

        if (@$route_parts[0] == '' && count($request_url_parts) == 0) {
            // Callback function
            if (is_callable($callback)) {
                $this->isRouteExists = true;
                call_user_func_array($callback, []);
                exit();
            }
            return $this->includeView($path_to_include);
        }


        if (count($route_parts) != count($request_url_parts)) {
            return "";
        }

        $parameters = [];
        for ($n = 0; $n < count($route_parts); $n++) {
            $route_part = $route_parts[$n];
            if (preg_match("/^[$]/", $route_part)) {
                $route_part = ltrim($route_part, '$');
                array_push($parameters, $request_url_parts[$n]);
                $$route_part = $request_url_parts[$n];
            } else if ($route_parts[$n] != $request_url_parts[$n]) {
                return "";
            }
        }

        // Callback function
        if (is_callable($callback)) {
            $this->isRouteExists = true;
            call_user_func_array($callback, $parameters);
            exit();
        }

        return $this->includeView($path_to_include);
    }

    /**
     * [Xamtax]
     * @param $path_to_include
     * @return bool
     */
    private function includeView($path_to_include)
    {
        $this->isRouteExists = @include($path_to_include);
        return true;
    }

    /**
     * [Xamtax]
     * @return bool
     */
    public function hasRoute()
    {
        return !!$this->isRouteExists;
    }


    function out($text)
    {
        echo htmlspecialchars($text);
    }


}