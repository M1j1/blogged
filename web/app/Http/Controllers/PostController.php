<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Posttags;
use App\Tags;
use App\Posts;
use App\User;
use Redirect;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Requests\SearchRequest;

use Illuminate\Http\Request;

// note: use true and false for active posts in postgresql database
// here '0' and '1' are used for active posts because of mysql database

class PostController extends Controller {

	/**
	 *
	 */
	public function index( Request $request, $slug = false )
	{
		$tags = false;
		$posttags = Posttags::select( DB::raw( 'tag_id, COUNT( tag_id ) as cnt' ) )->groupBy( 'tag_id' )->orderBy( 'cnt', 'desc' )->paginate( 4 );
		if ( $posttags ) {
			foreach ( $posttags as $posttag ) {
				$tag = Tags::where( 'id', $posttag->tag_id )->first();
				$tag->cnt = $posttag->cnt;
				$tags[] = $tag;
			}
		}

		if ( $request->search ) {
			$posts = Posts::join('posttags', 'posts.id', '=', 'posttags.post_id')
								->join('tags', 'tags.id', '=', 'posttags.tag_id')
								->select( 'posts.*' )
								->where( 'tags.label', 'like', '%' . trim( $request->search ) . '%' )
								->orWhere( 'posts.title', 'like', '%' . trim( $request->search ) . '%' )
								->orWhere( 'posts.body', 'like', '%' . trim( $request->search ) . '%' )
								->groupBy( 'posts.id' )
								->paginate( 20 );			
		}
		else if ( $slug ) {
			$posts = Posts::join('posttags', 'posts.id', '=', 'posttags.post_id')
								->join('tags', 'tags.id', '=', 'posttags.tag_id')
								->select( 'posts.*', 'tags.label' )
								->where( 'tags.label', trim( $slug ) )
								->paginate( 20 );
		}
		else {
			$posts = Posts::where( 'status', '1' )->orderBy('created_at','desc')->paginate( 20 );
		}
		
		//
		foreach ( $posts as $key => $post ) {
			$_tags = Tags::join( 'posttags', 'tags.id', '=', 'posttags.tag_id' )
							->where( 'posttags.post_id', $post->id )->get();

			$posts[ $key ]->tags = $_tags;
		}

		$title = 'Latest Posts';
		return view( 'home' )->withPosts( $posts )->withTitle($title)->withTags( $tags )->withSearch( $request->search );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function write(Request $request)
	{
		return view('post_new');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function writePost(PostRequest $request)
	{
		$post = new Posts();
		$post->title = $request->get('title');
		$post->body = $request->get('body');
		$post->slug = str_slug($post->title);
		
		$post->views = 0;
		$post->reads = 0;
		$post->emails= 0;
		
		$duplicate = Posts::where('slug',$post->slug)->first();
		if($duplicate)
		{
			return redirect('write')->withErrors('Title already exists.')->withInput();
		}	
		
		$post->user_id = $request->user()->id;
		if($request->has('save'))
		{
			$post->status = 0;
			$message = 'Post saved successfully';			
		}			
		else 
		{
			$post->status = 1;
			$message = 'Post published successfully';
		}
		$post->save();
		
		//
		$post_tags = $request->get( 'tags' );
		
		$_tags = !empty( $post_tags ) ? explode( ',', $post_tags ) : false;
		if ( false !== $_tags ) {
			foreach ( $_tags as $tag ) {
				
				$dup_tag = Tags::where( 'label', trim( $tag ) )->first();
				
				$tag_id = 0;
				if ( $dup_tag ) {
					$tag_id = $dup_tag->id;
				}
				else {
					//
					$tags = new Tags();
					$tags->label = trim( $tag );
					$tags->user_id = $request->user()->id;
					$tags->save();
					
					//
					$tag_id = $tags->id;
					
				}
				$posttag = new Posttags();
				$posttag->post_id = $post->id;
				$posttag->tag_id = $tag_id;
				$posttag->save();
			}
		}
		
		return redirect('edit/'.$post->slug)->withMessage($message); //edit/'.$post->slug
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function view( $slug )
	{
		$post = Posts::where('slug',$slug)->first();

		if($post)
		{
			if($post->status == false)
				return redirect('/')->withErrors('requested page not found');
			
			$tags = Tags::join( 'posttags', 'tags.id', '=', 'posttags.tag_id' )
							->where( 'posttags.post_id', $post->id )->get();
		}
		else 
		{
			return redirect('/')->withErrors('requested page not found');
		}
		return view( 'post_view')->withPost($post)->withTags($tags);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Request $request,$slug)
	{
		$post = Posts::where('slug',$slug)->first();
		if($post && ($request->user() && $request->user()->id == $post->user_id || $request->user()->is_admin())) {
			
			$tags = Tags::join( 'posttags', 'tags.id', '=', 'posttags.tag_id' )
							->where( 'posttags.post_id', $post->id )->get();
			$_tags = [];
			foreach ( $tags as $tag ) {
				$_tags[] = $tag->label;
			}
		
			return view('post_edit')->withPost( $post )->withTags( implode( ', ', $_tags ) );
		}
		else 
		{
			return redirect('/')->withErrors('you have not sufficient permissions');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request)
	{
		//
		$post_id = $request->input('post_id');
		$post = Posts::find($post_id);
		if($post && ($post->user_id == $request->user()->id || $request->user()->is_admin()))
		{
			$title = $request->input('title');
			$slug = str_slug($title);
			$duplicate = Posts::where('slug',$slug)->first();
			if($duplicate)
			{
				if($duplicate->id != $post_id)
				{
					return redirect('edit/'.$post->slug)->withErrors('Title already exists.')->withInput();
				}
				else 
				{
					$post->slug = $slug;
				}
			}
			
			$post->title = $title;
			$post->body = $request->input('body');
			$post->status = 1;

			$message = 'Post updated successfully';
			$landing = $post->slug;
		
			$post->save();
			
			// clear out the current allocated tags while leaving the tags in the tags table.. possible issue with most popular tags.....
			Posttags::where( 'posttags.post_id', $post->id )->delete();

			//
			$post_tags = $request->get( 'tags' );
			
			$_tags = !empty( $post_tags ) ? explode( ',', $post_tags ) : false;
			if ( false !== $_tags ) {
				foreach ( $_tags as $tag ) {
					
					$dup_tag = Tags::where( 'label', trim( $tag ) )->first();
					
					$tag_id = 0;
					if ( $dup_tag ) {
						$tag_id = $dup_tag->id;
					}
					else {
						//
						$tags = new Tags();
						$tags->label = trim( $tag );
						$tags->user_id = $request->user()->id;
						$tags->save();
						
						//
						$tag_id = $tags->id;
						
					}
					$posttag = new Posttags();
					$posttag->post_id = $post->id;
					$posttag->tag_id = $tag_id;
					$posttag->save();
				}
			}
			
	 		return redirect( '/edit/' . $landing )->withMessage($message);
		}
		else
		{
			return redirect('/')->withErrors('you have not sufficient permissions');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request, $id)
	{
		//
		$post = Posts::find($id);
		if($post && ($post->user_id == $request->user()->id || $request->user()->is_admin()))
		{
			Posttags::where( 'posttags.post_id', $post->id )->delete();
			
			$post->delete();
			$data['message'] = 'Post deleted Successfully';
		}
		else 
		{
			$data['errors'] = 'Invalid Operation. You have not sufficient permissions';
		}
		
		return redirect('/')->with($data);
	}
}
