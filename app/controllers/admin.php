<?php

class AdminController extends ApplicationController {
  public function before_action($params) {
    $this->user = R::findOne('users', $params->session()['user_id']);
    if ($this->user === null) {
      return $this->redirect($this->router()->generate('admin_login'));
    }

    $this->title = "administrace";
  }

  public function index($params) {
    $this->servers = R::find('servers');
  }

  public function login($params) {
  }

  public function do_login($params) {
  }
}
