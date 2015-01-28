@extends('layouts.master')

@section('content')
	<div class="section">
		<div class="row">
			<div style="width: 50%; margin: 5% auto">

				<p>Some bullshit copy here m8s</p>


				{{ Form::open(array('route' => 'twitter.import.post')) }}
					{{ Form::submit('Import', array('class' => 'button')) }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@stop