<?php

function get_key($array, $key, $default = null) {
  return isset($array[$key]) ? $array[$key] : $default;
}

function fill_attributes($bean, $params) {
  $type = $bean->getMeta('type');
  if (isset($params[$type])) {
    foreach (array_keys($params[$type]) as $key) {
      $bean->$key = $params[$type][$key];
    }
  }
}

function dispense($type, $params) {
  $bean = R::dispense($type);
  fill_attributes($bean, $params);
  return $bean;
}
