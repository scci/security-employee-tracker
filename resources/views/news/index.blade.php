@extends('layouts.master')

@section('title', 'News')


@section('content')
    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/news/create') }}" data-position="left" data-tooltip="Add News">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endcan

    <div class="card">
        @if (session('status'))
            <script>
                Materialize.toast("{{ @session('status') }}", 4000);
            </script>
        @endif
        <div class="card-content">
            <span class="card-title">News</span>
            <table class="row-border hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Publish Date</th>
                        @can('edit')
                            <th>Expire Date</th>
                            <th>Email</th>
                        @endcan
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allNews as $news)
                        <tr class="news-{{$news->id}}">
                            <td><a href="{{ url('/news', $news->id) }}">{{ $news->title }}</a></td>
                            <td class="text-nowrap">{{ $news->publish_date }}</td>
                            @can('edit')
                                <td class="text-nowrap">{{ $news->expire_date }}</td>
                                <td>
                                    @if($news->send_email &&  Carbon\Carbon::now()->gte(Carbon\Carbon::createFromFormat('Y-m-d', $news->publish_date)))
                                        <i class="small material-icons green-text tooltipped" data-tooltip="Sent">check</i>
                                    @elseif($news->send_email)
                                        <i class="small material-icons amber-text text-darken-1 tooltipped" data-tooltip="Will send on publish date.">query_builder</i>
                                    @else
                                        <i class="small material-icons tooltipped" data-tooltip="Don't Send">close</i>
                                    @endif
                                </td>
                            @endcan
                            <td class="no-wrap">
                                <div class="action-buttons">
                                @can('edit')
                                    <a href="{{ url("/news/$news->id/edit") }}" class="btn-flat btn-sm tooltipped" data-position="top" data-tooltip="Edit">
                                        <i class="material-icons">mode_edit</i>
                                    </a>
                                    <button type="button" class="btn-flat btn-sm delete-news tooltipped" data-id="{{ $news->id }}" data-position="top" data-tooltip="Delete">
                                        <i class="material-icons">delete</i>
                                    </button>
                                @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>    
    
@stop

@section('help')
    <p>The News page displays a list of all the published news articles.</p>
    
    @can('edit')
    <p>The Admin can see both published and unpublished news articles.</p>
    <strong>Add News</strong>
    <p>
       To add a news article, click on the Add News button (big red button) 
       on the bottom right of your screen. 
    </p>

    <strong>Edit News</strong>
    <p> To edit a news article, hover the mouse over a row to enable the edit 
        icon at the end of the row. Click on the Edit icon to edit the news article.
    </p>
    
    <strong>Delete News</strong>
    <p> To delete a news article, hover the mouse over a row to enable the 
        delete icon at the end of the row. Click on the Delete icon to delete 
        the news article.
    </p>
    @endcan    

    <strong>View News</strong>
    <p>
        Click on the News Title to view the complete news article.
    </p>   

@stop
