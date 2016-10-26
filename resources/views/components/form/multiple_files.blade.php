<div class="file-field input-field">
    <div class="btn" id="{{$id}}">
        <span>File</span>
        {!! Form::file('files[]', array('multiple' => true)) !!}
    </div>
    <div class="file-path-wrapper">
        <input class="file-path validate" type="text" placeholder="Upload one or more files">
    </div>
</div>