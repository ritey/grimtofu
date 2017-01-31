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
					<li class="nav-item"><a class="nav-link" href="">Login</a></li>
					<li class="nav-item"><a class="nav-link" href="{{ $register_url }}">Register</a></li>
				</ul>

			</div>
		</div>

		<div class="jumbotron">

			<h1>Grimtofu</h1>

		</div>

		<div class="row">

			<div class="col-sm-12 col-md-3">

				<a href="{{ route('new') }}" class="btn btn-primary">New discussion</a>

				<hr />

		        <ul class="list-unstyled">
		        	<li><a href="{{ route('index') }}">All</a></li>
		            @foreach($categories as $item)
		            <li>
		                <a href="{{ route('channel', ['channel' => strtolower($item['name'])]) }}">{{ $item['name'] }}</a>
		            </li>
		            @endforeach
		        </ul>

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
				<p class="well well-lg small">Developed and maintained by <a href="http://www.coderstudios.com" target="_blank">Coder Studios</a></p>
			</div>

		</div>

	</div>
@yield('footer')
</body>
</html>