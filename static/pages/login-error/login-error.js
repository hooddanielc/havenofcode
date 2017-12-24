app.modules.Page = app.modules.PageBaseView.extend({
  renderPage: function() {
    // render article list
    var el = $('<div/>');
    this.$elPage.append(el);
    this.$elPage.html(Mustache.render(app.mustache['login-error'], this.model.attributes));
  }
});

(function() {
  var page = new app.modules.Page({
    el: $(document.body),
    model: new Backbone.Model(app.data)
  });
  page.render();
})();
