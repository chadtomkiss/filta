@extends('layouts.master')

@section('content')
	{{ Form::open(array('route' => 'account.signup.post')) }}
		
		<h2>Signup</h2>

		@if($errors->has('error'))
			<div>
				{{ $errors->first('error') }}
			</div>
		@endif

		<div>
			@if($errors->has('email'))
				<div>
					{{ $errors->first('email') }}
				</div>
			@endif

			<label for="email">Email
				{{ Form::text('email', Input::old('email'), array('id' => 'email')) }}
			</label>
		</div>

		<div>
			@if($errors->has('password'))
				<div>
					{{ $errors->first('password') }}
				</div>
			@endif

			<label for="password">Password
				{{ Form::password('password', array('id' => 'password')) }}
			</label>
		</div>

		{{ Form::submit('Signup') }}
	{{ Form::close() }}
@stop