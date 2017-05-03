@extends('layout')

@section('supertitle')
	- Edit Your Post
@endsection

@section('title')
Edit Your Post
@endsection

@section('content')

<div class="" style="width:100%;">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2>Editing @if(!old('title')){{$post->title}}@endif{{ old('title') }}</h2>
			@yield('title-meta')
		</div>
		<div class="panel-body">

			<form action="{{ url('/edit/' . $post->slug ) }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="post_id" value="{{ $post->id }}{{ old('post_id') }}">
				<div class="form-group">
					<input required="required" placeholder="Enter title here" type="text" name = "title" class="form-control" value="@if(!old('title')){{$post->title}}@endif{{ old('title') }}"/>
				</div>
				<div class="form-group">
					<input required="required" placeholder="Enter tags here" type="text" name="tags" class="form-control" value="@if(!old('tags')){{$tags}}@endif{{ old('tags') }}"/>
				</div>
				<div class="form-group">
					<textarea name='body'class="form-control">@if(!old('body')){!! $post->body !!}@endif {!! old('body') !!}</textarea>
				</div>
				<input type="submit" name='publish' class="btn btn-success" value = "Update"/>
				<a href="{{  url('/home') }}" class="btn btn-default">Back home</a>
				<a href="{{  url('delete/'.$post->id.'?_token='.csrf_token()) }}" class="btn btn-danger" style="float:right;">Delete</a>
			</form>

		</div>
	</div>
</div>
@endsection
