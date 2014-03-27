app.modules.PageBaseView = Backbone.View.extend({
  events: {
    'click .fb-login': 'login',
    'click .fb-logout': 'logout',
    'click .profile-dropdown': 'openDropdown'
  },
  login: function(e) {
    e.preventDefault();
    window.location = '/oauth_login.php';
  },
  logout: function(e) {
    e.preventDefault();
    window.location = '/logout.php';
  },
  setLoggedIn: function(user) {
    var self = this;
    var me = this.model.get('user');
    self.$el.find('.profile-dropdown img').attr('src', me.avatar_url);
    self.$el.find('.log-out-wrap').show();
    self.$el.find('.log-in-wrap').hide();
  },
  openDropdown: function(e) {
    e.preventDefault();
    console.log('opening dropdown');
  },
  render: function() {
    window.the_page = this;

    this.el.innerHTML += app.mustache['page-base'];
    this.renderPage();

    // check user data
    if(app.data.user) {
      this.model.set('user', app.data.user);
      this.setLoggedIn(app.data.user);
    }

    // initialize resize event
    var self = this;
    $(window).resize(function() {
      self.resize();
    });
    self.resize();
  },
  supports_video: function() {
    return !!document.createElement('video').canPlayType;
  },
  // abstract void
  resize: function() {},
  // abstract void
  renderPage: function() {}
});