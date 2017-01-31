<?php

namespace CoderStudios\Library;

use Cache;
use GrahamCampbell\GitHub\GitHubManager;

class Comment extends BaseLibrary {

	public function __construct(GitHubManager $github)
	{
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
        $this->github = $github;
	}

	public function create($data)
	{
		return $this->github->issues()->comments()->create('ritey','grimtofu', $data['id'],
			[
				'body' => $data['message'],
			]
		);
	}

}