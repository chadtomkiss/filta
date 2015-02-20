<!DOCTYPE html>
<html>
<head>
	<title>Filta</title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>

	<div style="width: 70%; margin: 0 auto; margin-bottom: 6%; margin-top: 2%">
		<h1 class="logo" style="color: white; float: left; width: 5%">Filta</h1>

		<div style="float: left; width: 95%; margin-top: 5px; text-align: right">

			<a class="button button--twitter" style="" href="{{ URL::route('twitter.connect') }}">Log In with Twitter</a>
		</div>
		<div style="clear: both"></div>
	</div>

	<div style="width: 70%; margin: 0 auto; padding: 0; text-align: center">
		<h2 style="margin-bottom: 5%">Search the people you follow on Twitter</h2>
		<img src="{{ URL::to('img/screenshot.png') }}" style="width: 100%;" />
	</div>

	<div style="background: #FFF; color: black; text-align: center; padding: 5%">

		<div style="width: 40%; margin: 0 auto; line-height: 25px">
			<h1>Here's some examples</h1>
			<p>Filta was built so I could easily find people in the city I was visiting, but hey, there's some other use cases too <3</p>
		</div>

		<div style="float: left; width: 33.3%; padding: 5%">
			<i class="fa fa-street-view fa-3x"></i>
			<h2>Location</h2>
			<p>You follow people because they are awesome right? Go meet 'em.</p>
		</div>

		<div style="float: left; width: 33.3%;  padding: 5%">
			<i class="fa fa-university fa-3x"></i>
			<h2>Profession</h2>
			<p>Need a designer? People tend to put the things they're good at in their bio.</p>
		</div>

		<div style="float: left; width: 33.3%; padding: 5%">
			<i class="fa fa-film fa-3x"></i>
			<h2>Hobbies</h2>
			<p>Find people who worked on <a href="http://twitter.com/benhowdle">@plotmovies</a>.</p>
		</div>


		<div style="clear: both"></div>

		<a class="button button--twitter" style="" href="{{ URL::route('twitter.connect') }}">Get Started for Free</a>
	</div>

	<footer style="width: 70%; margin: 0 auto; margin-bottom: 6%; margin-top: 2%">
        <div class="container">
            <div class="row column">
                <a href="#">Filta</a>
                <div class="social">
                  <i class="fa fa-twitter fa-lg"></i>
                  Made by @chadtomkiss 
                </div>
            </div>
        </div>
    </footer>

	@section('scripts')
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	@show
</body>
</html>

