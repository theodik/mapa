<?php

class HomeController extends Controller {
  public function index($params) {
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

