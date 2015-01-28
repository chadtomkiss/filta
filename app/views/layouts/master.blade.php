<!DOCTYPE html>
<html>
<head>
	<title>Filta</title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
</head>
<body>

	<div class="site-header" style="display: block">
		@if($current_user)
		<div style="float: left; display: block; width: 60%">
			<input type="text" name="search_followers" value="{{ Input::get('query') }}" id="js-search-followers" placeholder="Search..." />
		</div>
		<div style="float: left; display: block; width: 40%">
			<div class="header-buttons">
				<a class="button button--twitter" href="{{ URL::to('twitter/import') }}">Import</a>
				<a class="button button--twitter" href="{{ URL::route('logout') }}">Logout</a>
			</div>
		</div>
		@else
			<div class="header-buttons">
				<a class="button button--twitter" href="{{ URL::route('twitter.connect') }}">Connect with Twitter</a>
			</div>
		@endif

		<div style="clear:both"></div>
	</div>

	@yield('content')

	@section('scripts')
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	@show
</body>
</html>