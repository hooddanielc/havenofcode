app.modules.PageBaseView = Backbone.View.extend({
  events: {
    'click .fb-login': 'login',
    'click .fb-logout': 'setLoggedOut',
    'click .profile-dropdown': 'openDropdown'
  },
  login: function() {
    FB.login();
  },
  setLoggedIn: function() {
    var self = this;
    FB.api('/me', function(response) {
      self.$el.find('.profile-dropdown img').attr('src', 'http://graph.facebook.com/' + response.id + '/picture');
      self.$el.find('.log-out-wrap').show();
      self.$el.find('.log-in-wrap').hide();
      self.model.set('user', response);
    });
  },
  setLoggedOut: function() {
    FB.logout();
    this.$el.find('.log-out-wrap').hide();
    this.$el.find('.log-in-wrap').show();
    this.model.set('user', undefined);
  },
  openDropdown: function(e) {
    e.preventDefault();
    console.log('opening dropdown');
  },
  initFacebookSDK: function() {
    var self = this;
    window.fbAsyncInit = function() {
      FB.init({
        appId : '226946073983769',
        status : true, // check login status
        cookie : true, // enable cookies to allow the server to access the session
        xfbml : false  // parse XFBML
      });
      FB.Event.subscribe('auth.authResponseChange', function(response) {
        if (response.status === 'connected') {
          self.setLoggedIn();
        } else if (response.status === 'not_authorized') {
          self.setLoggedOut();
        } else {
          self.setLoggedOut();
        }
      });
    };

    (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
    }(document));
  },
  render: function() {
    this.initFacebookSDK();
    this.el.innerHTML += app.mustache['page-base'];
    this.renderPage();

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