<?php
  class MarkdownEditor extends Module {
    public static $css = [
      'main/third-party/code-mirror/codemirror.css',
      'modules/markdown-editor/markdown-editor.css'
    ];

    public static $js = [
      'main/third-party/code-mirror/codemirror.js',
      'main/third-party/marked.js',
      'modules/markdown-editor/markdown-editor.js'
    ];

    public static $text = [
      'markdown-editor' => 'modules/markdown-editor/markdown-editor.mustache'
    ];
  }
?>