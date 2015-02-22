<!DOCTYPE html>
<html>
<head>
	<title>Filta</title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>

	<div class="site-header" style="display: block; background: #265b65; padding: 0; padding-left: 2%; padding-top: 1%; padding-bottom: 1%; border-top: 0">
		<div style="float: left; display: block; padding-top: 7px; width: 60%;">
			<a href="{{ URL::to('dashboard') }}" class="logo" style="color: white">Filta</a>

		</div>
		@if($current_user)
			<div style="float: left; display: block; width: 40%; padding-right: 2%;">
				<div class="header-buttons">
					<a class="button button--primary" href="{{ URL::to('twitter/import') }}">Import</a>
					<a class="button button--twitter" href="{{ URL::route('logout') }}">Logout</a>
				</div>
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