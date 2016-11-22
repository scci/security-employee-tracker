@extends('vendor.installer.layouts.master')

@section('title', trans('messages.environment.title'))
@section('container')
    @if (session('message'))
    <p class="alert">{{ session('message') }}</p>
    @endif
    <form method="post" action="{{ url('install/environment') }}">
        {!! csrf_field() !!}

        {!! Form::label('APP_URL', 'Application URL') !!}
        {!! Form::text('APP_URL', $fields['APP_URL']) !!}

        {!! Form::label('MAIL_FROM_ADDRESS', 'Application Email Address') !!}
        {!! Form::text('MAIL_FROM_ADDRESS', $fields['MAIL_FROM_ADDRESS']) !!}

        {!! Form::label('MAIL_FROM_NAME', 'Application Email Name') !!}
        {!! Form::text('MAIL_FROM_NAME', $fields['MAIL_FROM_NAME']) !!}

        <br />

        @if($fields['DB_CONNECTION'])
            {!! Form::label('DB_CONNECTION', 'DATABASE TYPE') !!}
            {!! Form::select('DB_CONNECTION', [
                'sqlite' => 'SQLite',
                'mysql' => 'MySQL',
                'postgres' => 'Postgres',
                'sqlsrv' => 'SQL Server',
            ], $fields['DB_CONNECTION'], ['class' => 'db_select']) !!}
        @endif

        <div class="db_fields">

            {!! Form::label('DB_HOST', 'Database Host') !!}
            {!! Form::text('DB_HOST', $fields['DB_HOST']) !!}

            {!! Form::label('DB_DATABASE', 'Database Name') !!}
            {!! Form::text('DB_DATABASE', $fields['DB_DATABASE']) !!}

            {!! Form::label('DB_USERNAME', 'Database Username') !!}
            {!! Form::text('DB_USERNAME', $fields['DB_USERNAME']) !!}

            {!! Form::label('DB_PASSWORD', 'Database Password') !!}
            <input type="password" value="{{$fields['DB_PASSWORD']}}" name="DB_PASSWORD" class="showpassword" />
            <button type="button" class="toggle-button button button--light buttons--right">Show</button>

        </div> <br />

        @if($fields['MAIL_DRIVER'])
            {!! Form::label('MAIL_DRIVER', 'Mail Driver') !!}
            {!! Form::select('MAIL_DRIVER', [
                'mail' => 'PHP Mail',
                'sendmail' => 'sendmail',
                'smtp' => 'SMTP',
            ], $fields['MAIL_DRIVER'], ['class' => 'mail_select']) !!}
        @endif

        <div class="mail_fields">

            {!! Form::label('MAIL_HOST', 'Mail Host') !!}
            {!! Form::text('MAIL_HOST', $fields['MAIL_HOST']) !!}

            {!! Form::label('MAIL_PORT', 'Mail Port') !!}
            {!! Form::text('MAIL_PORT', $fields['MAIL_PORT']) !!}

            {!! Form::label('MAIL_USERNAME', 'Mail Username') !!}
            {!! Form::text('MAIL_USERNAME', $fields['MAIL_USERNAME']) !!}

            {!! Form::label('MAIL_PASSWORD', 'Mail Password') !!}
            <input type="password" value="{{$fields['MAIL_PASSWORD']}}" name="MAIL_PASSWORD" class="showpassword" />
            <button type="button" class="toggle-button button button--light buttons--right">Show</button>

        </div>


        <div class="buttons">
            <button class="button" type="submit">
                {{ trans('messages.next') }}
            </button>
        </div>
    </form>


    <style>
        .showpassword {
            width: 72% !important;
            display: inline-block !important;
            margin-right: 10px;
        }
        .mail_fields {
            display:none;
        }
    </style>

    <script>

        hideFieldsIf('.db_fields', 'sqlite', $(".db_select"));
        showFieldsIf('.mail_fields', 'smtp', $(".mail_select"));

        $(function () {

            $(".db_select").change(function() {
                hideFieldsIf('.db_fields', 'sqlite', this);
            });

            $(".mail_select").change(function() {
                showFieldsIf('.mail_fields', 'smtp', this);
            });

            $(".showpassword").each(function (index, input) {
                var $input = $(input);
                $(this).siblings(".toggle-button").click(function () {
                    var change = "";
                    if ($(this).html() === "Show") {
                        $(this).html("Hide")
                        change = "text";
                    } else {
                        $(this).html("Show");
                        change = "password";
                    }
                    var rep = $("<input type='" + change + "' />")
                            .attr("id", $input.attr("id"))
                            .attr("name", $input.attr("name"))
                            .attr('class', $input.attr('class'))
                            .val($input.val())
                            .insertBefore($input);
                    $input.remove();
                    $input = rep;
                }).insertAfter($input);
            });
        });

        function hideFieldsIf(target, value, $this)
        {
            var selected = $($this).val();
            if (selected == value) {
                $(target).hide();
            } else {
                $(target).show();
            }
        }

        function showFieldsIf(target, value, $this)
        {
            var selected = $($this).val();
            if (selected != value) {
                $(target).hide();
            } else {
                $(target).show();
            }
        }
    </script>
@stop