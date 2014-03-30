<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';
  include 'modules/article_list.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();
      $this->article_list = new ArticleList();
    }

    public static $js = [
      'pages/home-page/home-page.js'
    ];
  }

  $page = new MyPage();
  $page->render();
?>