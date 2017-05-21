app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // render article list
    var el = $('<div/>');
    this.$elPage.append(el);
    this.latest_list = new app.modules.ArticleListView({
      el: el,
      model: new app.modules.ArticleListModel()
    });
    this.latest_list.render();
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();