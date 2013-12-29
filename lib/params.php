<?php

/**
 * Objekt params reprezentuje data předané přes POST/GET metodu
 */
class Params implements ArrayAccess {
  private $params, $session;

  public function __construct(array $additional) {
    $this->params = array_merge($_REQUEST, $additional);
    $this->session = new Session();
  }

  /**
   * Vrátí objekt se session
   */
  public function session() {
    return $this->session;
  }

  public function offsetExists($offset) {
    return isset($this->params[$offset]);
  }

  public function offsetGet($offset) {
    if (is_string($this->params[$offset])){
      return $this->sanitize($this->params[$offset]);
    } else {
      return $this->params[$offset];
    }
  }

  public function offsetSet($offset, $value) {
    $this->params[$offset] = $value;
  }

  public function offsetUnset($offset) {
    unset($this->params[$offset]);
  }

  private function sanitize($text) {
    return htmlspecialchars($text);
  }
}

/**
 * Objekt Session reprezentuje data v php session
 */
class Session implements ArrayAccess {
  private $params;

  public function __construct() {
    session_start();
    $this->params = &$_SESSION;
  }

  public function offsetExists($offset) {
    return isset($this->params[$offset]);
  }

  public function offsetGet($offset) {
    if (is_string($this->params[$offset])){
      return $this->sanitize($this->params[$offset]);
    } else {
      return $this->params[$offset];
    }
  }

  public function offsetSet($offset, $value) {
    $this->params[$offset] = $value;
  }

  public function offsetUnset($offset) {
    unset($this->params[$offset]);
  }

  /**
   * Vymaže všechny data ze session
   */
  public function clear() {
    session_destroy();
  }

  private function sanitize($text) {
    return htmlspecialchars($text);
  }
}
