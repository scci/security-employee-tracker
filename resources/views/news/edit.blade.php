@extends('layouts.master')

@section('title', 'News')


@section('content')
    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Edit News</div>
            {!! Form::model($news, array('action' => ['NewsController@update', $news->id], 'method' => 'PATCH', 'files' => true, 'id' => 'new-news')) !!}
                @include('news._new_news', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    <strong>Edit News</strong>
    <p>Only an Administrator can edit news articles.</p>
    
    @include('news._new_news_help')
    
    <strong>Update</strong>
    <p>To edit a news article, make changes to the above fields and click the 
        update button on the bottom right corner of the page.
    </p>
@stop