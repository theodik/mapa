<?php

class Params implements ArrayAccess {
  private $params;

  public function __construct(array $additional) {
    $this->params = array_merge($_REQUEST, $additional);
  }

  public function session() {
    return new Session();
  }

  public function offsetExists($offset) {
    return isset($this->params[$offset]);
  }

  public function offsetGet($offset) {
    return $this->sanitize($this->params[$offset]);
  }

  public function offsetSet($offset, $value) {
    $this->params[$offset] = $falue;
  }

  public function offsetUnset($offset) {
    unset($this->params[$offset]);
  }

  private function sanitize($text) {
    return htmlspecialchars($text);
  }
}


class Session extends Params {
  public function __construct() {
    session_start();
    $this->params = $_SESSION;
  }
}
