<?php

class View {
  protected $viewName, $layout, $variables, $controller;

  public function __construct($viewName, $variables, $controller, $layout = true) {
    $this->viewName = $viewName;
    $this->layout = $layout;
    $this->variables = $variables == null ? Array() : $variables;
    $this->controller = $controller;
  }

  public function render() {
    $this->_render($this->controller->getParams());
  }

  public function getFileName() {
    return ROOT_DIR . '/app/views/' . $this->viewName . '.php';
  }

  protected function _render($params) {
    $r = $this->controller->getRouter();
    $route = function ($path, $params = Array()) use ($r) {
      return $r->generate($path, $params);
    };
    extract($this->variables);
    include($this->getFileName());
  }
}

class HamlView extends View {
  public function __construct($viewName, $variables, $controller, $layout = true) {
    parent::__construct($viewName, $variables, $controller, $layout);
  }

  public function getFileName() {
    return ROOT_DIR . '/tmp/views/' . $this->viewName . '.php';
  }

  public function compile() {
    $haml_file = ROOT_DIR . '/app/views/' . $this->viewName . '.php.haml';
    $haml_code = file_get_contents($haml_file);
    $template = new MtHaml\Environment('php');
    $php_code = $template->compileString($haml_code, $haml_file);
    $this->ensureTempDirectory();
    file_put_contents($this->getFileName(), $php_code);
  }

  public function render() {
    $this->compile();
    parent::render();
  }

  protected function ensureTempDirectory() {
    if (!file_exists($this->getFileName())) {
      mkdir(dirname($this->getFileName()), 0777, true);
    }
  }
}