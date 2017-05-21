/*
* create by Daniel Hood (http://www.havenofcode.com)
*
* This class is responsible for manageing
* transitions between multiple videos 
* inside a resizeable container.
* The desired effect of video transitions
* should not be choppy or noticeable.
*
* events
* activity - fires when something is happening
* loading - fires when something is loading
* * * * * * * * * * * * * * * * * * * * * * * * * */

app.modules.VideoSequencer = Backbone.View.extend({
  /*
  * When true, unable to do anything
  * to the nav
  * * * */
  locked: false,

  /*
  * all videos currently added to stage
  * * * */
  videos: [],

  /*
  * locks the stage
  * * * */
  lockStage: function() {
    if(this.locked) {
      return false;
    }
    this.locked = true;
    this.trigger('lock_change', this.locked);
    return true;
  },

  /*
  * unlocks the stage
  * * * */
  _unlockStage: function() {
    if(this.locked) {
      this.locked = false; 
      this.trigger('lock_change', this.locked);
      return true
    }
    return false;
  },

  /*
  * removes all videos
  * * * */
  resetStage: function() {
    this.trigger('loading');
    for(var x in this.videos) {
      this.videos[x].remove();
    }
    this.videos = [];
  },

  /*
  * resets stage and plays
  * and loops one video
  * params (mp4 url, webm url)
  * * * */
  playAndLoop: function(mp4_url, webm_url, ogg_url) {
    this.resetStage();
    var el = $('<div/>');
    this.$el.append(el);
    this.videos[0] = new app.modules.VideoStage({
      el: el,
      mp4_url: mp4_url,
      webm_url: webm_url,
      ogg_url: ogg_url,
      autoplay: true,
      loop: true
    });
    this.videos[0].render();
    this.videos[0].elVideo.hide();
    var self = this;
    this.videos[0].elVideo.one('play', function() {
      self.videos[0].elVideo.show();
      self.videos[0].centerVideoInContainer();
      self.trigger('activity');
    });
  },

  /*
  * preload video and flash in
  * * * */
  flash: function(mp4_url, webm_url, ogg_url) {
    if(this.lockStage()) {
      var el = $('<div/>');
      this.$el.prepend(el);
      var video = new app.modules.VideoStage({
        el: el,
        mp4_url: mp4_url,
        webm_url: webm_url,
        ogg_url: ogg_url
      });
      video.render();
      var self = this;
      video.elVideo.on('playing', function() {
        self.videos[0].elVideo.fadeOut();
        self.videos.push(video);
      });
      video.elVideo.on('timeupdate', function(e) {
        if((video.elVideo[0].duration * 1000) - (video.elVideo[0].currentTime * 1000) <= 1000) {
          video.elVideo.fadeOut();
          self.videos[0].elVideo.fadeIn();
        }
      });
      video.elVideo.on('ended', function(e) {
        self._unlockStage();
        video.remove();
        video = null;
        delete self.videos[1];
      });
      video.centerVideoInContainer();
      video.elVideo[0].play();
    }
  },


  /*
  * center all videos in container
  * * * */
  centerAllVideos: function() {
    for(var x in this.videos) {
      this.videos[x].centerVideoInContainer();
    }
  },

  /*
  * always call render on a backbone view
  * * * */
  render: function() {
    this.$el.css({
      position: 'absolute',
      left: '0',
      top: '0',
      width: '100%',
      height: '100%',
      overflow: 'hidden'
    });
  }
});