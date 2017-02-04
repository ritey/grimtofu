<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Repository as Cache;
use CoderStudios\Library\Category;
use CoderStudios\Library\Thread;
use CoderStudios\Library\Comment;
use CoderStudios\Requests\Thread as ThreadRequest;
use CoderStudios\Requests\Comment as CommentRequest;

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
	public function __construct(Request $request, Cache $cache, Thread $thread, Category $category, Comment $comment)
	{
		parent::__construct($cache);
		$this->namespace = __NAMESPACE__;
		$this->basename = class_basename($this);
		$this->request = $request;
		$this->cache = $cache;
        $this->category = $category;
        $this->thread = $thread;
        $this->comment = $comment;
	}

	public function index()
	{
        $page = 1;
        $limit = 10;
        $offset = 0;
        if ($this->request->get('page'))
            $page = $this->request->get('page');
        if ($page > 1)
            $offset = (($page-1)*$limit);
        $key = $this->getKeyName(__function__ . '|' . $page);
        if (env('CACHE_ENABLED',0) && $this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $threads = $this->thread->all();
            $sliced_threads = $threads;
            if (count($threads) > $limit) {
                $sliced_threads = array_slice($threads,$offset,$limit);
                //dd($threads);
                //dd(count($sliced_threads));
            }
            $th = $this->thread->formatArray($sliced_threads);
            $vars = [
                'threads' => $this->thread->paginate($th, count($threads), $limit, $page, $this->request),
            ];
            $view = view('pages.index',compact('vars'))->render();
            $this->cache->add($key, $view, env('APP_CACHE_MINUTES',60));
        }
        return $view;
	}

    public function newThread()
    {
        if (!$this->request->session()->get('token')) {
            return redirect()->route('index')->with('error_message','You need to login to be able to create a new discussion');
        }
        $key = $this->getKeyName(__function__);
        if (env('CACHE_ENABLED',0) && $this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $vars = [
                'error_message' => '',
                'channel'       => $this->request->get('channel'),
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
        $offset = 0;
        $this->request->session()->put('channel',$channel);
        if ($this->request->get('page'))
            $page = $this->request->get('page');
        if ($page > 1)
            $offset = (($page-1)*$limit);
        $key = $this->getKeyName(__function__ . '|' . $channel . '|' . $page);
        if (env('CACHE_ENABLED',0) && $this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $threads = $this->thread->threads($channel);
            $sliced_threads = $threads;
            if (count($threads) > $limit) {
                $sliced_threads = array_slice($threads,$page,$limit);
            }
            $th = $this->thread->formatArray($sliced_threads);
            $vars = [
                'threads' => $this->thread->paginate($th, count($threads), $limit, $page, $this->request),
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
        $offset = 0;
        if ($this->request->get('page'))
            $page = $this->request->get('page');
        if ($page > 1)
            $offset = (($page-1)*$limit);
        $key = $this->getKeyName(__function__ . '|' . $channel . '|' . $thread . '|' . $page);
        if (env('CACHE_ENABLED',0) && $this->cache->has($key)) {
            $view = $this->cache->get($key);
        } else {
            $title = explode('::', $thread);
            $thread = $this->thread->show($title[1]);
            $comments = $this->comment->all($title[1]);
            $sliced_comments = $comments;
            if (count($comments) > $limit-1) {
                $sliced_comments = array_slice($comments,$page,$limit-1);
            }
            $cs = $this->comment->formatArray($sliced_comments);
            $thread['title'] = str_replace('[question_mark]','?',$thread['title']);
            $token = $this->request->session()->get('token');
            $vars = [
                'thread'        => $this->thread->formatArray([0 => $thread]),
                'comments'      => $this->comment->paginate($cs, count($comments), $limit, $page, $this->request),
                'id'            => $title[1],
                'token'         => $token,
            ];
            $view = view('pages.thread',compact('vars'))->render();
            $this->cache->add($key, $view, env('APP_CACHE_MINUTES',60));
        }
        return $view;
    }

}