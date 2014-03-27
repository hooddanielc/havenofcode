<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';
  include 'modules/video_sequencer.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();
      $this->video_sequencer = new VideoSequencer();
    }

    public static $js = [
      'pages/home-page/home-page.js'
    ];
  }

  $page = new MyPage();
  $page->render();
?>