app.modules.VideoStage = Backbone.View.extend({
  initialize: function(o) {
    this.renderData = o;
  },
  centerVideoInContainer: function() {
    // this function assumes the aspect
    // ratio of video is always 16:9
    var cWidth = this.$el.width();
    var cHeight = this.$el.height();
    var elVideo = this.$el.find('video');

    if((16 / 9) < (cWidth / cHeight)) {
      // make width 100% and center vertically
      elVideo.css({
        position: 'absolute',
        left: '0',
        top: '0',
        width: '100%',
        height: 'auto',
        marginTop: '-' + (((9 / 16 * cWidth) - cHeight) / 2) + 'px',
        marginLeft: '0'
      });
    } else {
      elVideo.css({
        position: 'absolute',
        left: '0',
        top: '0',
        width: 'auto',
        height: '100%',
        marginLeft: '-' + (((16 / 9 * cHeight) - cWidth) / 2) + 'px',
        marginTop: '0'
      });
    }
  },
  render: function() {
    this.$el.html(Mustache.render(app.mustache['video-stage'], this.renderData));
    this.$el.css({
      position: 'absolute',
      left: '0',
      top: '0',
      width: '100%',
      height: '100%'
    });
    this.elVideo = this.$el.find('video');
  }
});