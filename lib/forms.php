<?php

/**
 * Vytváří formuláře ve view
 *
 * příklad php:
 * <?php
 *  $f = new FormBuilder($user, &@errors);
 *  echo $f->form('index.php', 'get');
 *  echo $f->label('name');
 *  echo $f->text_field('name');
 *  echo $f->form();
 * ?>
 */
class FormBuilder {
  private $bean;

  /**
   * @params $bean Model
   * @params mixed[] Pole s chybami pro jednotlivé atributy modelu
   */
  public function __construct($bean, $errors = array()) {
    $this->bean = $bean;
    $this->errors = $errors;
  }

  /**
   * Vytvoří form tag nebo ukončí
   *
   * První voláni z akcí - ukončení formuláře bez parametrů
   *
   * @param string $action akce formuláře
   * @param mixed[] $params parametry routeru
   * @param string $method metoda formuláře (default post)
   * @return string form tag
   */
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

  /**
   * Vytvoří label tag pro konrétní atribut
   *
   * @param string $attribute název atributu
   * @param string $text text labelu, defaultně název atributu s velkým první písmenem
   * @return string label tag
   */
  public function label($attribute, $text = null) {
    if ($text === null) {
      $text = ucfirst($attribute);
    }
    $e = $this->errors;
    $bean = $this->bean->getMeta('type');
    return "<label " . (isset($e[$attribute]) ? "class=\"error\" " : '') . "for=\"{$bean}[$attribute]\">$text</label>\n";
  }

  /**
   * Vytvoří input tag pro konrétní atribut
   *
   * @param string $type typ inputu
   * @param string $attribute název atributu
   * @param boolean $required jestli je input required
   * @return string input tag
   */
  public function input($type, $attribute, $required = false) {
    $bean = $this->bean->getMeta('type');
    $value = $this->bean->$attribute;
    $e = $this->errors;

    $html = "<input " . (isset($e[$attribute]) ? "class=\"error\" " : '') . "type=\"$type\" id=\"{$bean}[$attribute]\" name=\"{$bean}[$attribute]\"".($value ? " value=\"$value\"" : '').($required ? ' required' : '') . ">\n";
    if (isset($e[$attribute])) {
      $html .= "<small class=\"error\">{$e[$attribute]}</small>\n";
    }
    return $html;
  }

  /**
   * Vytvoří text input
   *
   * @see FormBuilder::input()
   */
  public function text_field($attribute, $required = false) {
    return $this->input('text', $attribute, $required);
  }

  /**
   * Vytvoří password input
   *
   * @see FormBuilder::input()
   */
  public function password_field($attribute, $required = false) {
    return $this->input('password', $attribute, $required);
  }

  /**
   * Vytvoří submit čudl
   *
   * @param string $text value inputu
   * @return string submit input
   */
  public function submit($text = '') {
    return "<input type=\"submit\" ".($text ? " value=\"$text\"" : '').">\n";
  }

}
