$(function() {

	var MD = new Showdown.converter();
	
	var BASE_URL = $('base').attr('href');
	var url = function(url) {
		return BASE_URL + url;
	};	
	var appRouter = null; 

	var Page = Backbone.Model.extend( {
		getHTML : function() {
            var html = "";
            if(this.get('conteudo') != null) {
                html = MD.makeHtml(this.get('conteudo'));
            }
            return html;
		}
	});

	var PageCollection = Backbone.Collection.extend( {
		model : Page
	});

	var SidebarView = Backbone.View
			.extend( {
				el : $('#Sidebar'),
				template : $('#list-tmpl').text(),
				events: {
					'click li a': 'onClick'
				},
				render : function() {
					var html = Mustache.render(this.template, {
						pages: this.model.toJSON()
					});
					this.$el.html(html);
					return this;
				},
				onClick : function(e) {
					if (!e.altKey && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
						e.preventDefault();
						var url = $(e.target).attr('href');
						appRouter.navigate(url, {trigger: true});
					}
				},
                changeTo: function(id) {
                    this.$el.find(".active").removeClass('active');
                    this.$el.find("[data-doc=" + id + "]").addClass('active');
                }
			});

	var ContentView = Backbone.View.extend( {
		el : $('#Content'),
		template : "<h1 class=\"page-header\">{{titulo}}</h1>",
		render : function() {
			this.$el.empty().append(Mustache.render(this.template, this.model.toJSON()))
					.append(this.model.getHTML())
					.find('table')
						.addClass('table table-bordered table-striped');
            if(this.options.sidebarView) {
                this.options.sidebarView.changeTo(this.model.get('id'));
            }
			return this;
		}
	});
	
	var ToolbarView = Backbone.View
			.extend( {
				
			});

	var AppRouter = Backbone.Router.extend( {
		_data : null,
		_pages : null,
		_view : null,
		routes : {
			"view/:id" : "showPage",
            "novo"     : "newDocument",
			"*actions" : "defaultRoute"
		},
		initialize : function() {
			var self = this;
			$.ajax({
				url : url('api/documentos'),
				dataType : 'json',
				data : {},
				async : false,
				success : function(data) {
					self._data = data;
					self._pages = new PageCollection(data);
					self._view = new SidebarView( {
						model : self._pages
					});
					self._view.render();
				}
			});
			return this;
		},
		defaultRoute : function() {
			this.showPage(null);
		},
		showPage : function(id) {
            var targetModel = null;
            if(id != null) {
                targetModel = this._pages.where({"id": id})[0];
            } else {
                targetModel = this._pages.first();
            }
			var contentView = new ContentView( {
				model: targetModel,
                sidebarView: this._view
			});
			contentView.render();
		},
        newDocument: function() {
            $('.modal').modal('show');
        }
	});

	appRouter = new AppRouter();
	
	Backbone.history.start({
		root: BASE_URL,
		pushState: true
	});
});