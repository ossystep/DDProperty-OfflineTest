<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>File Browser</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<link href="<?php echo $baseUrl ?>/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $baseUrl ?>/assets/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $baseUrl ?>/assets/style.css" rel="stylesheet" type="text/css"/>

</head>


<body>

<div class="container" id="application">
	<div class="row">
		<div class="col-md-12 head-topic">
			<span class="fileNameHead">File Name</span>
			<span class="fileMTimeHead">Date Modified</span>
			<span class="fileSizeHead">Size</span>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div id="tree_block">

			</div>
		</div>
	</div>
</div>

<script src="<?php echo $baseUrl ?>/assets/json2.js"></script>
<script src="<?php echo $baseUrl ?>/assets/jquery.js"></script>
<script src="<?php echo $baseUrl ?>/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl ?>/assets/underscore.js"></script>
<script src="<?php echo $baseUrl ?>/assets/backbone.js"></script>

<script>
	(function(){

		window.App = {
			Models: {},
			Views: {},
			Collections: {},
			Router: {}
		};

		window.template = function(id){
			return _.template($("#"+id).html());
		};

		window.vent = _.extend({}, Backbone.Events);
	})();
</script>

<script id="sourceTemplate" type="text/temlpate">
<div class="row source_li">
	<div class="col-md-12">
		<a class="expand_button <% if( type == "dir" ) { print("close_icon"); } %>" href="#"></a>
		<a class="source_link" href="#"><%= baseName %></a>
		<span class="fileMTime_label">
			<%= fileMTime %>
		</span>
		<span class="fileSize_label">
			<% if( type == "dir" ) { print("--"); } else { %><%= fileSize %><% } %>
		</span>
	</div>
</div>
</script>

<script>
	var baseUrl        = "<?php echo $baseUrl; ?>/index.php";
	var sourceRootData = <?php echo json_encode($sources) ?>;

	/**
	 * Models
	 */
	App.Models.Source = Backbone.Model.extend({});

	/**
	 * Collections
	 */
	App.Collections.Sources = Backbone.Collection.extend({
                                        model: App.Models.Source
                                    });
	/**
	 * View App
	 */
	App.Views.App = Backbone.View.extend({
		el             : '#application',
		rootCollection : '',
        initialize: function() {
	        this.rootCollection = new App.Collections.Sources;

	        this._renderFirstData(sourceRootData);
        },
		_renderFirstData : function(sources){
			var t = this;
			_.each(sources, function(source){
				var sourceModel = new App.Models.Source(source);
				t.rootCollection.add(sourceModel);
			});

			var html = new App.Views.Sources({collection : t.rootCollection} ).render().el;
			this.$el.find("#tree_block" ).html(html);

			this._hilightEven();
		},

	     _hilightEven: function()
	     {
	         $(".source_li").removeClass("even");
	         $(".source_li:even").addClass("even");
	     }
	});

	App.Views.Sources = Backbone.View.extend({
         tagName   : 'ul',

		 initialize: function(){
			 //console.log(this.collection);
		 },

		 render: function()
		 {
			 var t = this;
			 this.collection.each(function(source){
				 var html = new App.Views.Source({model: source} ).render().el;
				 t.$el.append(html)
			 });
			 return this;
		 }
    });

	App.Views.Source = Backbone.View.extend({
         tagName   : 'li',
         template  : template("sourceTemplate"),

         attributes: function(){
	         return { 'class' : this.getClass() }
         },

         initialize: function(){

         },

		 events: {
			 'click a.source_link'   : 'onClickSource',
			 'click a.close_icon'    : 'onClickToExpand',
			 'click a.expanded_icon' : 'onClickToDeExpand',
			 'click .source_li'      : 'onClickToFocusThisRow'
		 },

         render: function()
         {
	         this.$el.html( this.template( this.model.toJSON() ) );
	         return this;
         },

		 getClass: function()
		 {
			 if( this.model.get("type") == "dir" )
			 {
			 	 return "dir_icon";
			 }
			 else
			 {
				 return "file_icon";
			 }
		 },

         onClickSource : function(event)
         {
	         event.preventDefault();
	         if( this.model.get('type') != "dir" )
	         {
		         window.open(baseUrl+"/loadfile?path="+this.model.get('fullPath'), '_blank');
	         }
	         else
	         {
		         $(event.currentTarget).closest("div").find(".expand_button").trigger("click");
	         }
         },

         onClickToExpand : function(event)
         {
	         var t = this;
	         event.preventDefault();

	         $(event.currentTarget).removeClass("close_icon").addClass("loading_icon");

	         var subSources = new App.Collections.Sources;
	         subSources.url = baseUrl+'/get-sources';
	         subSources.fetch({data: {path: this.model.get("fullPath")}} ).then(function(result){
		         var html = new App.Views.Sources({collection : subSources} ).render().el;
		         $(event.currentTarget ).removeClass("loading_icon").addClass('expanded_icon').closest("li" ).append(html);

		         t.model.set("subSources", subSources);

		         App.Global._hilightEven();
	         });

	         return false;
         },

         onClickToDeExpand : function(event)
         {
	         event.preventDefault();

	         $(event.currentTarget).removeClass("expanded_icon").addClass("loading_icon");

	         if ( this.model.has("subSources") )
	         {
		         var data = this.model.get("subSources");
		         data.remove();
	         }

	         $(event.currentTarget).removeClass("loading_icon").addClass('close_icon').closest("li" ).find("ul").remove();
	         App.Global._hilightEven();

	         return false;
         },

		 onClickToFocusThisRow : function(event)
		 {
			 event.preventDefault();
			 $("#application").find(".focusRow" ).removeClass("focusRow");
			 $(event.currentTarget).addClass("focusRow");
			 return false;
		 }

	});

	App.Global = new App.Views.App;
	Backbone.history.start();
</script>
</body>
</html>