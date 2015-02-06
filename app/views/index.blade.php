<!DOCTYPE html>
<html>
<head>
	<title>Filta</title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
</head>
<body>

	<div style="display: block;">
		<h1 class="logo" style="text-align: center;">Filta</h1>
	</div>

	@section('content')
	

	<div style="width: 70%; margin: 0 auto; text-align: center">
		<h2>Search the people you follow on Twitter</h2>
		
		<img src="{{ URL::to('img/screenshot.png') }}" style="width: 100%; margin-bottom: 3%" />

		
		<h3>Use Cases</h3>
		<p>Find people who are in the city you're about to visit.</p> 
		<p>Find people who do Rails.</p>
		<p>Find people who worked on <a href="http://twitter.com/benhowdle">@plotmovies</a>.</p>
		<p>Find people that love <a href="http://twitter.com/jongold">@jongold</a>.</p>
		<p></p> 
		<a class="button button--twitter" style="margin: 10px 0" href="{{ URL::route('twitter.connect') }}">Connect with Twitter</a>
	</div>
				
	@show

	@section('scripts')
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	@show
</body>
</html>

