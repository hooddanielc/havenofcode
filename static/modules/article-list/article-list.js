// infinite scroll jquery plugin
// created by Daniel Hood
(function($) {
  $.fn.scrolltastic = function(o) {
    var $this = this,
      _tickSpeed = o.tickSpeed,
      _offsetBottom = o.offsetBottom || 500,
      _bufferLength = o.bufferLength || 20,
      _renderAmount = o.renderAmount || 5,
      _renderMore = o.renderMore || function(){},
      _loadMore = o.loadMore || function(){},
      _onEnd = o.onEnd || function(){},
      _stop = false,
      _itemsWaitingToBeRendered = [],
      _scrollEl = $this,
      _contentEl = $this.find('.dh-scroll-content'),
      _loading = false,
      _page = 0;

    // make sure scrolling is enabled
    $this.css('overflow-y', 'auto');

    // hide end of content element
    $this.find('.dh-scroll-end').hide();

    $this.bufferItems = function(objArray) {
      // concatenate _itemsWaitingToBeRendered
      _itemsWaitingToBeRendered = _itemsWaitingToBeRendered.concat(objArray);
      if(objArray.length == 0) {
        // we need to stop now, because buffer fed
        // us nothing, and we don't expect buffer
        // to feed us anymore.
        _stop = true;
      } else {
        // stop the loading state
        _loading = false;
      }
    }

    $this.stop = function() {
      _stop = true;
    }

    function _getScrollDifference() {
      var scrollHeight = _scrollEl.height();
      var contentHeight = _contentEl.height();
      var scrollTop = _scrollEl.scrollTop();
      return contentHeight - (scrollHeight + scrollTop);
    }

    // sliceBuffer take the first
    // x items out of array and
    // returns them for your use
    function _sliceBuffer(amount) {
      var newBuffer = _itemsWaitingToBeRendered.slice(amount, _itemsWaitingToBeRendered.length);
      var returnedObjArray = _itemsWaitingToBeRendered.slice(0, amount);
      _itemsWaitingToBeRendered = newBuffer;
      return returnedObjArray;
    }

    // we are using state machine
    // to monitor the state of the
    // scroll.
    function _tick() {
      // should we stop?
      if(!_stop) {
        setTimeout($.proxy(function() {
          _tick();
        }, this), _tickSpeed);
      } else {
        // looks like there is an end
        // to the world after all
        $.proxy(_renderMore, $this)(_itemsWaitingToBeRendered);
        $this.find('.dh-scroll-loading').css('display', 'none');
        $this.find('.dh-scroll-end').css('display', 'block');
        _onEnd();
        return;
      }

      // is the user able to scroll yet?
      if(_scrollEl.height() >= _contentEl.height()) {
        if(_itemsWaitingToBeRendered.length > 0) {
          $.proxy(_renderMore, $this)(_sliceBuffer(_renderAmount));
        } else if(!_loading) {
          $.proxy(_loadMore, $this)(_page++);
          _loading = true;
        }
        return;
      }

      // should we load more?
      if(_itemsWaitingToBeRendered.length <= _bufferLength && !_loading) {
        $.proxy(_loadMore, $this)(_page++);
        _loading = true;
      }

      var scrollDifference = _getScrollDifference();

      // has the user scrolled far enough to
      // perhaps view more items?
      if(scrollDifference <= _offsetBottom && _itemsWaitingToBeRendered.length > 0) {
        $.proxy(_renderMore, $this)(_sliceBuffer(_renderAmount));
      }
    }
    _tick();
  }
})(jQuery);

// article list model being used
// as a reference
app.modules.ArticleListModel = Backbone.Model.extend({
  sync: function(method, model, ops) {
    var self = this;
    // method is only for reads
    if(method === 'read') {
      if(model.attributes.type === 'latest') {
        $.getJSON('/api?method=article_list&type=' + this.get('type'), function(o) {
          for(var i = 0; i < o.data.articles.length; i++) {
            o.data.articles[i].fromNow = moment(o.data.articles[i].publish_date).fromNow();
            if(app.data.user) {
              o.data.articles[i].canEdit = (o.data.articles[i].user.id == app.data.user.id);
            }
          }
          self.set('articles', o.data.articles);
        });
      } else if(model.attributes.type == 'user') {
        $.getJSON('/api?method=article_list&type=' + this.get('type') + '&github_id=' + this.get('github_id'), function(o) {
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
    articles: [],
    no_results_msg: 'No articles found'
  }
});

app.modules.ArticleListView = Backbone.View.extend({
  render: function() {
    var self = this;
    var lemmetakeaselfie = this;
    this.$el.html(Mustache.render(app.mustache['article-list'], this.model.attributes));

    // infinite scroll will drive the loading of content
    var foundSome = false;
    $(document.body).scrolltastic({
      onEnd: function() {
        if(!foundSome) {
          lemmetakeaselfie.$el.find('.dh-scroll-end').css('display', 'none');
          lemmetakeaselfie.$el.find('.zero-results-message').css('display', 'block');
        }
      },
      loadMore: function(page) {
        var self = this;
        var type = lemmetakeaselfie.model.get('type');
        if(type == 'user') {
          $.getJSON('/api?method=article_list&github_id='+ lemmetakeaselfie.model.get('github_id') +'&type='+ lemmetakeaselfie.model.get('type') +'&rows=20&start=' + (page * 20), function(o) {
            self.bufferItems(o.data.articles);
          });
        } else if (type == 'latest') {
          $.getJSON('/api?method=article_list&type='+ lemmetakeaselfie.model.get('type') +'&rows=20&start=' + (page * 20), function(o) {
            self.bufferItems(o.data.articles);
          });
        } else if (type == 'query') {
          $.getJSON('/api?method=article_list&query='+ lemmetakeaselfie.model.get('query') +'&type='+ lemmetakeaselfie.model.get('type') +'&rows=20&start=' + (page * 20), function(o) {
            self.bufferItems(o.data.articles);
          });
        }
      },
      renderMore: function(data) {
        if(data.length > 0) {
          foundSome = true;
        }
        self.$el.append(el);
        var articles = data;
        var article_container = self.$el.find('.article-list-items');
        for(var i = 0; i < articles.length; i++) {
          articles[i].fromNow = moment(articles[i].publish_date).fromNow();
          if(app.data.user) {
            articles[i].canEdit = (articles[i].user.id == app.data.user.id);
          }
          var el = $('<div/>');
          article_container.append(el);
          el.html(Mustache.render(app.mustache['article-list-item'], articles[i]));
        }
      }
    });
  }
});