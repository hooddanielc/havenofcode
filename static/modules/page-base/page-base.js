app.modules.PageUserNav = Backbone.View.extend({
  events: {
    'click .page-user-logout': 'logout',
    'submit .page-base-search-form': 'search'
  },
  search: function(e) {
    e.preventDefault();
    var value = this.$el.find('.page-base-input').val();
    if(value != '') {
      window.location = '/search.php?q=' + value;
    }
  },
  logout: function() {
    window.location = '/logout.php';
  },
  render: function() {
    this.$el.html(Mustache.render(app.mustache['page-user-nav'], this.model.attributes));
    if(app.data.get.q) {
      this.$el.find('.page-base-input').val(app.data.get.q);
    }
  }
});

app.modules.PageBaseView = Backbone.View.extend({
  events: {
    'click .fb-login': 'login',
    'click .fb-logout': 'logout'
  },
  initialize: function() {
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-27227565-1', 'auto');
    ga('send', 'pageview');
  },
  login: function(e) {
    e.preventDefault();
    window.location = '/oauth_login.php';
  },
  logout: function(e) {
    e.preventDefault();
    window.location = '/logout.php';
  },
  renderNavigation: function(user) {
    var el = this.$el.find('.page-base-nav');
    if(!this.user_nav) {
      this.user_nav = new app.modules.PageUserNav({
        el: el,
        model: new Backbone.Model(app.data.user)
      });
    }
    this.user_nav.render();
  },
  render: function() {
    this.el.innerHTML += app.mustache['page-base'];
    this.$elPage = this.$el.find('.content-display-wrapper');
    this.renderPage();
    this.renderNavigation();
    this.$el.find('.loading-background').addClass('hide');
    this.$el.find('.content-display').removeClass('hide');

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