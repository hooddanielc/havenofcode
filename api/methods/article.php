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
        if($con = $this->getDb()) {
          $stmt = $con->prepare(
            "INSERT INTO article (publish_date, id, github_id, title, description, md, youtube, published)"
            ." VALUES (NOW(), NULL, ?, ?, ?, ?, ?, ?)"
          );
          if($stmt) {
            $stmt->bind_param(
              "issssi",
              $params->github_id,
              $params->title,
              $params->description,
              $params->md,
              $params->youtube,
              $params->published
            );
            if($stmt->execute()) {
              $params->id = $con->insert_id;
              return $params;
            }
            return 'statement execution failed';
          }
         return 'statement preparation failed'; 
        }
        return 'error with database connection';
      }

      // update
      if($this->isAuthenticatedUser($params->github_id)) {
        if($con = $this->getDb()) {
          $stmt = $con->prepare(
            "UPDATE article"
            ." SET title=?,"
            ." description=?,"
            ." md=?,"
            ." youtube=?,"
            ." published=?,"
            ." modified_date=NOW()"
            ." WHERE id=?"
          );
          if($stmt) {
            $stmt->bind_param(
              "ssssii",
              $params->title,
              $params->description,
              $params->md,
              $params->youtube,
              $params->published,
              $params->id
            );
            if($stmt->execute()) {
              return [];
            }
            return 'mysql statement execute failed';
          }
          return 'statement preparation failed';
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

      if($con = $this->getDb()) {
        if($stmt = $con->prepare("SELECT * FROM article WHERE id=?")) {
          $stmt->bind_param("i", $params['id']);
          $result_set = [
            'id' => '',
            'github_id' => '',
            'title' => '',
            'description' => '',
            'md' => '',
            'youtube' => '',
            'published' => '',
            'publish_date' => '',
            'modified_date' => ''
          ];
          $stmt->bind_result(
            $result_set['id'],
            $result_set['github_id'],
            $result_set['title'],
            $result_set['description'],
            $result_set['md'],
            $result_set['youtube'],
            $result_set['published'],
            $result_set['publish_date'],
            $result_set['modified_date']
          );
          if($stmt->execute() && $stmt->fetch()) {
            return $result_set;
            if($result_set['published'] == 0 && !$this->isAuthenticatedUser($result_set['github_id'])) {
              return 'this article is private and only the signed in human is aloud to read';
            }
            return $result_set;
          }
          return 'query failed';
        }
        return 'statement preperation failed';
      }
      return 'error with database connection';
    }
  }
?>