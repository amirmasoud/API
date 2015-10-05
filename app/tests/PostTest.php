<?php

class PostTest extends TestCase {

	/**
	 * Test index method
	 * it should return json response including every posts
	 *
	 * @return void
	 */
	public function testIndexMethod()
	{
		$response = $this->action('GET', 'PostController@index');

		$this->isJson($response);
	}
}
