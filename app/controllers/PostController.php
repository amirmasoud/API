<?php

class PostController extends \BaseController {

	/**
	 * param for class injection
	 * @var obj
	 */
	private $postRepo;

	function __construct(postRepository $postRepository)
	{
		/**
		 * instaciate class injection
		 * @var obj
		 */
		$this->postRepo = $postRepository;
	}

	/**
	 * get all posts, 15 per page
	 *
	 * @return Response
	 */
	public function getPosts()
	{
		return $this->postRepo->getPosts( 15 );
	}

	/**
	 * create a new post
	 *
	 * @return Response
	 */
	public function createPost()
	{
		// generate and add UUID to request for mass assignment
		$request = $this->postRepo->prepareRequest();

		// Validate post
		$validator = $this->postRepo->createPostValidation($request);

		// if validation failed
		if ($validator->fails())
			return $this->postRepo->createPostValidationFailed($request, $validator);

		// create new post
		$post = Post::create($request);
		
		// if post actullay created return 201 status
		if ( ! is_null($post) )
			return $this->postRepo->createPostOkResponse($post);

		// if not return 500 server error
		return $this->postRepo->createPostUnexpectedError();
	}

	/**
	 * Show single post.
	 *
	 * @param  int/array/string  $id
	 * @return Response
	 */
	public function getPost($id)
	{
		// get post
		$post = Post::find($id);

		// if post exists
		if ( ! is_null($post) )
			return $this->postRepo->getPost($post);

		// if resource not found
		return $this->postRepo->notFound($id);
	}

	/**
	 * Update the specified resource in storage.
	 * @todo if post table parameter are more that 2, it's better to create a functin for request array
	 *       insted of getting Input instance.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function updatePost($id)
	{
		// get post
		$post = Post::find($id);

		if ( ! is_null($post) ) :
			$validator = $this->postRepo->updatePostValidation();

			// if failed throw new exception with error message
			if ($validator->fails())
				return $this->postRepo->updatePostValidationFailed($validator);

			// update specefic resource
			$post = $post->update( Input::only('title', 'body') );

			// if post successfuly updated
			if ($post)
				return $this->postRepo->updatePostOkResponse();

			// else return error
			return $this->postRepo->updatePostUnexpectedError();
		else :

			// if resource not found
			return $this->postRepo->notFound($id);
		endif;
		
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int/array  $id
	 * @return Response
	 */
	public function deletePost($id)
	{
		// delete base on id
		$post = Post::destroy( explode (',', $id) );

		if ( $post == 0 )
			// no resource deleted, return error object
			return $this->postRepo->notFound(explode (',', $id));

		// otherwise return delete response
		return $this->postRepo->deletePostOkResponse();
	}
}
