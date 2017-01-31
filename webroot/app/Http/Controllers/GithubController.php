<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Repository as Cache;
use GuzzleHttp\Client;

class GithubController extends BaseController
{
    /**
     * Laravel Request Repository
     *
     * @var object
     */
    protected $request;

    /**
     * Create a new home controller instance.
     *
     * @return void
     */
	public function __construct(Cache $cache, Request $request, Client $httpClient)
	{
		parent::__construct($cache);
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
		$this->request = $request;
		$this->httpClient = $httpClient;
	}

	public function callback()
	{
		$hash = Session::pull('hash');
		if ($hash != $this->request->get('state')) {
			return redirect()->route('index');
		}
		$r = $this->httpClient->request('POST','https://github.com/login/oauth/access_token', [
			'client_id' 		=> env('GITHUB_APP_ID'),
			'client_secret'		=> env('GITHUB_SECRET'),
			'code'				=> $this->request->get('code'),
			'redirect_uri'		=> route('callback'),
			'state'				=> $hash,
		]);
		dd($r);
	}
}