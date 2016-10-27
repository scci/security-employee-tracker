<div id="jpas-form" class="modal bottom-sheet">
    <div class="modal-content">
        <div class="row">
            <div class="col s12">

            </div>
        </div>
        <div class="row">
        {!! Form::open(array('action' => 'UserController@import', 'method' => 'POST', 'files' => true, 'class' => 'form-horizontal jpas-form')) !!}
            {!! Form::hidden('resolveImport', 0) !!}
            <div class="col s12 m7">
                <div class="file-field input-field">
                    <div class="btn">
                        <span>File</span>
                        {!! Form::file('file') !!}
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Excel JPAS Format">
                    </div>
                </div>
            </div>
            <div class="col s12 m5 input-field">
                {!! Form::submit('Upload', array('class' => 'btn waves-effect waves-indigo')) !!}
                <a href="#!" class="modal-action modal-close waves-effect waves-indigo btn-flat right">Close</a>
            </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
