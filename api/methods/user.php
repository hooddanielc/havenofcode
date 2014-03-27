<?php

  include $_SERVER['DOCUMENT_ROOT'].'/api/api_base.php';

  class User extends ApiMethod {
    public static $requires_authentication = true;
    public static $api_method = 'user';

    protected function post($params) {
      return [];
    }

    protected function get($params) {
      return [];
    }
  }
?>