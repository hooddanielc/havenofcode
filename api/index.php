<?php

  // include all methods from methods folder/package
  foreach (glob("methods/*.php") as $filename) {
    include $filename;
  }

  /*
  * json POST body must match the following format
  * 
  * {
  *  "data":{
  *     "params1":1,
  *     "params2":2
  *  },
  *  "method":"your_method"
  * }
  *
  * GET request should look like the following
  *
  * /api?method=method_name&param1=1&param2=2
  *
  * put your method class in methods folder
  * * * * * * * * * * * * * * * * * * * * * */

  $classes = get_declared_classes();
  $method;
  $post = false;
  $method_params;

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get response body
    // and decode the json
    $post = true;
    $data = json_decode(file_get_contents('php://input'));
    if($data) {
      $method = $data->method;
      $method_params = $data->data;
    } else {
      die('json is incorrect or malformed');
    }
  } else {
    $method = $_GET['method'];
    $method_params = $_GET;
  }

  if(isset($method)) {
    // find the class
    foreach($classes as &$cls) {
      if(isset($cls::$api_method) && $cls::$api_method === $method) {
        if(class_exists($cls)) {
          $instance = new $cls($method_params);
          $response = $instance->exec($post, $method_params);
          echo json_encode($response);
          exit();
        }
      }
    }
    // send an error response
    exit('todo: send error response in agreed response format.');
  } else {
    // method parameter does not exist
    exit('todo: send error response in agreed response format.');
  }

?>