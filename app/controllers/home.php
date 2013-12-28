<?php

class HomeController extends ApplicationController {
  public function index($params) {
    $this->servers = R::find('server');
    if (isset($params['id'])){
      $this->server = R::load('server', $params['id']);
    } else {
      $this->server = R::findOne('server');
    }
  }
}

