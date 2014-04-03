<?php

  if(!class_exists('ApiMethod')) {
    include $_SERVER['DOCUMENT_ROOT'].'/api/api_base.php';
  }

  /*
  * create / update article in database
  * omit id for creating a new row
  * * * * */
  class ArticleList extends ApiMethod {
    public static $api_method = 'article_list';

    public function post($params) {
      return 'posting to this method is not aloud';
    }

    /*
    * gets one article row
    * * * * * * * * * */
    public function get($params) {
      $required_params = [
        'type',
        'start',
        'rows'
      ];
      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'required parameters missing (type)';
      }
      if($params['type'] == 'latest') {
        if($con = $this->getDb()) {
          $stmt = $con->prepare(
            "SELECT article.publish_date, article.modified_date, article.id, article.title, article.description, article.github_id, users.user_cache FROM article"
            ." INNER JOIN users"
            ." ON article.github_id=users.github_id"
            ." WHERE article.published=1 ORDER BY article.id DESC LIMIT ?, ?"
          );
          if($stmt) {
            $stmt->bind_param(
              'ii',
              $params['start'],
              $params['rows']
            );
            $result_set = [
              'publish_date' => '',
              'modified_date' => '',
              'id' => '',
              'title' => '',
              'description' => '',
              'github_id' => '',
              'user_cache' => '',
            ];
            $stmt->bind_result(
              $result_set['publish_date'],
              $result_set['modified_date'],
              $result_set['id'],
              $result_set['title'],
              $result_set['description'],
              $result_set['github_id'],
              $result_set['user_cache']
            );
            if($stmt->execute()) {
              $return_obj = [
                'articles' => []
              ];
              while($stmt->fetch()) {
                $row = [];
                foreach ($result_set as $key => $val) {
                  $row[$key] = $val;
                }
                $user_data = json_decode($row['user_cache']);
                $return_obj['articles'][] = [
                  'id' => $row['id'],
                  'title' => $row['title'],
                  'description' => $row['description'],
                  'github_id' => $row['github_id'],
                  'user' => $user_data,
                  'modified_date' => $row['modified_date'],
                  'publish_date' => $row['publish_date']
                ];
              }
              return $return_obj;
            }
            return 'statement execution failed';
          }
          return 'statement preparation failed';
        }
        return 'error with database connection';
      } else if($params['type'] == 'user') {
        $required_params = [
          'github_id'
        ];
        if(!$this->checkRequiredParams($required_params, $params)) {
          return 'required parameters missing (github_id)';
        }
        if($con = $this->getDb()) {
          $stmt = $con->prepare(
            "SELECT article.published, article.publish_date, article.modified_date, article.id, article.title, article.description, article.github_id, users.user_cache FROM article"
            ." INNER JOIN users"
            ." ON article.github_id=users.github_id"
            ." WHERE article.github_id=? ORDER BY article.id DESC LIMIT ?, ?"
          );
          if($stmt) {
            $stmt->bind_param(
              "iii",
              $params['github_id'],
              $params['start'],
              $params['rows']
            );
            $result_set = [
              'published' => '',
              'publish_date' => '',
              'modified_date' => '',
              'id' => '',
              'title' => '',
              'description' => '',
              'github_id' => '',
              'user_cache' => '',
            ];
            $stmt->bind_result(
              $result_set['published'],
              $result_set['publish_date'],
              $result_set['modified_date'],
              $result_set['id'],
              $result_set['title'],
              $result_set['description'],
              $result_set['github_id'],
              $result_set['user_cache']
            );
            if($stmt->execute()) {
              // check if github_id is
              // isAuthenticatedUser user
              $authenticated_user = $this->isAuthenticatedUser($params['github_id']);
              $return_obj = [
                'articles' => []
              ];
              while($stmt->fetch()) {
                // can this user see unpublished post?
                if($result_set['published'] == 0 && !$authenticated_user) {
                  continue;
                }
                $row = [];
                foreach ($result_set as $key => $val) {
                  $row[$key] = $val;
                }
                $user_data = json_decode($row['user_cache']);
                $return_obj['articles'][] = [
                  'id' => $row['id'],
                  'title' => $row['title'],
                  'description' => $row['description'],
                  'github_id' => $row['github_id'],
                  'user' => $user_data,
                  'modified_date' => $row['modified_date'],
                  'publish_date' => $row['publish_date']
                ];
              }
              return $return_obj;
            }
            return 'statement execution failed';
          }
          return 'statement preparation failed';
        }
        return 'error with database connection';
      }
      return 'unknown type ('.$params['type'].')';
    }
  }
?>