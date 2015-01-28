@extends('layouts.master')

@section('content')
	{{ Form::open(array('route' => 'twitter.import.post')) }}
		{{ Form::submit('Import') }}
	{{ Form::close() }}
@stop