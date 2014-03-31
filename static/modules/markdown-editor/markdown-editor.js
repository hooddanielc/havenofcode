app.modules.MarkdownEditorView = Backbone.View.extend({
  initialize: function() {
    var self = this;
    this.model.on('change:editor_markdown', function(o) {
      self.updatePreview(o.changed.editor_markdown);
    }); 
  },

  updatePreview: function(markdown_string) {
    this.$el.find('.markdown-editor-markdown-viewer').html(marked(markdown_string));
  },

  render: function() {
    var self = this;
    this.$el.html(Mustache.render(app.mustache['markdown-editor'], this.model.attributes))

    // render codemirror
    var t_area = this.$el.find('.markdown-editor-text-area');
    this.codemirror = CodeMirror.fromTextArea(t_area[0], {
      lineNumbers: true
    });

    this.codemirror.on('change', function(cm, change) {
      self.model.set('editor_markdown', self.codemirror.getValue());
    });

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

    // update the preview
    this.updatePreview(this.codemirror.getValue());

    window.codemirror = this.codemirror;

    // refresh codemirror display
    $(document).on('ready', function() {
      self.codemirror.refresh();
    });
  }
});