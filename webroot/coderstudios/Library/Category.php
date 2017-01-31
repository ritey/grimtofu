<?php

namespace CoderStudios\Library;

use Cache;
use GrahamCampbell\GitHub\GitHubManager;

class Category extends BaseLibrary {

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
			$data = $this->github->issues()->labels()->all('ritey','grimtofu');
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

}