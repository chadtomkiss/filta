@extends('layouts.master')

@section('content')

<div style="margin: 2% 0 5% 0;">
	<div style="padding-left: 2%; padding-bottom: 2%">
		<input type="text" name="search_followers" value="{{ Input::get('query') }}" id="js-search-followers" placeholder="Search..." />
	</div>

	<div style="padding-left: 2%; padding-right: 5%">
		<div id="ajaxProgress" style="display: none;">
			<img src="https://i0.wp.com/cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" />
		</div>
		<div id="following-table-container">
		</div>
	</div>

	<div style="clear:both"></div>
</div>
@stop

@section('scripts')
@parent

<script type="text/javascript" src="js/handlebars-v2.0.0.js"></script>
<script type="text/javascript" src="js/utils.js"></script>

<!--Handlebar templates start-->
<script id="following-template" type="text/x-handlebars-template">
	@include('handlebars.following')
</script>
<!--Handlebar templates end-->

<script>

var paginate = function(pagination, options) {
	var type = options.hash.type || 'middle';
	var ret = '';
	var pageCount = Number(pagination.last_page);
	var page = Number(pagination.current_page);
	var term = pagination.term;
	var limit;
	if (options.hash.limit) limit = +options.hash.limit;

	if(page == 1 && page == pageCount)
		return;

	//page pageCount
	var newContext = {term: term};
	switch (type) {
		case 'middle':
		if (typeof limit === 'number') {
			var i = 0;
			var leftCount = Math.ceil(limit / 2) - 1;
			var rightCount = limit - leftCount - 1;
			if (page + rightCount > pageCount)
				leftCount = limit - (pageCount - page) - 1;
			if (page - leftCount < 1)
				leftCount = page - 1;
			var start = page - leftCount;

			while (i < limit && i < pageCount) {
				newContext.n = start;
				newContext.active = (start === page);
				
				ret = ret + options.fn(newContext);
				start++;
				i++;
			}
		}
		else {
			for (var i = 1; i <= pageCount; i++) {
				newContext.n = i;
				if (i === page) newContext.active = true;
				ret = ret + options.fn(newContext);
			}
		}
		break;
		case 'previous':
		if (page === 1) {
			return;
		}
		else {
			newContext.n = page - 1;
		}
		ret = ret + options.fn(newContext);
		break;
		case 'next':

			if (page === pageCount) {
				return;
			}
			else {
				newContext.n = page + 1;
			}
			ret = ret + options.fn(newContext);
		break;
	}

	return ret;
};

Handlebars.registerHelper('paginate', paginate);

var FILTA = {

	initialize:function(resJSON) {
		var templateSource   = $("#following-template").html(),

		template = Handlebars.compile(templateSource),
		html = template(resJSON);

		$('#following-table-container').html(html);
		$('#following-table-container').highlight($('#js-search-followers').val());
	},
	handleData:function(resJSON) {

		var templateSource   = $("#following-template").html(),

		template = Handlebars.compile(templateSource),
		html = template(resJSON);

		$('#following-table-container').html(html);
		$('#following-table-container').highlight($('#js-search-followers').val());

	},
	searchData : function(term, page) {

		var page = page || 1;

		$.ajax({
			url:"api/following",
			method:'get',
			data: { query: term, page: page },
			success:this.handleData
		});
	}
};

$(document).ready(function(){

	var data = {{ $following }};
	FILTA.initialize(data);

	var search = $('#js-search-followers');

	search.keyup( $.debounce( 250, function(e) {
		var query = $(this).val();

		FILTA.searchData(query);
	}));
});
</script>
@stop