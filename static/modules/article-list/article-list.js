app.modules.ArticleListModel = Backbone.Model.extend({
  sync: function(method, model, ops) {
    var self = this;
    // method is only for reads
    if(method === 'read') {
      if(model.attributes.type === 'latest') {
        $.getJSON('/api?method=article_list&type=latest', function(o) {
          for(var i = 0; i < o.data.articles.length; i++) {
            o.data.articles[i].fromNow = moment(o.data.articles[i].publish_date).fromNow();
            if(app.data.user) {
              o.data.articles[i].canEdit = (o.data.articles[i].user.id == app.data.user.id);
            }
          }
          self.set('articles', o.data.articles);
        });
      }
    }
  },
  defaults: {
    title: 'Latest Posts',
    type: 'latest',
    articles: []
  }
});

app.modules.ArticleListView = Backbone.View.extend({
  initialize: function() {
    this.model.on('change', this.render, this);
    this.model.fetch();
  },
  render: function() {
    this.$el.html(Mustache.render(app.mustache['article-list'], this.model.attributes));
  }
});