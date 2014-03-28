<?php

  if(!class_exists('ApiMethod')) {
    include $_SERVER['DOCUMENT_ROOT'].'/api/api_base.php';
  }

  /*
  * create / update article in database
  * omit id for creating a new row
  * * * * */
  class Comment extends ApiMethod {
    public static $requires_authentication = true;
    public static $api_method = 'comment';

    /*
    * comment / reply to comment / delete comment on article
    * * * * * * * * * * * * * */
    // omit comment_id to post a comment
    // use id to delete comment
    // {
    //   'method': 'comment',
    //   'data': {
    //     'github_id': '1351734',
    //     'article_id': 1,
    //     'comment_text': 'Nice article',
    //     'comment_foreign': null
    //   }
    // }
    // delete comment
    // {
    //   'method': 'comment',
    //   'data': {
    //     'github_id': '1351734',
    //     'comment_id': '1'
    //   }
    // }
    public function post($params) {

      $required_params = [
        'github_id'
      ];

      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'github_id parameter is required';
      }

      if(!$this->isAuthenticatedUser($params->github_id)) {
        return 'you can not post or delete comments on behalf of another person';
      }

      if(isset($params->article_id)) {
        // createing a new comment
        $required_params[] = 'article_id';
        $required_params[] = 'comment_text';
        $required_params[] = 'comment_foreign';
        if($this->checkRequiredParams($required_params, $params)) {
          $con = $this->getDb();
          if($con) {
            $comment_foreign = property_exists($params, 'comment_foreign') && isset($params->comment_foreign) ? $params->comment_foreign : 'NULL';
            $sql = "INSERT INTO article_comment (comment_id, comment_foreign, article_id, comment_deleted, github_id, comment_text)"
              ." VALUES (NULL, ".$comment_foreign.", ".$params->article_id.", 0, ".$params->github_id.", '".$con->real_escape_string($params->comment_text)."')";
            if($con->query($sql)) {
              $params->comment_id = $con->insert_id;
              return $params;
            }
            return 'insert failed, '.$con->error;
          }
          return 'error with database connection';
        }
        return 'missing required parameters';
      }
      // deleteing a comment
      $required_params[] = 'comment_id';
      if($this->checkRequiredParams($required_params, $params)) {
        $con = $this->getDb();
        if($con) {
          $sql = 'UPDATE article_comment SET comment_deleted=1 WHERE comment_id='.$params->comment_id;
          if($con->query($sql)) {
            return [
              'deleted' => $params->comment_id
            ];
          }
          return 'insert failed, '.$con->error;
        }
      }
      return 'comment_id is required to delete comment';
    }

    /*
    * gets one article row
    * * * * * * * * * */
    public function get($params) {
      $required_params = [
        'article_id'
      ];
      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'required parameters missing (article_id)';
      }
      $sql = "SELECT * FROM article_comment WHERE article_id=".$params['article_id']." ORDER BY comment_id ASC";
      $con = $this->getDb();
      if($con) {
        if($result = $con->query($sql)) {
          $rows = [];
          $return_obj = [];
          while($row = $result->fetch_assoc()) {
            $rows[$row['comment_id']] = $row;
            if($row['comment_deleted']) {
              $row['comment_text'] = '';
            }
            if(isset($row['comment_foreign'])) {
              $row['in_reply_to'] = $rows[$row['comment_foreign']];
            }
            $return_obj[] = $row;
          }
          return $return_obj;
        }
        return 'query failed';
      }
      return 'error with database connection';
    }
  }
?>