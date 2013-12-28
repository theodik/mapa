<?php

class AdminController extends ApplicationController {
  public function before_action($params) {
    $id = $params->session()['user_id'];
    $this->current_user = R::load('user', $id);
    if ($this->current_user->id == 0) {
      return $this->redirect('admin_new_sessions');
    }

    $this->title = "administrace";
  }
}
