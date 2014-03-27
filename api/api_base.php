<?php
  abstract class ApiMethod {
    public static $api_method = 'your_method';
    public static $requires_authentication = false;
    abstract protected function post($request_data);
    abstract protected function get($request_data);

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
        $response = $this->post();
      } else {
        $response = $this->get();
      }

      if(is_string($response)) {
        return $this->execError($response);
      }
      return $this->execSuccess($response);
    }
  }
?>