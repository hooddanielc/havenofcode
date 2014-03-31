<?php
  include $_SERVER['DOCUMENT_ROOT'].'/config.php';

  abstract class ApiMethod {
    public static $api_method = 'your_method';
    public static $requires_authentication = false;
    abstract public function post($request_data);
    abstract public function get($request_data);

    protected function checkRequiredParams($keys, $params) {
      foreach($keys as &$key) {
        if(is_array($params)) {
          if(!array_key_exists($key, $params)) {
            return false;
          }
        } else {
          if(!property_exists($params, $key)) {
            return false;
          }
        }
      }
      return true;
    }

    protected function getDb() {
      return mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    protected function isAuthenticatedUser($github_id) {
      session_start();
      if(isset($_SESSION['access_token'])) {
        $token = $_SESSION['access_token']; 
        ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
        $result = file_get_contents('https://api.github.com/user?access_token='.$token);
        $json = json_decode($result);
        if($github_id == $json->id) {
          return $json;
        }
      }
      return false;
    }

    protected function isAuthenticated() {
      // verify if they
      // have session id?
      session_start();
      return isset($_SESSION['access_token']);
    }

    protected function execError($error_msg) {
      return [
        'success' => false,
        'error' => $error_msg
      ];
    }

    protected function execSuccess($data) {
      return [
        'success' => true,
        'data' => $data
      ];
    }

    public function exec($post, $data) {

      // does method require authentication?
      if($this::$requires_authentication && !$this->isAuthenticated()) {
        return $this->execError('method requires an user authenticated session');
      }

      // result a formated object for json
      $response;
      if($post) {
        $response = $this->post($data);
      } else {
        $response = $this->get($data);
      }

      if(is_string($response)) {
        return $this->execError($response);
      }
      return $this->execSuccess($response);
    }
  }
?>