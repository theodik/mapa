<?php

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
      call_user_func_array(array($this->view, $name), $arguments);
    }
  }

  public function _render($view) {
    $this->view = $view;
  }
}

class PHPRenderer extends Renderer {
  public function _render($view, $context) {
    parent::_render($view);
    extract($context);
    include $this->filename;
  }
}
