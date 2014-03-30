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
        $sql = "SELECT id, title, description, github_id FROM article WHERE published=1 ORDER BY id DESC";

        $con = $this->getDb();
        if($con) {
          $result = $con->query($sql);
          if($result) {
            $return_obj = [
              'articles' => []
            ];
            while($row = $result->fetch_assoc()) {
              $return_obj['articles'][] = $row;
              $rows[$row['comment_id']] = $row;
            }
            return $return_obj;
          }
          return 'query failed';
        }
        return 'error with database connection';
      }

      return 'unknown type '.$params['type'];
    }
  }
?>