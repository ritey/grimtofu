<?php

namespace CoderStudios\Library;

use Cache;
use GrahamCampbell\GitHub\GitHubManager;
use Github\Client as GithubClient;
use Session;
use Carbon\Carbon;

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
		if (env('CACHE_ENABLED',0) && Cache::has($key)) {
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
		$issue = $github->api('issue')->create('ritey','grimtofu', [
				'title' => str_replace('?','[question_mark]',$data['title']),
				'body' => $data['message'],
				'labels' => [
					$data['category']
				],
		]);
		try {
			if (strtolower($data['category']) !== 'all') {
				$this->github->issues()->update('ritey','grimtofu', $issue['number'],['labels' => [$data['category']]]);
			} else {
				$this->github->issues()->update('ritey','grimtofu', $issue['number'],['labels' => ['General forum']]);
			}
		} catch(\Exception $e) {

		}
		Cache::flush();
		return $issue;
	}

	public function threads($channel)
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (env('CACHE_ENABLED',0) && Cache::has($key)) {
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
		if (env('CACHE_ENABLED',0) && Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->issues()->show('ritey','grimtofu', $number);
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

	public function formatArray(array $threads)
	{
		$a = [];
		if (is_array($threads) && count($threads)) {
			foreach($threads as $item) {
	            $a[] = [
	                'number'        => $item['number'],
	                'title'         => $item['title'],
	                'comments'      => $item['comments'],
	                'clean_title'   => str_replace('[question_mark]','?',$item['title']),
	                'slug'          => str_replace(' ','-',strtolower($item['title'])) . '::' . $item['number'],
	                'label'         => isset($item['labels'][0]) ? $item['labels'][0]['name'] : '',
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