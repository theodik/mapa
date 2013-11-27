<?php

class Params implements ArrayAccess {
  private $get, $post, $additional;

  public function __construct($get, $post, $additional) {
    $this->get = $get;
    $this->post = $post;
    $this->additional = $additional;
  }

  public function offsetExists($offset) {
    return isset($this->get[$offset]) || isset($this->post[$offset]) || isset($this->additional[$offset]);
  }

  public function offsetGet($offset) {
    if (isset($this->get[$offset])){
      return $this->sanitize($this->get[$offset]);
    }
    if (isset($this->post[$offset])){
      return $this->sanitize($this->post[$offset]);
    }
    if (isset($this->additional[$offset])){
      return $this->sanitize($this->additional[$offset]);
    }
  }

  public function offsetSet($offset, $value) {
    
  }

  public function offsetUnset($offset) {

  }

  private function sanitize($text) {
    return htmlspecialchars($text);
  }
}

