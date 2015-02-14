@extends('layouts.master')

@section('content')

<div style="margin: 2% 0 5% 0;">
	<div style="padding-left: 2%; padding-bottom: 2%">
		<input type="text" name="search_followers" value="{{ Input::get('query') }}" id="js-search-followers" placeholder="Search..." />
	</div>

	<div style="padding-left: 2%; padding-right: 5%">
		@include('partials.following_table', array('users' => $users))
	</div>

	<div style="clear:both"></div>
</div>
@stop

@section('scripts')
@parent

<script type="text/javascript" src="js/handlebars-v2.0.0.js"></script>
<script type="text/javascript" src="js/utils.js"></script>
<script>
	var FILTA = {
		initilize: function(term) {
			var words = term.split(' ');

			$.each(words, function(i, word) {
				$('.ranking-table tbody').highlight(word);
			});
		},
		searchData : function(term) {
			$.ajax({
				url:"following/search",
				method:'get',
				data: { query: term },
				success: function(resHTML) {
					$('#following-table-container').replaceWith(resHTML);

					var search = $('#js-search-followers').val();
					var words = search.split(' ');

					window.history.pushState({}, '', '/dashboard?query=' + search);

					$.each(words, function(i, word) {
						$('.ranking-table tbody').highlight(word);
					});
				}
			});
		}
	};

	$(document).ready(function(){
		var search = $('#js-search-followers');
		FILTA.initilize(search.val());

		search.keyup( $.debounce(300, function(e) {
			var query = $(this).val();

			FILTA.searchData(query);
		}));
	});
</script>
@stop