app.modules.ArticleEditorView = Backbone.View.extend({
  render: function() {
    this.$el.html(Mustache.render(app.mustache['article-editor'], this.model.attributes));
  
    // render the markdown editor
    var el = $('<div/>');
    this.$el.find('.markdown-editor-container').append(el);
    this.markdown_editor = new app.modules.MarkdownEditorView({
      el: el,
      model: this.model
    });
    this.markdown_editor.render();
  }
});