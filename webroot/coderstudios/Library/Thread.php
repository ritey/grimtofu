<?php

namespace CoderStudios\Library;

use Cache;
use GrahamCampbell\GitHub\GitHubManager;
use Github\Client as GithubClient;
use Session;

class Thread extends BaseLibrary {

	public function __construct(GitHubManager $github)
	{
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
        $this->github = $github;
	}

	public function all()
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->issues()->all('ritey','grimtofu', ['state' => 'open']);
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

	public function create($data)
	{
		$token = Session::get('token');
		if (!$token) { return; }
		$github = new GithubClient();
		$github->authenticate($token,null,'http_token');
		$github->api('current_user')->issues()->create('ritey','grimtofu', [
				'title' => $data['title'],
				'body' => $data['message'],
				'labels' => [
					$data['category']
				],
		]);
	}

	public function threads($channel)
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->issues()->all('ritey','grimtofu', ['state' => 'open', 'labels' => $channel ]);
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

	public function show($number)
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->issues()->show('ritey','grimtofu', $number);
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

}