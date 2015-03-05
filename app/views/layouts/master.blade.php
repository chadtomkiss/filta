<!DOCTYPE html>
<html>
<head>
	<title>Filta</title>
	<link rel="stylesheet" type="text/css" href="/assets/css/app.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>

	<div class="header clearfix">
		<div class="logo">
			<a href="{{ URL::to('dashboard') }}">Filta</a>
		</div>

		@if($current_user)
			<div class="header-nav">

				@if($following_count)
					<a class="btn btn__primary" href="{{ URL::to('twitter/import') }}">Import</a>
				@endif
				<a class="btn btn__secondary" href="{{ URL::route('logout') }}">Logout</a>
			</div>
		@endif
	</div>

	@yield('content')

	@section('scripts')
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	@show
</body>
</html>