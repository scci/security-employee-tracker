<?php namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SET\News;
use SET\User;
use Mail;
use Carbon\Carbon;
use SET\Mail\SendNewsEmail;
use SET\Attachment;
use Illuminate\Support\Facades\Gate;
use SET\Http\Requests\NewsRequest;
use Krucas\Notification\Facades\Notification;

/**
 * Description of NewsController
 *
 * @author sketa
 */
class NewsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Gate::denies('view')) {
            $allNews = News::publishedNews()->orderBy('publish_date', 'desc')->get();
        } else {
            $allNews = News::orderBy('publish_date', 'desc')->get();
        }

        return view('news.index', compact('allNews'));
    }

    public function create()
    {
        $this->authorize('edit');
        return view('news.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(NewsRequest $request)
    {
        $this->authorize('edit');
        $data = $request->all();
        $data['author_id'] = Auth::user()->id;
        $news = News::create($data);
                
        if ($request->hasFile('files')) {
            Attachment::upload($news, $request->file('files'));
        }
        
        $this->emailNews($news);      
        Notification::container()->success("News Created");        
        
        return redirect()->action('NewsController@index');
    }
    
    /**
     * Show the individual news article
     * @param $newsId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(News $news)
    {
        $this->authorize('show_published_news', $news);
        return view('news.show', compact('news')); 
    }
   
    /**
     * Show the news article to be edited
     * @param $newsId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($newsId)
    {
        $this->authorize('edit');
        $news = News::findOrFail($newsId);
        return view('news.edit', compact('news'));
    }
    
    /**
     * Update the selected news object with the request
     * @param NewsRequest $request
     * @param News $news
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(NewsRequest $request, News $news)
    {
        $this->authorize('edit');

        $data = $request->all();
        $news->update($data);
        if ($request->hasFile('files')) {
            Attachment::upload($news, $request->file('files'));
        }
        $this->emailNews($news);
        
        Notification::container()->success("News Updated");

        return redirect()->action('NewsController@index');
    }

    /**
     * Delete the specified news article
     * @param News $news
     */
    public function destroy(News $news)
    {
        $this->authorize('edit');
        
        Storage::deleteDirectory('news_' . $news->id);
        $news->delete();
    }
    
    /**
     * Email the news on the publish_date when a news article is created or updated
     * @param News $news
     */
    public function emailNews(News $news) 
    {        
        $publishDate = Carbon::createFromFormat('Y-m-d', $news->publish_date);
        
        if ($news->send_email && $publishDate->eq(Carbon::now())) {            
            $allUsers = User::skipSystem()->active()->get();
            foreach ($allUsers as $user) {
                    Mail::to($user->email)->send(new SendNewsEmail($news));                      
            }
        }        
    }
}
