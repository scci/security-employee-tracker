
{{-- Training Type --}}
@if ($training->trainingType)
  <div class="card">
      <div class="card-content">
          <div class="card-title">Training Type
              <a tabindex="0" role="button" data-trigger="focus" class="pull-right tooltipped"
              data-position="top" data-tooltip="This is the category type of the training." 
              aria-hidden="true"><i class="material-icons">live_help</i></a>
          </div>
          <span class="browser-default">{!! $training->trainingType->name !!}</span>
      </div>
  </div>
@endif

{{-- Auto Renew --}}
<div class="card">
        <div class="card-content">
            <div class="card-title">Auto Renew
                <a tabindex="0" role="button" data-position="bottom" data-trigger="focus" class="pull-right tooltipped"  data-position="top" data-tooltip="Training will automatically be re-assigned every X days. A 0 input will disable this feature." aria-hidden="true"><i class="material-icons">live_help</i></a>
            </div>
            Days until renewal: <span @can('edit') id="renews_in" data-type="text" data-url="{{ URL::route('training.update', ['id' => $training->id]) }}" data-pk="{{ $training->id }}" @endcan>{{ $training->renews_in }}</span>
        </div>
</div>

{{--Files--}}
        <ul class="collection with-header z-depth-1">
            <li class="collection-header" style="font-size: 24px; font-weight: 300;">Email Attachments
                <a tabindex="0" role="button" data-trigger="focus" class="pull-right tooltipped"  data-position="top" data-tooltip="When the system sends out an email regarding this training (new assignment/reminder), these files/attachments will be included as well." aria-hidden="true">
                    <i class="material-icons">live_help</i>
                </a>
            </li>
            @foreach($training->attachments as $file)
                <li class="collection-item">
                {!! Form::open(array('action' => ['AttachmentController@destroy', $file->id], 'method' => 'DELETE')) !!}
                <div class="file">
                    <button type="submit" class="btn-floating waves-effect secondary-content">
                        <i class="material-icons">delete</i>
                    </button>
                    <a class="" style="display:inline-block; max-width:calc(100% - 40px); overflow: hidden; text-overflow: ellipsis" href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                    <br /><small>Uploaded: {{ $file->created_at->format('y-m-d') }}</small>
                </div>
                {!! Form::close() !!}
                </li>
            @endforeach

            <li class="collection-item">
            {!! Form::open(array('action' => ['AttachmentController@store'], 'method' => 'POST', 'files' => true, 'class' => 'form-inline', 'id' => 'attachments-form')) !!}
                {!! Form::hidden('type', 'training') !!}
                {!! Form::hidden('id', $training->id) !!}
                {!! Form::multipleFiles('js-upload') !!}
            {!! Form::close() !!}
            </li>
        </ul>

{{--Description--}}
<div class="card">
    <div class="card-content">
        <div class="card-title">Description
            <a tabindex="0" role="button" data-trigger="focus" class="pull-right tooltipped"  data-position="top" data-tooltip="This will be the main body of the email to your assignees. It is best to input instructions on how to perform the training." aria-hidden="true"><i class="material-icons">live_help</i></a>
        </div>
        <span class="browser-default">{!! Markdown::convertToHTML($training->description) !!}</span>
    </div>
</div>
