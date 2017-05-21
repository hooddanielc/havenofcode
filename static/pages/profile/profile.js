app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    this.$elPage.html(Mustache.render(app.mustache.profile, this.model.attribtues));

    // render users article list
    var el = $('<div/>');
    this.$elPage.find('.user-articles').append(el);
    var list = new app.modules.ArticleListView({
      el: el,
      model: new app.modules.ArticleListModel({
        'title': app.data.user_profile.login + "'s Articles",
        'github_id': app.data.user_profile.id,
        no_results_msg: 'User has not created any articles',
        'type': 'user'
      })
    });
    list.render();
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();