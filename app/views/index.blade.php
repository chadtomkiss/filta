<!DOCTYPE html>
<html>
<head>
	<title>Filta</title>
	<link rel="stylesheet" type="text/css" href="assets/css/site.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>
	<div class="container">
		<div class="site-header clearfix">
			<h1 class="logo">Filta</h1>

			<div class="header-nav">
				<a class="btn btn__primary" href="{{ URL::route('twitter.connect') }}">Log In with Twitter</a>
			</div>
		</div>

		<div class="hero">
			<h2>Search the people you follow on Twitter</h2>
			<img class="screenshot" src="{{ URL::to('img/screenshot.png') }}" />
		</div>

		<div class="why">
			<div>
				<h1>Why you'll love using Filta</h1>

				<div class="pitch">
					<p>We follow people for a reason, because they are awesome right? Maybe they're a ridiculously talented illustrator (fyi, <a href="http://twitter.com/jackiesaik">@jackiesaik</a> did the sick as fuck illo down below), or perhaps they love photography as much as you do and have the best <a href="http://exposure.so">@exposure</a> posts.</p>

					<p>If you're visiting a new city and want to get beers, or looking to hire someone for that awesome app you are building, you can quickly search and organize them using Filta.</p>
				</div>

				<img src="{{ URL::to('img/super-duper-jackie-saik-illo.svg') }}" />
			</div>
		</div>

		<footer>
			<div class="cta">
				<a class="btn btn__primary" href="{{ URL::route('twitter.connect') }}">Get Started for Free</a>
			</div>

	        <div class="social">
	          	Made by <a href="http://twitter.com/chadtomkiss">@chadtomkiss</a>
	        </div>
	    </footer>
    </div>
</body>
</html>

