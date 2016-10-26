@extends('layouts.master')

@section('title', 'Add A Note')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Add a Note</div>

            {!! Form::open(array('action' => ['NoteController@store', $user->id], 'method' => 'POST', 'files' => true)) !!}
                @include('note._form', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Add a Note</h3>

@stop