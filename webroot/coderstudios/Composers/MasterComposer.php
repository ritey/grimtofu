<?php

namespace CoderStudios\Composers;

use Session;
use Illuminate\Contracts\View\View;
use CoderStudios\Library\Category;
use CoderStudios\Library\User;

class MasterComposer {

    /*
    |--------------------------------------------------------------------------
    | Admin Master Composer Class
    |--------------------------------------------------------------------------
    |
    | Loads variables for the master layout in one place
    |
    */

    public function __construct(Category $category, User $user)
    {
        $this->category = $category;
        $this->user = $user;
    }

	public function compose(View $view)
	{
        $view->with('categories',$this->category->all());
		$view->with('success_message', Session::pull('success_message'));
		$view->with('error_message', Session::pull('error_message'));
		$view->with('csrf_error', Session::pull('csrf_error'));
        $token = Session::get('token');
        $view->with('token', $token);
        $user = null;
        $name = null;
        if ($token) {
            $user = $this->user->getByToken($token);
            if (!empty($user)) {
                $name = $user->name;
                Session::put('name',$user->name);
            }
        }
        $view->with('name',$name);
        $hash = str_random(10);
        Session::put('hash',$hash);
        $view->with('register_url','https://github.com/login/oauth/authorize?client_id='.env('GITHUB_APP_ID').'&redirect_uri='.route('callback').'&state='.$hash.'&scope=public_repo');
	}
}