<?php

namespace CoderStudios\Composers;

use Session;
use Illuminate\Contracts\View\View;
use CoderStudios\Library\Category;

class MasterComposer {

    /*
    |--------------------------------------------------------------------------
    | Admin Master Composer Class
    |--------------------------------------------------------------------------
    |
    | Loads variables for the master layout in one place
    |
    */

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

	public function compose(View $view)
	{
        $view->with('categories',$this->category->all());
		$view->with('success_message', Session::pull('success_message'));
		$view->with('error_message', Session::pull('error_message'));
		$view->with('csrf_error', Session::pull('csrf_error'));
        $view->with('token', Session::get('token'));
        $hash = str_random(10);
        Session::put('hash',$hash);
        $view->with('register_url','https://github.com/login/oauth/authorize?client_id='.env('GITHUB_APP_ID').'&redirect_uri='.route('callback').'&state='.$hash);
	}
}