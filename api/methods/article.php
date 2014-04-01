<?php

  if(!class_exists('ApiMethod')) {
    include $_SERVER['DOCUMENT_ROOT'].'/api/api_base.php';
  }

  /*
  * create / update article in database
  * omit id for creating a new row
  * * * * */
  class Article extends ApiMethod {
    public static $requires_authentication = false;
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
        $con = $this->getDb();

        if($con) {
          $sql = "INSERT INTO article (publish_date, id, github_id, title, description, md, youtube, repo, published)"
            ." VALUES (NOW(), NULL, '".$params->github_id."', '".$con->real_escape_string($params->title)."',"
            ." '".$con->real_escape_string($params->description)."','".$con->real_escape_string($params->md)."', '".$params->youtube."',"
            ." '".$params->repo."', '".$params->published."')";

          if($con->query($sql)) {
            $params->id = $con->insert_id;
            return $params;
          }
          return 'insert failed, '.$con->error;
        }
        return 'error with database connection';
      }

      if($this->isAuthenticatedUser($params->github_id)) {
        $con = $this->getDb();
        if($con) {
          $sql = "UPDATE article"
            ." SET title='".$con->real_escape_string($params->title)."',"
            ." description='".$con->real_escape_string($params->description)."',"
            ." md='".$con->real_escape_string($params->md)."',"
            ." youtube='".$con->real_escape_string($params->youtube)."',"
            ." repo='".$con->real_escape_string($params->repo)."',"
            ." published=".$params->published.","
            ." modified_date=NOW()"
            ." WHERE id=".$params->id;

          if($con->query($sql)) {
            return [];
          }
          return 'update failed, '.$con->error;
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

      $sql = "SELECT * FROM article WHERE id=".$params['id'];

      $con = $this->getDb();
      if($con) {
        if($result = $con->query($sql)) {
          $row = $result->fetch_assoc();
          if($row['published'] == 0 && !$this->isAuthenticatedUser($row['github_id'])) {
            return 'this article is private and only the signed in human is aloud to read';
          }
          return $row;
        }
        return 'query failed';
      }
      return 'error with database connection';
    }
  }
?>