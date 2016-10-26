<!-- Form Validation Errors -->
@if( sizeof($errors->all()) )
    <div class="card-panel white-text red" role="alert">
        @foreach ($errors->all() as $error)
            {{ $error }} <br />
        @endforeach
    </div>
@endif
<!-- session/notifications -->
@notification()