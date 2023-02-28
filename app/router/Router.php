<?php

class Router
{
    public $defaultAction = 'index';

    /**
     * Handles the request and returns array with controller class and action name.
     *
     * @return array ['controller', 'action']
     */
    public function handleRequest()
    {
        if (isset($_REQUEST['action'])) {
            $route = $_REQUEST['action'];
        } else {
            $inputJson = file_get_contents('php://input');
            $requestData = json_decode($inputJson, true);
            if ($requestData && isset($requestData['action'])) {
                $route = $requestData['action'];
            } else {
                throw new InvalidArgumentException('Page not found.');
            }
        }

        $route = trim($route, '/');

        // find last / in the url
        $separatorPosition = strpos($route, '/');

        if ($separatorPosition === false) {
            $route = $route . '/' . $this->defaultAction;
            $separatorPosition = strpos($route, '/');
        }

        // split route by /
        $className = substr($route, 0, $separatorPosition);
        $action = substr($route, $separatorPosition + 1);

        // if action includes / - split it and add part to controller name
        if (strpos($action, '/') !== false) {
            $className .= '/' . substr($action, 0, strpos($action, '/'));
            $action = substr($action, strpos($action, '/') + 1);
        }

        // replace "-" symbol in action - make camelCase format
        $actionParts = explode('-', $action);

        $action = '';
        for ($i = 0; $i < count($actionParts); $i++) {
            $action .= ($i === 0) ? $actionParts[$i] : ucfirst($actionParts[$i]);
        }

        $controllerDirectory = '';
        if (strpos($className, '/') !== false) {
            [$controllerDirectory, $className] = explode('/', $className);
        }

        // get controller class name in PascalCase format, e.g. LoginController or TestController
        $className = preg_replace_callback('%-([a-z0-9_])%i', function ($matches) {
                return ucfirst($matches[1]);
            }, ucfirst($className)) . 'Controller';

        // make path to controller
        $pathToController = __DIR__ . '/../controllers' . (strlen($controllerDirectory) ? '/' . $controllerDirectory : '') . '/' . $className . '.php';

        if (!file_exists($pathToController)) {
            // controller not found
            throw new InvalidArgumentException('Page not found.');
        }

        require_once $pathToController;

        if (!class_exists($className)) {
            // wrong class name
            throw new InvalidArgumentException('Page not found.');
        }

        // replace "action" parameter - delete controller path from it
        $_REQUEST['action'] = $action;
        if (isset($_GET['action'])) {
            $_GET['action'] = $action;
        }
        if (isset($_POST['action'])) {
            $_POST['action'] = $action;
        }

        return [
            'controller' => new $className(),
            'action' => $action,
        ];
    }
}