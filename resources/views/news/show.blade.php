@extends('layouts.master')

@section('title', "$news->title")

@section('content')

    <div class="row">        
        <div class="col s12 m10 offset-m1 ">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">
                        {{ $news->title }}
                        @can('edit')
                            <a href="{{ url("/news/$news->id/edit") }}" class="btn-flat btn-sm"><i class="material-icons">mode_edit</i></a>
                        @endcan
                    </span>                    
                    <div class="divider"></div>
                    <div class="grey-text text-darken-2">
                         {{ $news->publish_date }} - {{ $news->author->userFullName }}                        
                    </div>
                    <br />
                    <div class="row">
                        @if (count($news->attachments) > 0)
                            <div class="col m1">Files:</div>
                        @endif
                        <div class="col m6">
                            @foreach($news->attachments as $file)
                                <span class="chip">
                                        <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                                    @can('edit')
                                        <i class="material-icons close" data-id="{{$file->id}}">close</i>
                                    @endcan
                                </span> &nbsp;
                            @endforeach
                        </div>
                    </div>
                    <div class="browser-default">
                        {!! $news->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('help')
    <p>This page displays all the details of the selected news article.</p>
    <p>The files attached to the news article can be viewed by clicking 
        on the filename.
    </p>
    @can('edit')
        <strong>Edit News</strong>
        <p> To edit a news article, click on the Edit icon next to the news title.</p>

        <strong>Delete File</strong>
        <p> To delete a file attachment, xlick on the "x" next to the filename.</p>
    @endcan
@stop
