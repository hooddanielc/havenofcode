<?php
  class PageBase extends Module {
    public static $css = [
      'http://fonts.googleapis.com/css?family=Muli',
      'modules/page-base/page-base.css'
    ];

    public static $js = [
      'main/third-party/jquery.min.js',
      'main/third-party/underscore.min.js',
      'main/third-party/backbone.min.js',
      'main/third-party/mustache.min.js',
      'modules/page-base/page-base.js'
    ];

    public static $text = [
      'page-base' => 'modules/page-base/page-base.mustache'
    ];
  }
?>