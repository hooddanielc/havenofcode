<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';
  include 'api/methods/article.php';
  include 'api/methods/comment.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();

      // try to get the article data on page load
      $article = new Article();
      $data = $article->get($_GET);
      if(!is_string($data)) {
        $this->addData('article', $data);
      } else {
        header('Location: 404.php');
        exit();
      }

      // try to get comment data on page load
      $comments = new Comment();
      $data = $comments->get([
        'article_id' => $_GET['id']
      ]);
      if(!is_string($data)) {
        $this->addData('comments', $data);
      } else {
        header('Location: 404.php');
        exit();
      }
    }

    public static $css = [
      'pages/article/article.css'
    ];

    public static $js = [
      'main/third-party/marked.js',
      'main/third-party/moment.js',
      'pages/article/article.js'
    ];

    public static $text = [
      'article' => 'pages/article/article.mustache',
      'comment-box' => 'pages/article/comment-box.mustache'
    ];
  }

  $page = new MyPage();
  $page->render();
?>