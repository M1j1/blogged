<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model {

	//posts table in database
	protected $guarded = [];
	
	public function author()
	{
		return $this->belongsTo('App\User','user_id');
	}

}
