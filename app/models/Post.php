<?php

class Post extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'body', 'id'];

	public function setIdAttribute($value)
    {
    	$this->attributes['id'] = Uuid::generate(4)->string;
    }
}
