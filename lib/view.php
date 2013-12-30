<?php

/**
 * Vytváří view
 */
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
    $locator = new ViewLocator(ROOT_DIR . "/app/views/layouts");
    if ($filename === null) {
      $layouts = array_reverse(class_parents($this->controller));
      foreach ($layouts as $name)  {
        $filename = $locator->locate(Controller::controller_name($name));
        $this->addView($this->createView($filename));
      }
    }

    $filename = $locator->locate($this->controller->name());
    $this->addView($this->createView($filename));

    return $this;
  }


  public function view($name = null) {
    $locator = new ViewLocator(ROOT_DIR . "/app/views");
    $filename = $locator->locate($this->controller->name(), $name ?: $this->action);
    $view = $this->createView($filename, "View `%s' not found.");
    return $this->addView($view);
  }

  public function build() {
    return $this->root;
  }

  private function createView($filename, $error = false) {
    if ($filename === false && !$error) return false;
    if ($filename) {
      $view = new FileView($filename);
    } elseif($error) {
      $view = new NotFoundView($this->controller, $this->action, $error);
    } else {
      $view = false;
    }
    return $view;
  }

  private function addView($view) {
    if ($view === FALSE) return;
    if ($this->view) {
      $this->view->addChild($view);
    } else {
      $this->root = $view;
    }
    $this->view = $view;
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

  ////// helpers
  public function include_javascript($filename = '*') {
    $styles = "";
    $i = 0;
    $base = ROOT_DIR . "/assets/javascripts/";
    foreach (glob("$base$filename.js", GLOB_BRACE) as $file) {
      $href = str_replace(ROOT_DIR, Application::instance()->config()->basePath, $file);
      $styles .= "<script src=\"$href\"></script>\n";
      $i += 1;
    }
    $styles .= "<!-- $i scripts -->\n";

    echo $styles;
  }

  public function include_stylesheet($filename = '*') {
    $styles = "";
    $i = 0;
    $base = ROOT_DIR . "/assets/stylesheets/";
    foreach (glob("$base$filename.css", GLOB_BRACE) as $file) {
      $href = str_replace(ROOT_DIR, Application::instance()->config()->basePath, $file);
      $styles .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$href\">\n";
      $i += 1;
    }
    $styles .= "<!-- $i styles-->\n";

    echo $styles;
  }

  public function link_to($text, $action, $params = array()) {
    $router = Application::instance()->getRouter();
    $path = $router->generate($action, $params);
    return $this->link_tag($text, $path);
  }

  public function link_tag($text, $href = null) {
    if ($href === null) $href = $text;
    $text = htmlspecialchars($text, ENT_QUOTES,'UTF-8');
    $href = htmlspecialchars($href, ENT_QUOTES,'UTF-8');
    return "<a href=\"$href\">$text</a>";
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

  public function __toString() {
    return "FileView: {$this->filename}";
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

  public function _render($context) {
    printf("<div style=\"color:red\">".$this->message."</div>", "{$this->controller->name()}#{$this->action}");
  }

  public function addChild($name, $view = null) {
  }

  public function __toString() {
    return "NotFoundView: {$this->filename}";
  }
}

