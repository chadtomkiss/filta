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

		@if($current_user)
			<script>
			  window.intercomSettings = {
			    user_id: "{{ $current_user->id }}",
			    user_hash: "{{ hash_hmac("sha256", $current_user->id, "uc1yeWcF2VJ9bIJj-k58MMN_bQ_iAlMNhjibRK6h") }}",
			    twitter_id: "{{ $current_user->twitter_user_id }}",
			    name: "{{ $current_user->twitter_user_name }}",
			    created_at: {{ strtotime($current_user->created_at) }},
			    app_id: "n03mrcn4"
			  };
			</script>
			<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/n03mrcn4';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
		@endif
	@show
</body>
</html>