<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';
  include 'modules/article_editor.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();
      $this->article_editor = new ArticleEditor();
    }

    public static $js = [
      'pages/create-article/create-article.js'
    ];

    public static $text = [
      'create-article' => 'pages/create-article/create-article.mustache'
    ];
  }

  $page = new MyPage();
  $page->render();
?>