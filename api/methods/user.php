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
    public static $requires_authentication = false;
    public static $api_method = 'user';

    /*
    * create new user in database if it exists
    * * * * */
    public function post($params) {
      if($user = $this->isAuthenticatedUser($params['github_id'])) {
        if($con = $this->getDb()) {
          $stmt = $con->prepare(
            "INSERT INTO users (github_id, user_cache)"
            ." VALUES (?, ?)"
            ." ON DUPLICATE KEY UPDATE user_cache=?"
          );
          if($stmt) {
            $user_json = json_encode($user);
            $stmt->bind_param(
              "iss",
              $params['github_id'],
              $user_json,
              $user_json
            );
            if($stmt->execute()) {
              return [];
            }
            return 'statement execution failed';
          }
          return 'statement preparation failed';
        }
        return 'error with database connection';
      }
      return 'user creation requires that user to be logged in';
    }

    /*
    * get used to get cached
    * user data from github
    * * * * * * * * * */
    public function get($params) {
      $required_params = [
        'github_id'
      ];
      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'User id is required';
      }
      if($con = $this->getDb()) {
        if($stmt = $con->prepare("SELECT * FROM users WHERE github_id=?")) {
          $stmt->bind_param("i", $params['github_id']);
          $result_set = [
            'github_id' => '',
            'user_cache' => ''
          ];
          $stmt->bind_result(
            $result_set['github_id'],
            $result_set['user_cache']
          );
          if($stmt->execute() && $stmt->fetch()) {
            return [
              'user' => json_decode($result_set['user_cache'])
            ];
          }
          return 'user does not exists';
        }
        return 'statement preparation failed';
      }
      return 'error with database connection';
    }
  }
?>