<?php
  class ArticleList extends Module {
    public static $css = [
      'modules/article-list/article-list.css'
    ];

    public static $js = [
      'main/third-party/moment.js',
      'modules/article-list/article-list.js'
    ];

    public static $text = [
      'article-list' => 'modules/article-list/article-list.mustache'
    ];
  }
?>