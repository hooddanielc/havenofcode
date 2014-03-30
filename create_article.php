<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();
    }

    public static $js = [
      'pages/create-article/create-article.js'
    ];
  }

  $page = new MyPage();
  $page->render();
?>