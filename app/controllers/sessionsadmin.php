<?php

class SessionsadminController extends ApplicationController {
  public function new_session($params) {
    $this->user = dispense('user', $params);
  }

  public function create_session($params) {
    $info = array($params['user']['name'], sha1($params['user']['name'].':'.$params['user']['password']));
    $this->user = R::findOne('user', 'name = ? AND hash_pass = ?', $info);

    if (!$this->user) {
      $this->user = dispense('user', $params);
      $this->errors['name'] = "Špatné jméno nebo heslo";
      return $this->render('new_session');
    }

    $session = $params->session();
    $session['user_id'] = $this->user->id;
    var_dump($params->session());

    // return $this->render('new_session');
    return $this->redirect('admin_index');
  }

  public function delete_session($params) {
    $params->session()->clear();
    return $this->redirect('root');
  }
}
