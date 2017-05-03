@extends('layout')

@section('content')


<div class="" style="width:100%;">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2>Login</h2>
		</div>
		<div class="panel-body">

	<form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="form-group">
			<label class="col-md-4 control-label">E-Mail Address</label>
			<div class="col-md-6">
				<input type="email" class="form-control" name="email" value="">
			</div>
		</div>

		<div class="form-group">
			<label class="col-md-4 control-label">Password</label>
			<div class="col-md-6">
				<input type="password" class="form-control" name="password">
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
				<button type="submit" class="btn btn-primary">
					Login
				</button>
			</div>
		</div>
	</form>

		</div>
	</div>
</div>

@endsection
