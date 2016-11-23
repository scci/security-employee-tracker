<div class="row">
    <div class="input-field col s12" id="news_title">
       {!! Form::label('title', 'Title:') !!}
       {!! Form::text('title', null) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6" id="publish_date">        
        {!! Form::label('publish_date', 'Publish Date:') !!}
        {!! Form::date('publish_date', $submit == 'Update' ? null : Carbon\Carbon::today(), ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6" id="expire_date">        
        {!! Form::label('expire_date', 'Expires On:') !!}
        {!! Form::date('expire_date', null, ['class' => 'datepicker']) !!}
    </div>
</div>
<div class="row">
    <div class="input-field col s12" id="news_description">
        {!! Form::label('description', 'Description:', ['class' => 'active']) !!}
        {!! Form::textarea('description', null, ['class' => 'wysiwyg']) !!}
    </div>
</div>
<div class="row">
    <div class="col s12" id="file_upload">
        {!! Form::multipleFiles() !!}
        @if (isset($news))
            Attachments:
            @foreach($news->attachments as $file)
                <span class="chip">
                    <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                    <i class="material-icons close" data-id="{{$file->id}}">close</i>
                </span> &nbsp;
            @endforeach
        @endif
    </div>
</div>
<div class="row">
    <div class="col m4 s12">
            {!! Form::hidden('send_email', 0) !!}
            <input type="checkbox" name="send_email" value=1 class="filled-in" id="send_email"
                   @if( old('send_email') || ( $submit == 'Update' && $news->send_email) )
                        checked
                   @endif
            />
            <label for="send_email">Email news on publish date</label>
    </div>    
</div>

<div class="row">
    <div class="col s12 right-align">
        {!! Form::reset('Reset', array('class' => 'btn-flat waves-effect waves-indigo', 'id' => 'training-reset')) !!}
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>

<script>
    new SimpleMDE({spellChecker: false});
</script>
