<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';
  include 'api/methods/article.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();

      // try to get the article data
      $article = new Article();
      $data = $article->get($_GET);
      if(!is_string($data)) {
        $this->addData('article', $data);
      } else {
        header('Location: 404.php');
        exit();
      }
    }

    public static $js = [
      'main/third-party/marked.js',
      'pages/article/article.js'
    ];
  }

  $page = new MyPage();
  $page->render();
?>