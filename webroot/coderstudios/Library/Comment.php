<?php

namespace CoderStudios\Library;

use Cache;
use GrahamCampbell\GitHub\GitHubManager;
use Github\Client as GithubClient;
use Session;
use Carbon\Carbon;

class Comment extends BaseLibrary {

	public function __construct(GitHubManager $github)
	{
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
        $this->github = $github;
	}

	public function all($name)
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (env('CACHE_ENABLED',0) && Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->issues()->comments()->all('ritey','grimtofu', $name);
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
		$comment = $github->api('issue')->comments()->create('ritey','grimtofu', $data['id'], [
				'body' => $data['message'],
		]);
		Cache::flush();

		return $comment;
	}

	public function formatArray(array $comments)
	{
		$a = [];
		if (is_array($comments) && count($comments)) {
			foreach($comments as $item) {
	            $a[] = [
	                'body'          => $item['body'],
	                'created_at'    => Carbon::now()->subseconds(Carbon::now()->diffInSeconds(Carbon::parse($item['created_at'])))->diffForHumans(),
	                'updated_at'    => Carbon::parse($item['updated_at'])->format('d-m-Y'),
	                'username'      => $item['user']['login'],
	                'avatar'        => $item['user']['avatar_url'],
	            ];
	        }
	    }
        return $a;
	}
}