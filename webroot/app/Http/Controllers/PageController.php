<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Repository as Cache;
use GrahamCampbell\GitHub\GitHubManager;
use CoderStudios\Library\Category;
use CoderStudios\Library\Thread;
use CoderStudios\Library\Comment;
use CoderStudios\Requests\Thread as ThreadRequest;
use CoderStudios\Requests\Comment as CommentRequest;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class PageController extends BaseController
{

    /**
     * Laravel Request Repository
     *
     * @var object
     */
    protected $request;

    /**
     * Laravel Cache Repository
     *
     * @var object
     */
    protected $cache;

    /**
     * Create a new home controller instance.
     *
     * @return void
     */
	public function __construct(Request $request, Cache $cache, GitHubManager $github, Thread $thread, Category $category, Comment $comment)
	{
		parent::__construct($cache);
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
		$this->request = $request;
		$this->cache = $cache;
        $this->github = $github;
        $this->category = $category;
        $this->thread = $thread;
        $this->comment = $comment;
	}

	public function index()
	{
        $page = 1;
        $limit = 10;
        if ($this->request->get('page')) {
            $page = $this->request->get('page');
        }
        $key = $this->getKeyName(__function__ . '|' . $page);
        if ($this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $threads = $this->thread->all();
            $t = new Paginator(array_slice($threads,$page,$limit),count($threads),$limit,$page, [
                'path'  => $this->request->url(),
                'query' => $this->request->query(),
            ]);
            $vars = [
                'threads' => $t,
            ];
            $view = view('pages.index',compact('vars'))->render();
            $this->cache->add($key, $view, env('APP_CACHE_MINUTES',60));
        }
        return $view;
	}

    public function newThread()
    {
        if (!$this->request->session()->get('token')) {
            return redirect()->route('index');
        }
        $key = $this->getKeyName(__function__);
        if ($this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $vars = [
                'error_message' => '',
                'categories'    => $this->category->all(),
                'request'       => $this->request,
            ];
            $view = view('pages.new',compact('vars'))->render();
            $this->cache->add($key, $view, env('APP_CACHE_MINUTES',60));
        }
        return $view;
    }

    public function saveThread(ThreadRequest $request)
    {
        $this->thread->create($request->only('title','category','message'));
        return redirect('index');
    }

    public function saveComment(CommentRequest $request)
    {
        $title = explode('::',$request->server->get('HTTP_REFERER'));
        $data = $request->only('message');
        $data['id'] = $title[1];
        $this->comment->create($data);
        return redirect($request->server->get('HTTP_REFERER'));
    }

    public function channel($channel = '')
    {
        $page = 1;
        $limit = 10;
        if ($this->request->get('page')) {
            $page = $this->request->get('page');
        }
        $key = $this->getKeyName(__function__ . '|' . $channel . '|' . $page);
        if ($this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $threads = $this->github->issues()->all('ritey','grimtofu', ['state' => 'open', 'labels' => $channel ]);
            $t = new Paginator(array_slice($threads,$page,$limit),count($threads),$limit,$page, [
                'path' => $this->request->url(),
                'query' => $this->request->query(),
            ]);
            $vars = [
                'threads' => $t,
            ];
            $view = view('pages.threads',compact('vars'))->render();
            $this->cache->add($key, $view, env('APP_CACHE_MINUTES',60));
        }
        return $view;
    }

    public function thread($channel = '',$thread = '')
    {
        $page = 1;
        $limit = 10;
        if ($this->request->get('page')) {
            $page = $this->request->get('page');
        }
        $key = $this->getKeyName(__function__ . '|' . $channel . '|' . $thread . '|' . $page);
        if ($this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $title = explode('::', $thread);
            $thread = $this->github->issues()->show('ritey','grimtofu', $title[1]);
            $categories = $this->github->issues()->labels()->all('ritey','grimtofu');
            $comments = $this->github->issues()->comments()->all('ritey','grimtofu', $title[1]);
            $c = new Paginator(array_slice($comments,$page,$limit),count($comments),$limit,$page, [
                'path' => $this->request->url(),
                'query' => $this->request->query(),
            ]);
            $thread['title'] = str_replace('[question_mark]','?',$thread['title']);
            $token = $this->request->session()->get('token');
            $vars = [
                'categories'    => $categories,
                'thread'        => [0 => $thread],
                'comments'      => $c,
                'id'            => $title[1],
                'token'         => $token,
            ];
            $view = view('pages.thread',compact('vars'))->render();
            $this->cache->add($key, $view, env('APP_CACHE_MINUTES',60));
        }
        return $view;
    }

}