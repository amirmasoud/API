<?php

class PostController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Post::all();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// create new post
		return Post::create($this->postRequest(Request::all()));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int/array/string  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Post::where('uuid', $id)
			->orWhere('id', $id)
			->get();
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// get post
		$post = $this->getPost($id);

		// update post just for title and desc
		$post = $post->update( $this->postRequest( Input::only('title', 'description'), $post->uuid ) );

		// return result boolean/error
		return ['updated' => $post];
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
		$post = Post::destroy(explode(',', $id));

		// delete base on uuid
		$post += Post::whereIn('uuid', explode(',', $id))->delete();

		// return number of rows that deleted
		return ['deleted' => $post];
	}

	/**
	 * add UUID to request array
	 * @param  array $request current request
	 * @return array          add uuid element to request array
	 */
	private function postRequest($request, $uuid = '')
	{
		if ($uuid == '')
			$request['uuid'] = Uuid::generate(4)->string;
		else
			$request['uuid'] = $uuid;

		// validate request
		$validator = Validator::make(
		    $request,
		    array(
		        'title' => 'required',
		        'description' => 'required|min:3',
		        'uuid' => 'required'
		    )
		);

		// if failed throw new exception with error message
		if ($validator->fails())
		{
			throw new Exception($validator->messages());
		}

		return $request;
	}	


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	private function getPost($id)
	{
		// return post base on the post id
		if ( is_numeric($id) )
			return Post::findOrFail($id);

		// return post based on uuid
		else
			return Post::where('uuid', $id)->firstOrFail();
	}
}
