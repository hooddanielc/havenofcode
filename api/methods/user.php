<?php

  if(!class_exists('ApiMethod')) {
    include $_SERVER['DOCUMENT_ROOT'].'/api/api_base.php';
  }

  /*
  * creates new user in database
  * given an github user id is provided, 
  * and the user session token matches github id
  * * * * */
  class User extends ApiMethod {
    public static $requires_authentication = true;
    public static $api_method = 'user';

    /*
    * create new user in database if it exists
    * * * * */
    public function post($params) {
      $sql = 'INSERT INTO users'
      .' (github_id)'
      .' SELECT '.$params['github_id']
      .'  FROM dual'
      .' WHERE NOT EXISTS (SELECT *'
      .'  FROM users'
      .' WHERE users.github_id = '.$params['github_id'].')';

      if($this->isAuthenticatedUser($params['github_id'])) {
        $con = $this->getDb();
        if($con) {
          if($con->query($sql)) {
            return [];
          }
          return 'user created failed, '.$con->error;
        }
        return 'error with database connection';
      }
      return 'user creation requires that user to be logged in';
    }

    /*
    * get not used
    * * * * * * * * * */
    public function get($params) {
      return [];
    }
  }
?>