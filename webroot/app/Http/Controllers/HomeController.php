<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Repository as Cache;
use GrahamCampbell\GitHub\GitHubManager;
use Session;

class HomeController extends BaseController
{

    /**
     * Laravel Request Repository
     *
     * @var object
     */
    protected $request;

    /**
     * Laravel Cache Repository
     *
     * @var object
     */
    protected $cache;

    /**
     * Create a new home controller instance.
     *
     * @return void
     */
	public function __construct(Request $request, Cache $cache, GitHubManager $github)
	{
		parent::__construct($cache);
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
		$this->request = $request;
		$this->cache = $cache;
        $this->github = $github;
	}

    public function logout()
    {
        $this->request->session()->flush();
        return redirect()->route('index');
    }

    public function login()
    {
        $hash = str_random(10);
        Session::put('hash',$hash);
        return redirect('https://github.com/login/oauth/authorize?client_id='.env('GITHUB_APP_ID').'&redirect_uri='.route('callback').'&state='.$hash.'&scope=repo%20notifications');
    }

	public function index()
	{
        $this->request->session()->put('channel','');
        $key = $this->getKeyName(__function__);
        if (env('CACHE_ENABLED',0) && $this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $view = view('pages.home',compact('vars'))->render();
            $this->cache->add($key, $view, env('APP_CACHE_MINUTES',60));
        }
        return $view;
	}
}