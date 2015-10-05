<?php

class postRepository
{
	/**
	 * Get all posts
	 * @param  int $perPage $posts
	 * @return JSON
	 */
	public function getPosts($perPage)
	{
		// get posts
		$posts = Post::paginate($perPage);

		// paginatation array
        $postsResponse['total']        = $posts->getTotal();
        $postsResponse['per_page']     = $posts->getPerPage();
        $postsResponse['current_page'] = $posts->getCurrentPage();
        $postsResponse['last_page']    = $posts->getLastPage();
        $postsResponse['from']         = $posts->getFrom();
        $postsResponse['to']           = $posts->getTo();

        // self link
		$postsResponse['links'] = ['self' => action('PostController@getPosts')];

		// data param
		foreach ($posts as $post) :
			$postsResponse['data'][] = [
										'type' => 'posts',
										'id' => $post->id,
										'attributes' => $post
										];
		endforeach;

		// Create Response header
		$response = Response::make($postsResponse, 200);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * add UUID to existing request
	 * @return Request
	 */
	public function prepareRequest()
	{
		// get all input
		$request = Request::all();

		// attach uuid as id
		$request['id'] = Uuid::generate(4)->string;

		return $request;
	}

	/**
	 * create post validating ruls
	 * @param  Request $request
	 * @return array/null
	 */
	public function createPostValidation($request)
	{
		// validate request for create
		return Validator::make(
		    $request,
		    array(
		        'title' => 'required',
		        'body' => 'required|min:3',
		    )
		);
	}

	/**
	 * if create post validation failed echo error details
	 * @param  Request $request
	 * @param  Array $validator
	 * @return JSON            
	 */
	public function createPostValidationFailed($request, $validator)
	{
		// error array
		$error = [
				'error' =>
					[
						'code' => 500,
						'title' => Lang::get('post.validationErr'),
						'detail' => $validator->messages(),
						'source' => [
									'parameter' => $request
									]
					]
				];

		// validation error header
		$response = Response::make($error, 500);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * After post creatation was successful this function will return created post in Json
	 * @param  collection $post current post created
	 * @return JSON       
	 */
	public function createPostOkResponse($post)
	{
		// Create JSON API Response
		$postResponse = array(
			'type' => 'posts',
			'id' => $post->id,
			'attributes' => $post,
			'links' => array(
						'self' => action('PostController@getPost', $post->id)
						)
			);

		// Create Response header
		$response = Response::make($postResponse, 201);
		$response->header('Location', action('PostController@getPost', $post->id));
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * server unexpected error during post creation
	 * will return error json with error desc
	 * @return JSON
	 */
	public function createPostUnexpectedError()
	{
		// if unexpected error occured
		// error array
		$error = [
				'error' => ['title' => Lang::get('post.createErr')]
				];

		// Error response header
		$response = Response::make($error, 500);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * return a single post
	 * @param  collection $post
	 * @return JSON
	 */
	public function getPost($post)
	{
		// Create JSON API Response
		$postsResponse['data'] = [
									'type' => 'posts',
									'id' => $post->id,
									'attributes' => $post
									];

		// Create Response header
		$response = Response::make($postsResponse, 200);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * custom 404 not found
	 * @param  string/array $param parameter that cause the error
	 * @return JSON
	 */
	public function notFound($param)
	{
		// error array
		$error = [
				'error' =>
					[
						'code' => 404,
						'title' => Lang::get('post.notFoundTitle'),
						'detail' => Lang::get('post.notFoundDetail'),
						'source' => [
									'parameter' => $param
									]
					]
				];

		// Error response header
		$response = Response::make($error, 404);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	public function updatePostValidation()
	{
		// validate request for create
		return Validator::make(
		    Input::only('title', 'body'),
		    array(
		        'title' => 'required',
		        'body' => 'required|min:3',
		    )
		);
	}

	/**
	 * return validation error on update request
	 * @param  array $validator
	 * @return json
	 */
	public function updatePostValidationFailed($validator)
	{
		// error array
		$error = [
				'error' =>
					[
						'code' => 500,
						'title' => 'validation error.',
						'detail' => $validator->messages(),
						'source' => ['parameter' => Input::only('title', 'body')]
					]
				];

		// validation error header
		$response = Response::make($error, 500);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * Return successful update request
	 * @return json
	 */
	public function updatePostOkResponse()
	{
		// return result boolean/error
		$response = Response::make('', 204);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * server unexpected error during post update
	 * will return error json with error desc
	 * @return JSON
	 */
	public function updatePostUnexpectedError()
	{
		// if unexpected error occured
		// error array
		$error = [
				'error' =>	['title' => Lang::get('post.updateErr')]
				];

		// Error response header
		$response = Response::make($error, 500);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}

	/**
	 * Return successful delete request
	 * @return json
	 */
	public function deletePostOkResponse()
	{
		// resource deleted successfuly
		// Header response header
		$response = Response::make('', 204);
		$response->header('Content-Type', 'application/vnd.api+json');

		return $response;
	}
}