<!DOCTYPE html>
<html>
<head>
	<title>Filta</title>
	<link rel="stylesheet" type="text/css" href="/css/site.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>

	<div style="min-height:100%; position:relative;">

	<div style="width: 70%; margin: 0 auto; margin-bottom: 6%; margin-top: 2%">
		<h1 class="logo" style="color: white; float: left; width: 5%">Filta</h1>

		<div style="float: left; width: 95%; margin-top: 5px; text-align: right">

			<a class="button button--twitter" style="" href="{{ URL::route('twitter.connect') }}">Log In with Twitter</a>
		</div>
		<div style="clear: both"></div>
	</div>

	<div style="width: 70%; margin: 0 auto; padding: 0; text-align: center">
		<h2 style="margin-bottom: 5%">Search the people you follow on Twitter</h2>
		<img src="{{ URL::to('img/screenshot.png') }}" style="display: block; width: 100%; padding: 0; margin: 0" />
	</div>

	<div style="background: #FFF; color: black; text-align: center; padding: 5%">

		<div style="width: 70%; margin: 0 auto; line-height: 20px">
			<div style="width: 40%; margin: 0 auto; line-height: 25px">
				<h1>Why you'll love using Filta</h1>
			</div>

			<div style="width: 80%; margin: 40px auto; line-height: 25px">
			<p>We follow people for a reason, because they are awesome right? Maybe they're a ridiculously talented illustrator (fyi, <a href="http://twitter.com/jackiesaik">@jackiesaik</a> did the sick as fuck illo down below), or perhaps they love photography as much as you do and have the best <a href="http://exposure.so">@exposure</a> posts.</p>

			<p>If you're visiting a new city and want to get beers, or looking to hire someone for that awesome app you are building, you can quickly search and organize them using Filta.</p>
			</div>

			<img src="{{ URL::to('img/super-duper-jackie-saik-illo.svg') }}" />

		</div>
	</div>

	<div style="background: #F38264; color: white; text-align: center; padding: 5%; padding-bottom: 120px;">
		<div style="width: 70%; margin: 0 auto; text-align: center; line-height: 20px">
			<a class="button button--twitter" style="padding: 20px;" href="{{ URL::route('twitter.connect') }}">Get Started for Free</a>
		</div>
	</div>

	<footer style="width: 100%; text-align: center; background: #F38264; position: absolute; bottom: 0; height: 75px; padding-top: 30px;">
        <div class="container">
                <div class="social">
                  Made by <a href="http://twitter.com/chadtomkiss" style="text-decoration: none; color: white">@chadtomkiss</a>
                </div>
        </div>
    </footer>

    </div>

	@section('scripts')
		<!--<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
	@show
</body>
</html>

