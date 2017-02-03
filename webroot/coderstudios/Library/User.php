<?php

namespace CoderStudios\Library;

use Cache;
use Session;
use CoderStudios\Models\User;

class User extends BaseLibrary {

	public function __construct(User $user)
	{
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
        $this->user = $user;
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

	public function getByToken($token)
	{
		$data = [];

		$key = $this->getKeyName(__function__ . '|' . $token);
		if (Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->user->where('github_token',$token)->first();
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

}