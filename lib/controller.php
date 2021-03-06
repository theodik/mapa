<?php

spl_autoload_register(function ($name) {
  if (!preg_match('/^\w+Controller$/', $name)) {
    return;
  }
  include(ROOT_DIR . '/app/controllers/' . getControllerFileName($name) . '.php');
});

/**
 * Vrátí cestu k souboru s controllerem
 *
 * @return string cesta k souboru s controllerem
 */
function getControllerFileName($name) {
  $matches = Array();
  preg_match('/^(\w+)Controller$/', $name, $matches);
  return strtolower($matches[1]);
}

/**
 * Třída Controller vykonává akce a renderuje views
 */
class Controller {

  protected $params, $application;
  public $errors;

  public function __construct($application, $params) {
    $this->application = $application;
    $this->params = $params;
  }

  /**
   * Vrací z názvu třídy controlleru lowercase název.
   *
   * @param string $className Název třídy
   * @return string lowercase název
   */
  public static function controller_name($className) {
    return strtolower(str_replace("Controller", '', $className));
  }

  /**
   * Vrací jméno controlleru
   *
   * @return string název
   */
  public function name() {
    return self::controller_name(get_class($this));
  }

  /**
   * Vrací router
   *
   * @return AltoRouter router
   */
  public function router() {
    return $this->application->getRouter();
  }


  /**
   * Zavolá akci a vyrenderuje požadované view.
   *
   * @param string $action akce controlleru
   */
  public function render_view($action) {
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
    $builder->view(get_key($result, 'view', $action));
    $view = $builder->build();

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
  /**
   * Přesměruje na jinou akci
   *
   * V akci controlleru:
   * return $this->redirect('root');
   *
   * @param string $path akce
   * @return boolean Vždy vrací false
   */
  protected function redirect($path) {
    $router = $this->application->getRouter();
    $url = $router->generate($path);
    header('Location: ' . $url);
    return false;
  }

  /**
   * Vyrenderuje view jiné akce
   *
   * V akci controlleru:
   * return $this->redirect('index');
   *
   * @param string $action akce controlleru
   * @return mixed[]
   */
  protected function render($action) {
    return array('view' => $action);
  }
}
