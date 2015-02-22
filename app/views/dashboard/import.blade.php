@extends('layouts.master')

@section('content')
	<div class="section">
		<div class="row">
			<div style="width: 30%; background: white; margin: 5% auto; padding: 2% 0">
				<div style="width: 60%; margin: 0 auto; text-align: center">
					<h2>Import</h2>
					<p>If you follow more than 200 people, we will import the rest of them for you later.</p>
					{{ Form::open(array('route' => 'twitter.import.post')) }}
						{{ Form::submit('OK, lets go!', array('class' => 'button')) }}
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>

@stop