app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // render article list
    var el = $('<div/>');
    this.$elPage.append(el);

    this.article_editor = new app.modules.ArticleEditorView({
      el: el,
      model: new Backbone.Model({
        editor_title: 'Create Article',
        editor_markdown: 'Hello World\n==========='
      })
    });
    this.article_editor.render();
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();