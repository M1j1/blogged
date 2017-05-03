@extends('layout')

@section('title')
	@if($post)
		{{ $post->title }}
	@else
		Page does not exist
	@endif
@endsection

@section('title-meta')
<p>{{ $post->created_at->format('M d,Y \a\t h:i a') }} By <a href="{{ url('/user/'.$post->author_id)}}">{{ $post->author->name }}</a></p>
@endsection

@section('content')

@if($post)
	<div class="post-view">
		<h2>
			{{ $post->title }}
			@if(!Auth::guest() && ($post->user_id == Auth::user()->id || Auth::user()->is_admin()))
				<button class="btn" style="float: right"><a href="{{ url('edit/'.$post->slug)}}">Edit</a></button>
			@endif
		</h2>

		<div class="post-body">{!! $post->body !!}</div>
		<a href="{{ url('home')}}" class="btn btn-default">Back</a>
	</div>

@else
404 error
@endif

@endsection
