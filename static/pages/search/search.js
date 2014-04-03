app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // render article list
    if(app.data.get.q) {
      var el = $('<div/>');
      this.$elPage.append(el);
      this.latest_list = new app.modules.ArticleListView({
        el: el,
        model: new app.modules.ArticleListModel({
          type: 'query',
          title: 'Search for "' + app.data.get.q + '"',
          no_results_msg: 'Zero result found for your search "' + app.data.get.q + '"',
          query: app.data.get.q
        })
      });
      this.latest_list.render();
    } else {
      window.location = '/404.php';
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