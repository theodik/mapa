<?php

class ViewBuilder {
  private $controller, $action;
  protected $root, $view;

  public function __construct($controller, $action) {
    $this->controller = $controller;
    $this->action = $action;
  }

  public function controller() {
    return $this->controller;
  }

  public function action() {
    return $this->action;
  }

  public function layout($filename = null) {
    if ($filename === null) {
      $locator = new ViewLocator(ROOT_DIR . "/app/views/layouts");
      $filename = $locator->locate($this->controller->name());
      if ($filename === FALSE) {
        foreach (class_parents($this->controller) as $name)  {
          $filename = $locator->locate(Controller::controller_name($name));
          if ($filename !== FALSE) break;
        }
      }
    }

    $view = $filename
      ? new FileView($filename)
      : new NotFoundView($this->controller, $this->action, "Layout for %s not found.");

    return $this->addView($view);
  }


  public function view($name = null) {
    $locator = new ViewLocator(ROOT_DIR . "/app/views");
    $filename = $locator->locate($this->controller->name(), $name ?: $this->action);
    $view = $filename
      ? new FileView($filename)
      : new NotFoundView($this->controller, $this->action);

    return $this->addView($view);
  }

  public function build() {
    return $this->root;
  }

  private function addView($view) {
    if ($this->view) {
      $this->view->addChild($view);
    } else {
      $this->root = $view;
    }
    $this->view = $view;
    return $this;
  }
}

class ViewLocator {
  private $basePath;
  public function __construct($basePath) {
    $this->basePath = $basePath;
  }

  public function locate($controller, $action = null) {
    $searchPath = $this->basePath;
    if ($action) {
      $searchPath .= "/$controller/$action.*";
    } else {
      $searchPath .= "/$controller.*";
    }
    $views = array_filter(glob($searchPath), 'is_file');
    return count($views) > 0 ? $views[0] : FALSE;
  }
}

class View {
  protected $children = Array();

  public function addChild($name, $view = null) {
    if ($view == null) {
      $view = $name;
      $name = 'default';
    }
    $this->children[$name] = $view;
  }

  public function _render($context) {
    $this->context = $context;
  }

  public function yield($scope = 0) {
    if (array_key_exists($scope, $this->children)) {
      $view = $this->children[$scope];
    } else {
      $view = $this->children['default'];
    }

    if (method_exists($view, '_render')){
      $view->_render($this->context);
    }
  }
}

class FileView extends View {
  protected $filename;
  public function __construct($filename) {
    $this->filename = $filename;
  }

  public function _render($context) {
    parent::_render($context);
    $renderer = Renderer::get($this->filename);
    $renderer->_render($this, $context);
  }
}

class NotFoundView extends FileView {
  private $controller, $action;

  public function __construct($controller, $action, $message = null) {
    parent::__construct(null);
    $this->controller = $controller;
    $this->action = $action;
    $this->message = $message ?: "Template %s not found!";
  }

  public function _render() {
    printf("<div style=\"color:red\">".$this->message."</div>", "{$this->controller->name()}#{$this->action}");
  }

  public function addChild($name, $view = null) {
  }
}

