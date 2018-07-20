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
      $this->addData('error', $_GET['error']);
      $this->addData('error_description', $_GET['error_description']);
      $this->addData('error_uri', $_GET['error_uri']);
    }

    public static $js = [
      'pages/login-error/login-error.js'
    ];

    public static $text = [
      'login-error' => 'pages/login-error/login-error.mustache'
    ];
  }

  $page = new MyPage();
  $page->render();
?>
