<?php

namespace CoderStudios\Library;

use Cache;
use GrahamCampbell\GitHub\GitHubManager;
use Github\Client as GithubClient;
use Session;

class Comment extends BaseLibrary {

	public function __construct(GitHubManager $github)
	{
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
        $this->github = $github;
	}

	public function create($data)
	{
		$token = Session::get('token');
		if (!$token) { return; }
		$github = new GithubClient();
		$github->authenticate($token,null,'http_token');
		$comment = $github->api('issue')->comments()->create('ritey','grimtofu', $data['id'], [
				'body' => $data['message'],
		]);
		Cache::flush();

		return $comment;
	}

}