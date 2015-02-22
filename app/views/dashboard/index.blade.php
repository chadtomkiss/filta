@extends('layouts.master')

@section('content')

<div style="margin: 0% 0 5% 0;">


	<div style="float: left; width: 20%; margin-top: 2%">
		<div style="margin: 0 auto; width: 80%; padding-bottom: 2%">
			
			{{ Form::open(array('route' => 'twitter.search.saved.post')) }}
				<div>
					<input type="text" style="width: 100%; padding-right: 0;" name="search_followers" value="{{ Input::get('query') }}" id="js-search-followers" placeholder="Search..." />
				</div>

				<button type="submit" class="save_search button" style="width: 100%; {{ (!Input::get('query')) ? 'display: none' : '' }}">Save this Search</button>
				
			{{ Form::close() }}

		@if($saved_searches->count())
			<div class="saved_search">
				<h2>Saved Searches</h2>
				<ul>
					@foreach($saved_searches as $search)
						<li>
							<a href="dashboard?query={{ $search->query }}">{{ $search->title }}</a>
						</li>
					@endforeach
				</ul>
			</div>
		@endif

		</div>
	</div>

	<div style="margin: 2%; margin-left: 0; width: 78%; float: left; background: white; padding: 2%">
		@include('partials.following_table', array('users' => $users))
	</div>

	<div style="clear:both"></div>
</div>
@stop

@section('scripts')
@parent

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

					if(search.length > 0 && $('.ranking-table tr').length)
					{
						$('.save_search').css('display', 'block');
					}
					else
					{
						$('.save_search').css('display', 'none');
					}

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

		search.keyup( $.debounce(400, function(e) {
			var query = $(this).val();

			FILTA.searchData(query);
		}));
	});
</script>
@stop