@extends('layout')

@section('title')
{{$title}}
@endsection

@section('content')

@if ( !$posts->count() )
<div class="no-posts">No posts can be found at this moment. Try again later - Pretty please.</div>
@else
<div style="vertical-align: middle;">
	@foreach( $posts as $post )
	<div class="list-group"  style="vertical-align: middle;">
		<div class="list-group-item">
			<h3><a href="{{ url('/view/'.$post->slug) }}">{{ $post->title }}</a>
				@if(!Auth::guest() && ($post->user_id == Auth::user()->id || Auth::user()->is_admin()))
					<button class="btn"><a href="{{ url('edit/'.$post->slug)}}">Edit</a></button>
				@endif
			</h3>
			<p>{{ $post->created_at->format('M d, Y - h:i a') }} By <a href="{{ url('/author/'.$post->user_id)}}">{{ $post->author->name }}</a></p>
			
		</div>
		<div class="list-group-item" style="background-color: #fafafa; min-height: 80px;">
			<article>
				{!! str_limit($post->body, $limit = 100, $end = '... <a href='.url("/view/".$post->slug).'>Read More</a>') !!}
			</article>
		</div>
		
		@if ( $post->tags->count() )
		<div class="list-group-item">
			@foreach( $post->tags as $_tag )
			<p class="post-tag"><i class="fa fa-tag" aria-hidden="true"></i> <a href="{{url( '/home/' . $_tag->label )}}">{{$_tag->label}}</a></p>
			@endforeach
		</div>
		@else
		<div class="list-group-item">&nbsp;</div>
		@endif
	</div>
	@endforeach
	{!! $posts->render() !!}
</div>
@endif

@endsection


@section( 'tags' )
	@if ( $tags and count( $tags ) )
	<div class="popular-tags">
		<ul>
			<li class="all">Popular Tags</li>
			@foreach( $tags as $tag )
			<li class="tag"><a href="{{url( '/home/' . $tag->label )}}">{{$tag->label}}</a><p class="count-badge">{{$tag->cnt}}</p></li>
			@endforeach
		</ul>
	</div>
	@endif
@endsection
