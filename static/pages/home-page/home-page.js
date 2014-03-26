app.modules.VideoSequencerNav = Backbone.View.extend({
  initialize: function() {

  },
  render: function() {
    
  }
});

app.modules.Page = app.modules.PageBaseView.extend({
  // render video background
  resize: function() {
    this.videoSequencerBackground.centerAllVideos();
  },
  renderBackground: function() {
    // renders the start of the sequencer background
    var el = $('<div/>');
    this.$el.find('.content-background').append(el);
    this.videoSequencerBackground = new app.modules.VideoSequencer({
      el: el
    });
    this.videoSequencerBackground.render();
    this.videoSequencerBackground.playAndLoop(
      'static/video/scene-1/cray.mp4',
      'static/video/scene-1/cray.webm',
      'static/video/scene-1/cray.ogg'
    );
  },
  renderVideoSequencerNav: function() {
    var self = this;
    $(document.body).on('click', function() {
      self.videoSequencerBackground.flash(
        'static/video/scene-hello/hi.mp4',
        'static/video/scene-hello/hi.webm',
        'static/video/scene-hello/hi.ogg'
      )
    });
  },
  renderBoringSite: function() {
    // TODO:
    // render a message that explains
    // they should get a better browser
    alert('sorry, this website only works on HTML5 enabled browsers :-(');
  },
  renderAwesomeSite: function() {
    this.renderBackground();
    this.renderVideoSequencerNav();
  },
  renderPage: function() {
    if(!this.supports_video) {
      this.renderBoringSite();
    } else {
      this.renderAwesomeSite();
    }
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();