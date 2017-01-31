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
        $threads = $this->thread->all();
        $vars = [
            'threads' => $threads,
        ];
        return view('pages.index',compact('vars'));
	}

    public function newThread()
    {
        $vars = [
            'error_message' => '',
            'categories' => $this->category->all(),
            'request'   => $this->request,
        ];
        return view('pages.new',compact('vars'));
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
        $threads = $this->github->issues()->all('ritey','grimtofu', ['state' => 'open', 'labels' => $channel ]);
        $vars = [
            'threads' => $threads,
        ];
        return view('pages.threads',compact('vars'));
    }

    public function thread($channel = '',$thread = '')
    {
        $title = explode('::', $thread);
        $thread = $this->github->issues()->show('ritey','grimtofu', $title[1]);
        $categories = $this->github->issues()->labels()->all('ritey','grimtofu');
        $comments = $this->github->issues()->comments()->all('ritey','grimtofu', $title[1]);
        $vars = [
            'categories'    => $categories,
            'thread'        => [0 => $thread],
            'comments'      => $comments,
            'id'            => $title[1],
        ];
        return view('pages.thread',compact('vars'));
    }

}