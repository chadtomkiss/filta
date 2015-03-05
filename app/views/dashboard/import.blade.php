@extends('layouts.master')

@section('content')
	<div class="section">
		<div class="row">
			<div class="import">
					<h2>Import</h2>

					<p>If you follow more than 200 people, we will import the rest of them for you later.</p>

					<div class="import-form">
						{{ Form::open(array('route' => 'twitter.import.post')) }}
							{{ Form::submit('OK, lets go!', array('class' => 'btn btn__secondary')) }}
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>

@stop