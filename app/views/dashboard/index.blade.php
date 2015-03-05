@extends('layouts.master')

@section('content')
	<div class="cf">
		<div class="sidebar">
			<div class="search">

				<div class="search-form">
					{{ Form::open(array('route' => 'twitter.search.saved.post')) }}
						
						<input type="text" name="search_followers" value="{{ Input::get('query') }}" id="js-search-followers" placeholder="Search..." />
						<button type="submit" class="btn btn__secondary js-save-search">Save this Search</button>
						
					{{ Form::close() }}
				</div>

				@if($saved_searches->count())

					<div class="saved_search">

						<h3>Saved Searches</h3>

						<ul class="saved-search-list js-saved-search-list">
							@foreach($saved_searches as $search)
								<li class="{{ (Input::get('query') == $search->query) ? 'current-item' : '' }}">
									<a href="dashboard?query={{ $search->query }}">{{ $search->title }}</a>

									{{ Form::open(array('route' => 'dashboard', 'method' => 'PUT', 'class' => 'js-delete-search', 'style' => 'float: right;')) }}
										<button class="fa fa-times btn__delete-cross js-confirm-delete"></button>
										{{ Form::hidden('search_id', $search->id) }}
									{{ Form::close() }}
								</li>
							@endforeach
						</ul>
					</div>
				@endif
			</div>
		</div>

		<div class="main">
			@include('partials.following_table', array('users' => $users))
		</div>
	</div>
@stop

@section('scripts')
	@parent

	<script type="text/javascript" src="js/utils.js"></script>
	<script type="text/javascript" src="assets/js/app.js"></script>
@stop