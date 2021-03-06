app.modules.CommentBox = Backbone.View.extend({
  events: {
    'submit .comment-form': 'post_comment',
    'submit .delete-form': 'delete_comment',
    'click .reply-to-comment': 'reply'
  },
  focus: function() {
    this.$el.find('textarea').focus();
  },
  reply: function(e) {
    e.preventDefault();
    this.trigger('reply_to_comment', this.model.attributes);
  },
  delete_comment: function(e) {
    e.preventDefault();
    var self = this;
    var m = this.model.attributes;
    var data = {
      github_id: m.github_id,
      comment_id: m.comment_id
    }
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: '/api/index.php',
      data: JSON.stringify({
        'method': 'comment',
        'data': data
      }),
      success: function(o) {
        self.model.set('comment_deleted', true);
        self.render();
      }
    });
  },
  post_comment: function(e) {
    var self = this;
    e.preventDefault();
    if(this.$el.find('textarea').val() == '')
      return;
    var comment_foreign = null;
    var m = this.model.attributes;
    if(m.in_reply_to) {
      comment_foreign = m.in_reply_to.comment_id;
    }
    var data = {
      github_id: m.github_id,
      comment_text: this.$el.find('textarea').val(),
      article_id: app.data.article.id,
      comment_foreign: comment_foreign
    }
    // post comment to server
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: '/api/index.php',
      data: JSON.stringify({
        'method': 'comment',
        'data': data
      }),
      success: function(o) {
        self.trigger('comment_posted');
        self.model.set(o.data);
        self.render();
      }
    });
  },
  render: function(edit) {
    this.model.attributes.currentUser = (this.model.attributes.github_id == app.data.user.id && !edit);
    this.model.attributes.fromNow = moment(this.model.attributes.timestamp).fromNow();
    if(this.model.attributes.in_reply_to) {
      this.model.attributes.in_reply_to.fromNow = moment(this.model.attributes.in_reply_to.timestamp).fromNow()
    }
    this.model.attributes.edit = edit;
    this.$el.html(Mustache.render(app.mustache['comment-box'], this.model.attributes));
  }
});

app.modules.Comments = Backbone.View.extend({
  replyToComment: function(o) {
    // replace edit
    var data = {
      in_reply_to: o,
      github_id: app.data.user.id
    }
    this.newComment.model.set(data);
    this.newComment.render(true);
    this.newComment.focus();
  },
  makeCommentPlaceholder: function() {
    if(app.data.user.id) {
      // render blank comment box in edit mode
      var el = $('<div/>');
      this.$el.prepend(el);
      this.newComment = new app.modules.CommentBox({
        el: el,
        model: new Backbone.Model({
          github_id: app.data.user.id,
          user_cache: app.data.user
        })
      });
      this.newComment.render(true);
      var self = this;
      this.newComment.on('comment_posted', function() {
        self.makeCommentPlaceholder();
      });
    }
  },
  render: function() {
    var coms = this.model.get('comments');
    this.makeCommentPlaceholder();
    // render all comments
    for(var i = 0; i < coms.length; i++) {
      coms[i].comment_deleted = coms[i].comment_deleted == '1';

      if(coms[i].in_reply_to) {
        coms[i].in_reply_to.comment_deleted = 
          coms[i].in_reply_to.comment_deleted == '1';
      }

      var el = $('<div/>');
      this.$el.append(el);
      var com = new app.modules.CommentBox({
        el: el,
        model: new Backbone.Model(coms[i])
      });
      com.render();

      com.on('reply_to_comment', this.replyToComment, this);
    }
  }
});

app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // parse youtube video id

    if(this.model.attributes.article.youtube != '') {
      var video_id = this.model.attributes.article.youtube.split('v=')[1];
      if(video_id) {
        var ampersandPosition = video_id.indexOf('&');
        if(ampersandPosition != -1) {
          video_id = video_id.substring(0, ampersandPosition);
        }
        this.model.attributes.video_id = video_id;
      }
    }

    // render article
    this.$elPage.html(Mustache.render(app.mustache['article'], this.model.attributes));
    // set marked options
    marked.setOptions({
      renderer: new marked.Renderer(),
      gfm: true,
      tables: true,
      breaks: true,
      pedantic: false,
      sanitize: true,
      smartLists: true,
      smartypants: false,
      highlight: function (code) {
        return hljs.highlightAuto(code).value;
      }
    });

    // render article base
    var el = $('<div/>');
    this.$elPage.find('.article-md').append(el);
    el.html(marked(app.data.article.md));

    // render comments
    var comments = new app.modules.Comments({
      el: this.$elPage.find('.article-comments'),
      model: new Backbone.Model({
        comments: app.data.comments
      })
    });
    comments.render();
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();