<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\User;
use Auth;

class PostRequest extends Request {

	/**
	 * 
	 */
	public function authorize() {
		if ( true ) {
			return true;
		}
		return false;
	}

	/**
	 *
	 */
	public function rules() {
		return [
			'title' => 'required|unique:posts|max:100',
			'title' => array('Regex:/^[A-Za-z0-9 ]+$/'),
			'tags' => 'required|max:100',
			'body' => 'required',
		];
	}	
}
?>