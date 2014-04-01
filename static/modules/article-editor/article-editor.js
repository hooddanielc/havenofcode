app.modules.ArticleEditorModel = Backbone.Model.extend({
  url: '/api/index.php',
  sync: function(type, m) {
    var self = this;
    var method = 'article';
    switch(type) {
      case 'read':
        if(m.id) {
          $.getJSON(this.url + '?method='+ method +'&id=' + m.attributes.id, function(data) {
            if(data.success) {
              var o = data.data;
              m.id = o.id;
              m.set({
                'id': o.id,
                'title': o.title,
                'editor_markdown': o.md,
                'published': (o.published == '1'),
                'description': o.description,
                'github_repo_link': o.repo,
                'youtube_link': o.youtube,
                'github_id': o.github_id
              });
              self.trigger('fetch_success');
            } else {
              window.location = '/create_article.php';
            }
          });
        }
        break;
      case 'create':
        $.ajax({
          type: "POST",
          dataType: 'json',
          url: this.url,
          data: JSON.stringify({
            'method': 'article',
            'data': {
              'github_id': m.get('github_id'),
              'title': m.get('title'),
              'description': m.get('description'),
              'md': m.get('editor_markdown'),
              'repo': m.get('github_repo_link'),
              'youtube': m.get('youtube_link'),
              'published': m.get('published') ? 1 : 0
            }
          }),
          success: function(data) {
            if(data.success) {
              m.id = data.data.id;
              m.set('id', data.data.id);
              m.trigger('create_success');
            } else {
              m.trigger('sync_failed', 'Something went wrong on the server. Could be that darn cat again :-(');
            }
          }
        });
        break;
      case 'update':
        $.ajax({
          type: "POST",
          dataType: 'json',
          url: this.url,
          data: JSON.stringify({
            'method': 'article',
            'data': {
              'id': m.get('id'),
              'github_id': m.get('github_id'),
              'title': m.get('title'),
              'description': m.get('description'),
              'md': m.get('editor_markdown'),
              'repo': m.get('github_repo_link'),
              'youtube': m.get('youtube_link'),
              'published': m.get('published') ? 1 : 0
            }
          }),
          success: function(data) {
            if(data.success) {
              m.trigger('update_success');
            } else {
              // assuming the user is malicious...
              window.location = '/create_article.php';
            }
          }
        });
        break;
    }
  },
  defaults: {
    github_id: app.data.user.id ? app.data.user.id : 0, // page required logged in user
    editor_title: 'Create Article',
    editor_markdown: 'Hello World\n===========',
    github_repo_link: '',
    youtube_link: '',
  }
});

app.modules.ArticleEditorView = Backbone.View.extend({
  events: {
    'submit .article-editor-form': 'submit',
    'change .form-control': 'form_changed',
    'change .form-control-checkbox': 'form_changed'
  },

  initialize: function() {
    this.model.on('create_success', function() {
      this.model.set('editor_title', 'Update Article');
      this.$el.find('.article-editor-title').text('Update Article');
      this.showSuccess('Article Creation successful! Good job :-)');
    }, this);
    this.model.on('update_success', function() {
      this.showSuccess('Update successful! Good job :-)');
    }, this);
    this.model.on('fetch_success', function() {
      this.render();
    }, this);
    this.model.on('sync_failed', function(message) {
      this.showError(message);
    }, this);
  },

  showSuccess: function(message) {
    var el = $('<div style="display:none;" class="alert alert-success">' + message + '</div>');
    this.$el.find('.warnings').append(el);
    el.fadeToggle();
    setTimeout(function() {
      el.fadeToggle({
        complete: function() {
          el.remove();
        }
      });
    }, 6000);
  },

  showError: function(message) {
    var el = $('<div style="display:none;" class="alert alert-danger">' + message + '</div>');
    this.$el.find('.warnings').append(el);
    el.fadeToggle();
    setTimeout(function() {
      el.fadeToggle({
        complete: function() {
          el.remove();
        }
      });
    }, 6000);
  },

  showWarning: function(message) {
    var el = $('<div style="display:none;" class="alert alert-warning">' + message + '</div>');
    this.$el.find('.warnings').append(el);
    el.fadeToggle();
    setTimeout(function() {
      el.fadeToggle({
        complete: function() {
          el.remove();
        }
      });
    }, 6000);
  },

  validateForm: function() {
    var m = this.model;
    var passed = true;
    if(m.get('title') == '') {
      this.showWarning('Oops! You forgot to fill in an article title')
      passed = false;
    }
    if(m.get('description') == '') {
      this.showWarning('Oops! You forgot to fill in an article description');
      passed = false;
    }
    if(m.get('editor_markdown') == '') {
      this.showWarning('Oops! You forgot to fill in any article markdown');
      passed = false;
    }
    return passed;
  },

  form_changed: function(e) {
    var el = $(e.currentTarget);
    if(el.attr('type') == 'checkbox') {
      this.model.set(el.attr('name'), el[0].checked);
    } else {
      this.model.set(el.attr('name'), el.val());
    }
  },

  submit: function(e) {
    e.preventDefault();
    if(this.validateForm()) {
      // tell the model to save
      this.model.save();
    }
  },

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