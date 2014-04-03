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
        // we are createing a new comment
        $required_params[] = 'article_id';
        $required_params[] = 'comment_text';
        $required_params[] = 'comment_foreign';
        if($this->checkRequiredParams($required_params, $params)) {
          if($con = $this->getDb()) {
            $comment_foreign = property_exists($params, 'comment_foreign') && isset($params->comment_foreign) ? $params->comment_foreign : null;
            $stmt = $con->prepare(
              "INSERT INTO article_comment (timestamp, comment_id, comment_foreign, article_id, comment_deleted, github_id, comment_text)"
              ." VALUES (NOW(), NULL, ?, ?, 0, ?, ?)"
            );
            if($stmt) {
              $stmt->bind_param(
                "iiis",
                $comment_foreign,
                $params->article_id,
                $params->github_id,
                $params->comment_text
              );
              if($stmt->execute()) {
                $params->comment_id = $con->insert_id;
                return $params;
              }
              return 'statement execution failed '.$stmt->error;
            }
            return 'statement preparation failed';
          }
          return 'error with database connection';
        }
        return 'missing required parameters';
      }

      // we are deleteing a comment
      $required_params[] = 'comment_id';
      if($this->checkRequiredParams($required_params, $params)) {
        if($con = $this->getDb()) {
          if($stmt = $con->prepare('UPDATE article_comment SET comment_deleted=1 WHERE comment_id=?')) {
            $stmt->bind_param('i', $params->comment_id);
            if($stmt->execute()) {
              return [
                'deleted' => $params->comment_id
              ];
            }
            return 'statement execution failed';
          }
          return 'statement preparation failed';
        }
        return 'database connection failed';
      }
      return 'comment_id is required to delete comment';
    }

    /*
    * gets comments with article id
    * * * * * * * * * */
    public function get($params) {
      $required_params = [
        'article_id'
      ];
      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'required parameters missing (article_id)';
      }
      if($con = $this->getDb()) {
        $stmt = $con->prepare(
          "SELECT article_comment.*, users.user_cache FROM article_comment"
          ." INNER JOIN users ON article_comment.github_id=users.github_id"
          ." WHERE article_id=? ORDER BY comment_id ASC"
        );
        if($stmt) {
          $stmt->bind_param("i", intval($params['article_id']));
          $result_set = [
            'comment_id' => '',
            'comment_foreign' => '',
            'comment_text' => '',
            'article_id' => '',
            'comment_deleted' => '',
            'github_id' => '',
            'timestamp' => '',
            'user_cache' => ''
          ];
          $stmt->bind_result(
            $result_set['comment_id'],
            $result_set['comment_foreign'],
            $result_set['comment_text'],
            $result_set['article_id'],
            $result_set['comment_deleted'],
            $result_set['github_id'],
            $result_set['timestamp'],
            $result_set['user_cache']
          );
          if($stmt->execute()) {
            $return_obj = [];
            while($stmt->fetch()) {
              $row = [];
              foreach($result_set as $key => $val) {
                $row[$key] = $val;
              }
              $row['user_cache'] = json_decode($row['user_cache']);
              $rows[$row['comment_id']] = $row;
              if($row['comment_deleted']) {
                $row['comment_text'] = '';
              }
              if(isset($row['comment_foreign'])) {
                $row['in_reply_to'] = $rows[$row['comment_foreign']];
              }
              array_unshift($return_obj, $row);
            }
            return $return_obj;
          }
          return 'statement execution failed  dangit  '.mysqli_stmt_error($stmt);
        }
        return 'statement preparation failed';
      }
      return 'error with database connection';
    }
  }
?>