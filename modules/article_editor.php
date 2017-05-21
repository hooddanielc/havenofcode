<?php
  include 'markdown_editor.php';

  class ArticleEditor extends Module {

    function __construct() {
      $this->markdown_editor = new MarkdownEditor();
    }

    public static $js = [
      'modules/article-editor/article-editor.js'
    ];

    public static $text = [
      'article-editor' => 'modules/article-editor/article-editor.mustache'
    ];
  }
?>