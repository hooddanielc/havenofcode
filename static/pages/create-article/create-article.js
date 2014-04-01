app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // render article list
    var el = $('<div/>');
    this.$elPage.append(el);

    // data
    var article_editor_data = {};
    var aid = app.data.get.article;
    if(aid && !isNaN(parseInt(aid)) && isFinite(aid) && aid > 0) {
      article_editor_data.id = parseInt(aid);
      article_editor_data.editor_title = 'Update Article';
    }

    this.article_editor = new app.modules.ArticleEditorView({
      el: el,
      model: new app.modules.ArticleEditorModel(article_editor_data)
    });
    if(aid) {
      this.article_editor.model.fetch();
    } else {
      this.article_editor.render();
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