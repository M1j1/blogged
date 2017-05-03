@extends('layout')

@section('supertitle')
	- Add a New Post
@endsection

@section('title')
	Add a New Post
@endsection

@section('content')

<div class="" style="width:100%;">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2>Add a New Post</h2>
			@yield('title-meta')
		</div>
		<div class="panel-body">

			<form action="{{ url('/write') }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="form-group">
					<input required="required" value="{{ old('title') }}" placeholder="Enter a Title here" type="text" name="title" class="form-control" />
				</div>
				<div class="form-group">
					<input required="required" value="{{ old('tags') }}" placeholder="Enter some Tags here (comma seperated please)" type="text" name="tags" class="form-control" />
				</div>
				<div class="form-group">
					<textarea name="body" class="form-control" placeholder="Blog goes here :P">{{ old('body') }}</textarea>
				</div>
				<input type="submit" name='publish' class="btn btn-success" value="Save"/>
				<a href="{{ url( '/home' ) }}" class="btn btn-default" >Cancel</a>
			</form>

		</div>
	</div>
</div>

@endsection
