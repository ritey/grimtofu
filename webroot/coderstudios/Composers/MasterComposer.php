<?php

namespace CoderStudios\Composers;

use Session;
use Illuminate\Contracts\View\View;
use CoderStudios\Library\Category;
use CoderStudios\Library\User;
use Illuminate\Http\Request;

class MasterComposer {

    /*
    |--------------------------------------------------------------------------
    | Admin Master Composer Class
    |--------------------------------------------------------------------------
    |
    | Loads variables for the master layout in one place
    |
    */

    public function __construct(Request $request, Category $category, User $user)
    {
        $this->category = $category;
        $this->user = $user;
        $this->request = $request;
    }

	public function compose(View $view)
	{
        $view->with('categories',$this->category->all());
		$view->with('success_message', Session::pull('success_message'));
		$view->with('error_message', Session::pull('error_message'));
		$view->with('csrf_error', Session::pull('csrf_error'));
        $view->with('channel', Session::get('channel'));
        $view->with('request', $this->request);
        $token = Session::get('token');
        $view->with('token', $token);
        $name = null;
        if ($token) {
            $name = Session::get('name');
        }
        $view->with('name',$name);
	}
}