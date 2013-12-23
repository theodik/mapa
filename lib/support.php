<?php

function get_key($array, $key, $default = null) {
  return isset($array[$key]) ? $array[$key] : $default;
}
