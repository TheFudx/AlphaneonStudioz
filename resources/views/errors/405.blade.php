
@extends('errors.error-layout')
@section('title')
    405 - Method Not Allowed
@endsection
@section('content')
        <div class="error-code">405 - Method Not Allowed</div>
        <div class="error-message">The HTTP method used is not allowed for this route.</div>
@endsection