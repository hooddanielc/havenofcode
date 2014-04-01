app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // render article list
    var el = $('<div/>');
    this.$elPage.append(el);
    // set marked options
    marked.setOptions({
      renderer: new marked.Renderer(),
      gfm: true,
      tables: true,
      breaks: true,
      pedantic: false,
      sanitize: true,
      smartLists: true,
      smartypants: false
    });
    el.html(marked(app.data.article.md));
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();