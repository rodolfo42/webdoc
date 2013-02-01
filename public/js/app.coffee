MD = new Showdown.converter()
BASE_URL = $('base').attr 'href'
url = (url) -> BASE_URL + url

Page = Backbone.Model.extend
  getHTML: ->
    html = ""
    if @get('conteudo') isnt ""
      html = MD.makeHtml @get('conteudo')
    html

PageCollection = Backbone.Collection.extend
  model: Page

SidebarView = Backbone.View.extend
  el: $ '#Sidebar'
  template: """
            {{#pages}}
              <li data-doc="{{id}}">
                <a href="view/{{id}}">
                {{#titulo}}
                  {{titulo}}
                {{/titulo}}
                {{^titulo}}
                  <i>Sem título</i>
                {{/titulo}}
                </a>
              </li>
            {{/pages}}
            """
  events:
    'click li a': 'onClick'
  render: ->
    html = Mustache.render this.template,
      pages: @model.toJSON()
    @$el.html html
    @
  onClick: (e) ->
    if not (e.altKey or e.ctrlKey or e.metaKey or e.shiftKey)
      e.preventDefault()
      url = $(e.target).attr 'href'
      appRouter.navigate url, trigger: on
  changeTo: (id) ->
    @$el.find(".active").removeClass 'active'
    @$el.find("[data-doc=#{id}]").addClass 'active'

ContentView = Backbone.View.extend
  el: $ '#Content'
  render: ->
    @$el.empty()
    @$el.append "<h1 class=\"page-header\">#{@model.get('titulo')}</h1>"
    content = $ @model.getHTML()
    @$el.append content
    ($ 'table', @$el).addClass 'table table-bordered table-striped'
    if @options.sidebarView?
      @options.sidebarView.changeTo @model.get 'id'
    this

AppRouter = Backbone.Router.extend
  _data: null
  _pages: null
  _view: null
  routes:
    "view/:id": "showPage"
    "novo"    : "newDocument"
    "*actions": "defaultRoute"
  initialize: ->
    $.ajax
      url: url 'api/documentos'
      type: 'GET'
      dataType: 'json'
      data: {}
      async: off
      success: (data) =>
        @_data = data
        @_pages = new PageCollection data
        @_view = new SidebarView
          model: @_pages
        @_view.render()
    @
  defaultRoute: ->
    @showPage null
  showPage: (id) ->
    if id?
      targetModel = (@_pages.where "id": id)[0]
    else
      targetModel = @_pages.first()
    options =
      model: targetModel
      sidebarView: @_view
    contentView = new ContentView options
    contentView.render()
  newDocument: ->
    $('.modal').modal 'show'

appRouter = new AppRouter()

Backbone.history.start
  root: BASE_URL
  pushState: on

null