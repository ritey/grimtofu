<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('page_title','Grimtofu')</title>
    <link rel="stylesheet" href="{{ elixir("css/app.css") }}">
	@yield('metas')
</head>
<body>

	<div class="container">

		<div class="row">
			<div class="col-sm-12">

				<ul class="nav justify-content-end">
					@if(!$token)
					<li class="nav-item"><a class="nav-link" href="{{ route('github_link') }}">Login</a></li>
					@else
					<li class="nav-item"><a class="nav-link" href="{{ route('clear-session') }}">Logout {{ $name or '' }}</a></li>
					@endif
				</ul>

			</div>
		</div>

		<div class="jumbotron">

			<h1>Grimtofu</h1>
            <p class="lead">Simplicity and communication is key.</p>

		</div>

		<div class="row">

			<div class="col-sm-12">

				@if($success_message)

				<div class="alert alert-success" role="alert">{!! $success_message !!}</div>

				@endif

				@if($error_message)

				<div class="alert alert-danger text-center" role="alert">{!! $error_message !!}</div>

				@endif

			</div>

		</div>

		<div class="row">

			<div class="col-sm-12 col-md-3">

				<p class="text-center"><a href="{{ route('new', ['channel' => $channel]) }}" class="btn btn-primary">New discussion</a></p>

				<hr />

				<div class="row">

					<div class="col-sm-12">

						<div class="menu">

					        <ul class="nav flex-column">
					        	<li class="nav-item"><a class="nav-link {{ $channel == 'all' ? 'active' : '' }}" href="{{ route('index') }}">All</a></li>
					            @foreach($categories as $item)
					            <li class="nav-item">
					                <a class="{{ (strtolower($item['name']) == strtolower($channel)) ? 'nav-link active' : 'nav-link' }}" href="{{ route('channel', ['channel' => strtolower($item['name'])]) }}">{{ $item['name'] }}</a>
					            </li>
					            @endforeach
					        </ul>

					    </div>

				    </div>

				</div>

			</div>

			<div class="col-sm-9">

				@yield('content')

			</div>

		</div>

	</div>

	<hr />

	<div class="container">

		<div class="row">

			<div class="col-sm-12">
				<p class="well well-lg small"><a href="http://grimtofu.coderstudios.com" target="_blank">Grimtofu</a> developed and maintained by <a href="http://www.coderstudios.com" target="_blank">Coder Studios</a></p>
			</div>

		</div>

	</div>

	<script src="{{ elixir("js/app.js") }}" type="text/javascript"></script>

@yield('footer')
</body>
</html>