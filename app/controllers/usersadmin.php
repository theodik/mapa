<?php

class UsersadminController extends AdminController {
  public function index($params) {
    $this->users = R::find('user');
  }

  public function new_user($params) {
    $this->user = dispense('user', $params);
  }

  public function create_user($params) {
    $user = R::dispense('user');
    $user->name = trim($params['user']['name']);

    if (!$user->name) {
      $this->errors['name'] = "Jméno nesmí být prázdné";
      return $this->render('new_user');
    }

    if (!trim($params['user']['password'])) {
      $this->errors['password'] = "Heslo nesmí být prázdné";
      return $this->render('new_user');
    }

    if ($params['user']['password'] != $params['user']['password_confirmation']) {
      $this->errors['password'] = "Hesla se neshodují";
      return $this->render('new_user');
    }

    $user->hash_pass = sha1("{$params['user']['name']}:{$params['user']['password']}");
    R::store($user);

    return $this->redirect('admin_index_users');
  }

  public function edit_user($params) {
    $this->user = R::findOne('user', $params['id']);
    if($this->user->id == 0) {
      return $this->redirect('admin_index_users');
    }
  }

  public function update_user($params) {
    $user = R::findOne('user', $params['id']);

    if (!trim($params['user']['password'])) {
      $this->errors['password'] = "Heslo nesmí být prázdné";
      return $this->render('edit_user');
    }

    if ($params['user']['password'] != $params['user']['password_confirmation']) {
      $this->errors['password_confirmation'] = "Hesla se neshodují";
      return $this->render('edit_user');
    }

    $user->hash_pass = sha1("{$user->name}:{$params['user']['password']}");
    R::store($user);

    return $this->redirect('admin_index_users');
  }

  public function delete_user($params) {
    $user = R::load('user', $params['id']);
    R::trashAll($user->ownServer);
    R::trash($user);
    return $this->redirect('admin_index_users');
  }
}
