<?php

class PostController extends \BaseController {

	/**
	 * param for class injection
	 * @var obj
	 */
	private $postService;

	function __construct(postService $postService)
	{
		/**
		 * instaciate class injection
		 * @var obj
		 */
		$this->postService = $postService;
	}

	/**
	 * get all posts, 15 per page
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->postService->getPosts( 15 );
	}

	/**
	 * create a new post
	 *
	 * @return Response
	 */
	public function store()
	{
		// generate and add UUID to request for mass assignment
		$request = $this->postService->prepareRequest();

		// Validate post
		$validator = $this->postService->createPostValidation($request);

		// if validation failed
		if ($validator->fails())
			return $this->postService->createPostValidationFailed($request, $validator);

		// create new post
		$post = Post::create($request);
		
		// if post actullay created return 201 status
		if ( ! is_null($post) )
			return $this->postService->createPostOkResponse($post);

		// if not return 500 server error
		return $this->postService->createPostUnexpectedError();
	}

	/**
	 * Show single post.
	 *
	 * @param  int/array/string  $id
	 * @return Response
	 */
	public function show($id)
	{
		// get post
		$post = Post::find($id);

		// if post exists
		if ( ! is_null($post) )
			return $this->postService->getPost($post);

		// if resource not found
		return $this->postService->notFound($id);
	}

	/**
	 * Update the specified resource in storage.
	 * @todo if post table parameter are more that 2, it's better to create a functin for request array
	 *       insted of getting Input instance.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// get post
		$post = Post::find($id);

		if ( ! is_null($post) ) :
			$validator = $this->postService->updatePostValidation();

			// if failed throw new exception with error message
			if ($validator->fails())
				return $this->postService->updatePostValidationFailed($validator);

			// update specefic resource
			$post = $post->update( Input::only('title', 'body') );

			// if post successfuly updated
			if ($post)
				return $this->postService->updatePostOkResponse();

			// else return error
			return $this->postService->updatePostUnexpectedError();
		else :

			// if resource not found
			return $this->postService->notFound($id);
		endif;
		
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int/array  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// delete base on id
		$post = Post::destroy( explode (',', $id) );

		if ( $post == 0 )
			// no resource deleted, return error object
			return $this->postService->notFound(explode (',', $id));

		// otherwise return delete response
		return $this->postService->deletePostOkResponse();
	}
}
