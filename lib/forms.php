<?php

class FormBuilder {
  private $bean;

  public function __construct($bean, $errors = array()) {
    $this->bean = $bean;
    $this->errors = $errors;
  }

  public function form($action = null, $params = null, $method = 'post') {
    if ($action === null) return "</form>\n";
    $router = Application::instance()->getRouter();
    try {
      $url = $router->generate($action, array($params => $this->bean->$params));
    } catch (Exception $e) {
      $url = $action;
    }

    return "<form action=\"$url\" method=\"$method\">\n";
  }

  public function label($attribute, $text = null) {
    if ($text === null) {
      $text = ucfirst($attribute);
    }
    $e = $this->errors;
    return "<label " . (isset($e[$attribute]) ? "class=\"error\" " : '') . "for=\"$attribute\">$text</label>\n";
  }

  public function input($type, $attribute, $required) {
    $bean = $this->bean->getMeta('type');
    $value = $this->bean->$attribute;
    $e = $this->errors;

    $html = "<input " . (isset($e[$attribute]) ? "class=\"error\" " : '') . "type=\"$type\" id=\"{$bean}[$attribute]\" name=\"{$bean}[$attribute]\"".($value ? " value=\"$value\"" : '').($required ? ' required' : '') . ">\n";
    if (isset($e[$attribute])) {
      $html .= "<small class=\"error\">{$e[$attribute]}</small>\n";
    }
    return $html;
  }

  public function text_field($attribute, $required) {
    return $this->input('text', $attribute, $required);
  }

  public function password_field($attribute, $required) {
    return $this->input('password', $attribute, $required);
  }

  public function submit($text = '') {
    return "<input type=\"submit\" ".($text ? " value=\"$text\"" : '').">\n";
  }

}
