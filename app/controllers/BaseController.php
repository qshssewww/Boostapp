<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../office/services/LoggerService.php';

class BaseController
{
    protected $requestData = [];
    protected $actionParams = [];

    protected static $_instance;

    /**
     * @return BaseController
     */
    final public static function getInstance()
    {
        if (static::$_instance == null) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    public function runAction($action = null)
    {
        $this->processInput();

        try {
            $action = $action ?? $this->requestData['action'] ?? null;
            unset($this->requestData['action']);
            if ($action) {
                $args = $this->bindActionParams($action, $this->requestData);

                return call_user_func([$this, $action], ...$args);
            }
        } catch (ReflectionException $e) {
            LoggerService::error($e);
            throw new InvalidArgumentException('Page not found.', 404);
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }
        throw new InvalidArgumentException('Action not found');
    }

    /**
     * @param $action
     * @param $params
     * @return array
     * @throws ReflectionException
     */
    protected function bindActionParams($action, $params)
    {
        $method = new \ReflectionMethod($this, $action);

        $args = [];
        $missing = [];
        $actionParams = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = $actionParams[$name] = (array) $params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $actionParams[$name] = $params[$name];
                } else {

                    throw new InvalidArgumentException('Invalid data received for parameter "' . $name . '".');
                }
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $actionParams[$name] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new InvalidArgumentException('Missing required parameters: ' . implode(', ', $missing));
        }

        $this->actionParams = $actionParams;

        return $args;
    }

        /**
     * @return void
     */
    protected function processInput()
    {
        if (!empty($_GET)) {
            $this->requestData = $_GET;
        } elseif (!empty($_POST)) {
            $this->requestData = $_POST;
        } else {
            // get data from json input
            $inputJSON = file_get_contents('php://input');
            $this->requestData = json_decode($inputJSON, true);
        }
    }

    /**
     * @return array|false
     */
    protected function getHeaders()
    {
        return getallheaders();
    }

    /**
     * @return string
     */
    protected function getMethod()
    {
        if (isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }

        if (array_key_exists('X-Http-Method-Override', $this->getHeaders())) {
            return strtoupper($this->getHeaders()['X-Http-Method-Override']);
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * @return bool
     */
    protected function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    protected function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @return void
     */
    protected function asJson()
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * @param $name
     * @return void
     */
    protected function exitIfNotSet($name)
    {
        if (!isset($this->requestData[$name])) {
            json_message('Error: ' . $name . ' is not exist', false);
        }
    }

    /**
     * @param $view
     * @param array $data
     * @return void
     */
    protected function render($view, array $data = [])
    {
        echo $this->renderAsString($view, $data);
    }

    /**
     * @param $view
     * @param $data
     * @return string
     */
    protected function renderAsString($view, $data): string
    {
        return View::make($view, $data)->render();
    }


    /**
     * @param $data array|object
     * @return bool
     */
    protected function json($data = []): bool
    {
        $this->asJson();

        echo json_encode($data);

        return true;
    }
}
