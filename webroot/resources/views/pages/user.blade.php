@extends('layouts.master')

@section('page_title')
Grimtofu
@endsection

@section('content')

<div class="container">

    <div class="row">

        <div class="col-sm-6 offset-sm-3">

			<div class="card">
				<img src="{{ $vars['user']['avatar_url'] }}" class="" style="width:100%" alt="{{ $vars['user']['login'] or '' }}">
				<div class="card-block">
    				<h4 class="card-title">{{ $vars['user']['login'] or '' }}</h4>
    				<p class="card-text"></p>
    				<ul class="list-group list-group-flush">
    					<li class="list-group-item">{{ count($vars['threads']) }} threads created</li>
    					@if ($vars['user']['company'])
    					<li class="list-group-item">{{ $vars['user']['company'] }}</li>
    					@endif
    					@if ($vars['user']['location'])
    					<li class="list-group-item">{{ $vars['user']['location'] }}</li>
    					@endif
    					@if ($vars['user']['blog'])
    					<li class="list-group-item">{{ $vars['user']['blog'] }}</li>
    					@endif
    				</ul>
				</div>

			</div>

        </div>

    </div>

</div>

@endsection
