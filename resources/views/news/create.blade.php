@extends('layouts.master')

@section('title', 'News')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Add News</div>
            {!! Form::open(array('action' => 'NewsController@store', 'method' => 'POST', 'files' => true, 'id' => 'new-news')) !!}
                @include('news._new_news', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    <strong>Add News</strong>
    <p>Only an Administrator can add news articles in SET.</p>
    
    @include('news._new_news_help')
    
    <strong>Create</strong>
    <p>To add a news article, enter values in the above fields and click the 
        create button on the bottom right corner of the page.
    </p>
@stop
