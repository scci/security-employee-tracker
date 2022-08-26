@if ( Session::has('created') )
    <p style="text-align: center;
                          margin-bottom:1em;
                          padding: .5em 1em;
                          border: solid #14919B;
                          background: #BEF8FD;
                          border-radius:5px;
                          border-width: thin">
        {{ Session::get('created')}} successfully created.
    </p>
@elseif(Session::has('updated'))
    <p style="text-align: center;
                          margin-bottom:1em;
                          padding: .5em 1em;
                          border: solid #DE911D;
                          background: #FFF3C4;
                          border-radius:5px;
                          border-width: thin">
        {{ Session::get('updated')}} successfully updated.
    </p>
@elseif(Session::has('destroyed'))
    <p style="text-align: center;
                          margin-bottom:1em;
                          padding: .5em 1em;
                          border: solid #F44336;
                          background: #FACDCD;
                          border-radius:5px;
                          border-width: thin">
        {{ Session::get('destroyed')}} marked as destroyed.
    </p>
@endif