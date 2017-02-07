<?php

namespace CoderStudios\Library;

use Cache;
use GrahamCampbell\GitHub\GitHubManager;
use Github\Client as GithubClient;
use Session;
use Carbon\Carbon;
use Parsedown;

class Thread extends BaseLibrary {

	public function __construct(GitHubManager $github, Parsedown $parsedown)
	{
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
        $this->github = $github;
        $this->parsedown = $parsedown;
	}

	public function all()
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (env('CACHE_ENABLED',0) && Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->issues()->all('ritey','grimtofu', ['state' => 'open', 'sort' => 'updated']);
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
		$result = $github->api('current_user')->notifications()->removeSubscription($issue['number']);
		try {
			if (strtolower($data['category']) === 'all') {
				$this->github->issues()->update('ritey','grimtofu', $issue['number'],['labels' => ['General forum']]);
			} else {
				$this->github->issues()->update('ritey','grimtofu', $issue['number'],['labels' => [$data['category']]]);
			}
		} catch(\Exception $e) {
			Log::info('Create error' . print_r($e));
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
			$data = $this->github->issues()->all('ritey','grimtofu', ['state' => 'open', 'labels' => $channel, 'sort' => 'updated']);
			Cache::add($key, $data, (60*6));
		}

		return $data;
	}

	public function threadsByUsername($username)
	{
		$data = [];

		$key = $this->getKeyName(__function__);
		if (env('CACHE_ENABLED',0) && Cache::has($key)) {
			$data = Cache::get($key);
		} else {
			$data = $this->github->issues()->all('ritey','grimtofu', ['state' => 'open', 'creator' => $username, 'sort' => 'updated']);
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
			try {
				$data = $this->github->issues()->show('ritey','grimtofu', $number);
			} catch(\Exception $e) {
				Abort(404);
			}
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
	                'body_intro'	=> strlen($item['body']) > 200 ? '<p>'.substr($item['body'],0,200) . '...</p>' : '<p>'.$item['body'].'</p>',
	                'body'          => $this->parsedown->setMarkupEscaped(true)->text($item['body']),
	                'created_at'    => Carbon::now()->subseconds(Carbon::now()->diffInSeconds(Carbon::parse($item['created_at'])))->diffForHumans(),
	                'updated_at'    => Carbon::now()->subseconds(Carbon::now()->diffInSeconds(Carbon::parse($item['updated_at'])))->diffForHumans(),
	                'username'      => $item['user']['login'],
	                'avatar'        => $item['user']['avatar_url'],
	            ];
	        }
	    }
        return $a;
	}

}