<?php

namespace CoderStudios\Library;

use Cache;
use Session;
use CoderStudios\Models\User as UserModel;
use GrahamCampbell\GitHub\GitHubManager;

class User extends BaseLibrary {

	public function __construct(GitHubManager $github, UserModel $user)
	{
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
        $this->user = $user;
        $this->github = $github;
	}

	public function all()
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->user->all();
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

	public function getByUsername($username)
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (env('CACHE_ENABLED',0) && Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->user()->show($username);
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

	public function getByToken($token)
	{
		$data = [];

		$key = $this->getKeyName(__function__ . '|' . $token);
		if (Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->user->where('github_access_token',$token)->first();
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

}