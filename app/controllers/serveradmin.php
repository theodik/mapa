<?php

class ServeradminController extends AdminController {
  public function index($params) {
    $this->servers = R::find('server');
  }

  public function new_server($params) {
    $this->server = dispense('server', $params);
  }

  public function create_server($params) {
    $this->server = dispense('server', $params);
    $this->server->user = $this->current_user;
    if(filter_var($this->server->url, FILTER_VALIDATE_URL)){
      R::store($this->server);
      return $this->redirect('admin_index');
    } else {
      $this->errors = array('url' => 'Špatná adresa');
      return $this->render('new_server');
    }
  }

  public function edit_server($params) {
    $this->server = R::findOne('server', 'id = ? AND user_id = ?', array($params['id'], $this->current_user->id));
    if($this->server->id == 0) {
      return $this->redirect('admin_index');
    }
  }

  public function update_server($params) {
    $this->server = R::findOne('server', 'id = ? AND user_id = ?', array($params['id'], $this->current_user->id));
    if($this->server == null) {
      return $this->redirect('admin_index');
    }

    fill_attributes($this->server, $params);
    if(filter_var($this->server->url, FILTER_VALIDATE_URL)){
      R::store($this->server);
      return $this->redirect('admin_index');
    } else {
      $this->errors = array('url' => 'Špatná adresa');
      return $this->render('edit_server');
    }
  }

  public function delete_server($params) {
    $this->server = R::findOne('server', 'id = ? AND user_id = ?', array($params['id'], $this->current_user->id));
    R::trash($this->server);
    return $this->redirect('admin_index');
  }
}
