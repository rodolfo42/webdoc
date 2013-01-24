$(function() {

	var MD = new Showdown.converter();
	
	var BASE_URL = $('base').attr('href');
	var url = function(url) {
		return BASE_URL + url;
	};	
	var appRouter = null; 

	var Page = Backbone.Model.extend( {
		getHTML : function() {
			return MD.makeHtml(this.get('conteudo'));
		}
	});

	var PageCollection = Backbone.Collection.extend( {
		model : Page
	});

	var SidebarView = Backbone.View
			.extend( {
				el : $('#Sidebar'),
				template : "{{#pages}}<li rel=\"{{id}}\"><a href=\"view/{{id}}\">{{titulo}}</a></li>{{/pages}}",
				events: {
					'click li a': 'onClick'
				},
				render : function() {
					var html = Mustache.render(this.template, {
						pages : this.model.toJSON()
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
			"*actions" : "defaultRoute"
		},
		initialize : function(options) {
			self = this;
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
		defaultRoute : function(actions) {
			this.showPage(1);
		},
		showPage : function(id) {
			var contentView = new ContentView( {
				model : this._pages.at(id - 1)
			});
			$(".active").removeClass('active');
			$("[rel=" + id + "]").addClass('active');
			contentView.render();
		}
	});

	appRouter = new AppRouter();
	
	Backbone.history.start({
		root: BASE_URL,
		pushState: true
	});
});