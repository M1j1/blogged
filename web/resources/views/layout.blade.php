<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		
		<title>Blogged! - Michael's little example</title>

		<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('/css/style.css') }}" rel="stylesheet">
		<link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Fontdiner+Swanky" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="{{ url( '/' ) }}">Blogged! <span style="color:#dedede; font-size: 24px;">@yield('title')</span></a>
				</div>

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						@if (Auth::guest())
						<li>
							<a href="{{ url('/login') }}">Login</a> | <a href="{{ url('/register') }}">Register</a>
						</li>
						@else
						<li class="dropdown">
							<a href="#">{{ Auth::user()->name }}</a> | <a href="{{ url('/logout') }}">Logout</a>
						</li>
						@endif
					</ul>
				</div>
			</div>
		</nav>
		
		<div class="sub-nav">
			@yield( 'tags' )
			
			@if (!Auth::guest())
			<div class="new-post">
				<a href="{{ url('/write') }}" class="new-post-btn">+ Write a new Blog Post</a>
			</div>
			@endif
			<div class="search-post">
				<form method="get" action="{{url('/home')}}">
					<input type="text" class="search-input" name="search" value="{{@$search}}"/>
					<input type="submit" value="Search" class="search-btn" />
					@if ( @$search ) 
					<a href="{{ url( '/home' ) }}" class="clear-btn" >Clear</a>
					@endif
				</form>
			</div>
		</div>

		<div class="container">
			@if (Session::has('message'))
			<div class="flash alert-info">
				<p class="panel-body">
					{{ Session::get('message') }}
				</p>
			</div>
			@endif
			@if ($errors->any())
			<div class='flash alert-danger'>
				<ul class="panel-body">
					@foreach ( $errors->all() as $error )
					<li>
						{{ $error }}
					</li>
					@endforeach
				</ul>
			</div>
			@endif
			<div class="row">
				@yield('content')
			</div>
			<div class="row">
				<p>Developed By Michael Kerr for Figured</p>
			</div>
		</div>
	</body>
</html>
