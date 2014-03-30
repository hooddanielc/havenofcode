app.modules.ArticleListModel = Backbone.Model.extend({
  sync: function(method, model, ops) {
    var self = this;
    // method is only for reads
    if(method === 'read') {
      if(model.attributes.type === 'latest') {
        $.getJSON('/api?method=article_list&type=latest', function(o) {
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