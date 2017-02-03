<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Repository as Cache;
use GuzzleHttp\Client;
use CoderStudios\Models\User;
use Github\Client as GithubClient;

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
	public function __construct(Cache $cache, Request $request, Client $httpClient, User $user)
	{
		parent::__construct($cache);
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
		$this->request = $request;
		$this->httpClient = $httpClient;
		$this->user = $user;
	}

	public function callback()
	{
		$hash = Session::pull('hash');
		if ($hash != $this->request->get('state')) {
			return redirect()->route('index');
		}
		$r = $this->httpClient->request('POST','https://github.com/login/oauth/access_token', [
			'form_params' => [
				'client_id' 		=> env('GITHUB_APP_ID'),
				'client_secret'		=> env('GITHUB_SECRET'),
				'code'				=> $this->request->get('code'),
				'redirect_uri'		=> route('callback'),
				'state'				=> $hash,
			],
		]);
		$str = $r->getBody()->getContents();
		$data = [];
		parse_str($str,$data);
		if (isset($data['access_token']) && !empty($data['access_token'])) {
			$github = new GithubClient();
			$github->authenticate($data['access_token'],null,'http_token');
			$current_user = $github->api('current_user')->show();
			Session::put('name',$current_user['login']);
			/*$user = $this->user->where('github_access_token',$data['access_token'])->first();
			if (!$user) {
		        $github = new GithubClient();
		        $github->authenticate($data['access_token'],null,'http_token');
		        $current_user = $github->api('current_user')->show();
				$this->user->create([
					'name' => $current_user['login'],
					'email' => $current_user['login'].'@'.$data['access_token'],
					'password' => bcrypt(str_random(10)),
					'github_access_token' => $data['access_token']
				]);
			}*/
			$this->request->session()->put('token',$data['access_token']);
		}
		return redirect()->route('index');
	}
}