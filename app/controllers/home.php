<?php

class HomeController extends ApplicationController {
  public function index($params) {
    $this->test = "TESTIIIK";
  }

  public function login($params) {
    $user = R::findOne('users', 'name = ? AND hash_pass = SHA1(CONCAT(`name`, ?))', array($params['name'], $params['password']));
    if ($user != null){
      return array('name' => $user->name);
    } else {
      return $this->redirect('root');
    }
  }
}

