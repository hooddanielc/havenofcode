<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';
  include 'modules/article_list.php';
  include 'api/methods/user.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();
      $this->article_list = new ArticleList();

      // add user data to page
      $user = new User();
      $user_data = $user->get([
        'github_id' => $_GET['id']
      ]);
      if(is_string($user_data)) {
        header("Location: 404.php");
        exit();
      } else {
        $this->addData('user_profile', $user_data['user']);
      }
    }

    public static $js = [
      'pages/profile/profile.js'
    ];

    public static $css = [
      'pages/profile/profile.css'
    ];

    public static $text = [
      'profile' => 'pages/profile/profile.mustache'
    ];
  }

  $page = new MyPage();
  $page->render();
?>