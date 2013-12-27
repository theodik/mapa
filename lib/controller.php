<?php

spl_autoload_register(function ($name) {
  if (!preg_match('/^\w+Controller$/', $name)) {
    return;
  }
  include(ROOT_DIR . '/app/controllers/' . getControllerFileName($name) . '.php');
});

function getControllerFileName($name) {
  $matches = Array();
  preg_match('/^(\w+)Controller$/', $name, $matches);
  return strtolower($matches[1]);
}

class Controller {

  protected $params, $application;

  public function __construct($application, $params) {
    $this->application = $application;
    $this->params = $params;
  }

  public static function controller_name($className) {
    return strtolower(str_replace("Controller", '', $className));
  }

  public function name() {
    return self::controller_name(get_class($this));
  }

  public function router() {
    return $this->application->getRouter();
  }

  public function render($action) {
    if ($this->call_before_action() === false) return;

    $result = $this->call_action($action);
    if ($result === false) {
      return;
    }

    if ($this->call_after_action() === false) return;


    $builder = new ViewBuilder($this, $action);
    if (get_key($result, 'layout', true)) {
      $builder->layout();
    }
    $builder->view($action);
    $view = $builder->build();
    //var_dump($view);

    $context = get_object_vars($this);
    $context['app'] = $this->application;
    $context['controller'] = $this;
    $context['router'] = $this->application->getRouter();
    $context['params'] = $this->params;

    $view->_render($context);
  }

  protected function call_before_action() {
    return $this->call_action('before_action');
  }

  protected function call_after_action() {
    return $this->call_action('after_action');
  }

  protected function call_action($action) {
    if (method_exists($this, $action)) {
      return call_user_func(array($this, $action), $this->params);
    }
    return null;
  }

  /// helpers
  protected function redirect($path) {
    $router = $this->application->getRouter();
    $url = $router->generate($path);
    header('Location: ' . $url);
    return false;
  }

}
