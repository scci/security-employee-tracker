<?php

namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Krucas\Notification\Facades\Notification;
use SET\Attachment;
use SET\Http\Requests\NewsRequest;
use SET\News;

/**
 * Description of NewsController.
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

        return view('news.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewsRequest $request
     *
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

        $news->emailNews();
        Notification::container()->success('News Created');

        return redirect()->action('NewsController@index');
    }

    /**
     * Show the individual news article.
     *
     * @param News $news
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(News $news)
    {
        $this->authorize('show_published_news', $news);

        return view('news.show', compact('news'));
    }

    /**
     * Show the news article to be edited.
     *
     * @param $newsId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($newsId)
    {
        $this->authorize('edit');
        $news = News::findOrFail($newsId);

        return view('news.edit', compact('news'));
    }

    /**
     * Update the selected news object with the request.
     *
     * @param NewsRequest $request
     * @param News        $news
     *
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
        $news->emailNews();

        Notification::container()->success('News Updated');

        return redirect()->action('NewsController@index');
    }

    /**
     * Delete the specified news article.
     *
     * @param News $news
     */
    public function destroy(News $news)
    {
        $this->authorize('edit');

        Storage::deleteDirectory('news_'.$news->id);
        $news->delete();
    }
}
