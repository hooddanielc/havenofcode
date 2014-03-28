<?php

  if(!class_exists('ApiMethod')) {
    include $_SERVER['DOCUMENT_ROOT'].'/api/api_base.php';
  }

  /*
  * create / update article in database
  * omit id for creating a new row
  * * * * */
  class Article extends ApiMethod {
    public static $requires_authentication = true;
    public static $api_method = 'article';

    /*
    * create / update article
    * * * * * * * * * * * * * */
    // if new, no article id should be provided
    // {
    //   'method': 'article',
    //   'data': {
    //     'github_id': '1351734',
    //     'id': 1,
    //     'title': 'New title name',
    //     'description': 'New article description',
    //     'md': 'no md here',
    //     'youtube': '',
    //     'repo': ''
    //   }
    // }
    public function post($params) {
      $required_params = [
        'github_id',
        'title',
        'description',
        'md',
        'youtube',
        'repo',
        'published'
      ];
      // check required parameters
      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'Error: required parameters missing';
      }

      // check to see if data is all there
      $update = isset($params->id);

      if(!$update) {
        // insert
        $sql = "INSERT INTO article (id, github_id, title, description, md, youtube, repo, published)"
          ." VALUES (NULL, '".$params->github_id."', '".$params->title."',"
          ." '".$params->description."','".$params->md."', '".$params->youtube."',"
          ." '".$params->repo."', '".$params->published."')";
        
        $con = $this->getDb();
        if($con) {
          if($con->query($sql)) {
            $params->id = $con->insert_id;
            return $params;
          }
          return 'insert failed, '.$con->error;
        }
        return 'error with database connection';
      }

      $sql = "UPDATE article"
        ." SET title='".$params->title."',"
        ." description='".$params->description."',"
        ." md='".$params->md."',"
        ." youtube='".$params->youtube."',"
        ." repo='".$params->repo."',"
        ." published=".$params->published
        ." WHERE id=".$params->id;

      if($this->isAuthenticatedUser($params->github_id)) {
        $con = $this->getDb();
        if($con) {
          if($con->query($sql)) {
            return [];
          }
          return 'insert failed, '.$con->error;
        }
        return 'error with database connection';
      }
      return 'user creation requires that user to be logged in';
    }

    /*
    * gets one article row
    * * * * * * * * * */
    public function get($params) {
      $required_params = [
        'id'
      ];
      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'required parameters missing (id)';
      }

      $sql = "SELECT * FROM article WHERE id=".$params['id']." AND published=1";

      $con = $this->getDb();
      if($con) {
        $result = $con->query($sql);
        if($result) {
          return $result->fetch_assoc();
        }
        return 'query failed';
      }
      return 'error with database connection';
    }
  }
?>