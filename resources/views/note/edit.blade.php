@extends('layouts.master')

@section('title', 'Update A Note')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Update a Note</div>
            {!! Form::model($note, array('action' => ['NoteController@update', $user->id, $note->id], 'method' => 'PATCH', 'files' => true)) !!}
                @include('note._form', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Update a Note</h3>

@stop