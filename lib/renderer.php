<?php

/**
 * Renderuje view podle typu
 */
class Renderer {
  public static function get($filename) {
    $file = basename($filename);
    $ext = explode('.', $file, 2);
    if (count($ext) != 2) {
      throw new Exception("No views found for $filename");
    }
    $ext = $ext[1];

    $class = strtoupper($ext)."Renderer";
    if (class_exists($class)) {
      return new $class($filename);
    } else {
      throw new Exception("$class renderer not found!");
    }
  }

  protected $filename, $view;

  public function __construct($filename) {
    $this->filename = $filename;
  }

  public function __call($name, $arguments) {
    if (method_exists($this, $name)){
      call_user_func_array(array($this, $name), $arguments);
    } else {
      return call_user_func_array(array($this->view, $name), $arguments);
    }
  }

  public function _render($view) {
    $this->view = $view;
  }
}

class PHPRenderer extends Renderer {
  public function _render($view) {
    parent::_render($view);
    extract($view->context);
    include $this->filename;
  }
}

class HAMLRenderer extends PHPRenderer {

  private $origfilename;

  protected function compile() {
    $tmpdir = ROOT_DIR . "/tmp/views" . str_replace(ROOT_DIR . '/app/views', '', dirname($this->filename)) . "/";
    $tempname = $tmpdir . basename($this->filename, '.haml') . '.php';
    $haml_code = file_get_contents($this->filename);
    $template = new MtHaml\Environment('php');
    $php_code = $template->compileString($haml_code, $this->filename);
    $this->ensureDirectory($tmpdir);
    file_put_contents($tempname, $php_code);
    return $tempname;
  }

  protected function ensureDirectory($dir) {
    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }
  }

  public function _render($view) {
    $this->origfilename = $this->filename;
    $this->filename = $this->compile();
    parent::_render($view);
  }
}
