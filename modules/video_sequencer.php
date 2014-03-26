<?php
  include 'video_stage.php';
  class VideoSequencer extends Module {

    function __construct() {
      $this->video_stage = new VideoStage();
    }

    public static $js = [
      'modules/video-sequencer/video-sequencer.js',
    ];
  }
?>