@extends('vendor.installer.layouts.master')

@section('title', trans('messages.welcome.title'))
@section('container')
    <p class="paragraph">{{ trans('messages.welcome.message') }}</p>
    <div class="buttons">
        <a href="{{ action('Installation\EnvironmentController@index') }}" class="button">{{ trans('messages.next') }}</a>
    </div>
@stop