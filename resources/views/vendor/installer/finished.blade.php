@extends('vendor.installer.layouts.master')

@section('title', 'Installation Complete')
@section('container')
    <p class="paragraph">Access to this installer is now disabled.</p>
    <div class="buttons">
        <a href="{{ url('/') }}" class="button">{{ trans('messages.final.exit') }}</a>
    </div>
@stop