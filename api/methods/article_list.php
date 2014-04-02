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
        'type'
      ];
      if(!$this->checkRequiredParams($required_params, $params)) {
        return 'required parameters missing (type)';
      }

      if($params['type'] == 'latest') {
        $sql = "SELECT article.publish_date, article.modified_date, article.id, article.title, article.description, article.github_id, users.github_id, users.user_cache FROM article"
        ." INNER JOIN users"
        ." ON article.github_id=users.github_id"
        ." WHERE article.published=1 ORDER BY article.id DESC";

        if($con = $this->getDb()) {
          if($result = $con->query($sql)) {
            $return_obj = [
              'articles' => []
            ];
            while($row = $result->fetch_assoc()) {
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
          return 'query failed';
        }
        return 'error with database connection';
      } else if($params['type'] == 'user') {
        $required_params = [
          'github_id'
        ];
        if(!$this->checkRequiredParams($required_params, $params)) {
          return 'required parameters missing (github_id)';
        }
        $sql = "SELECT article.published, article.publish_date, article.modified_date, article.id, article.title, article.description, article.github_id, users.github_id, users.user_cache FROM article"
        ." INNER JOIN users"
        ." ON article.github_id=users.github_id"
        ." WHERE article.github_id=".$params['github_id']." ORDER BY article.id DESC";

        if($con = $this->getDb()) {
          if($result = $con->query($sql)) {
            // check if github_id is
            // isAuthenticatedUser user
            $authenticated_user = $this->isAuthenticatedUser($params['github_id']);

            $return_obj = [
              'articles' => []
            ];
            while($row = $result->fetch_assoc()) {
              // can this user see unpublished post?
              if($row['published'] == 0 && !$authenticated_user) {
                continue;
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
          return 'something wrong with query';
        }
        return 'error with database connection';
      }

      return 'unknown type '.$params['type'];
    }
  }
?>