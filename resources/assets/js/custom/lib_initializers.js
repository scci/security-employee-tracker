/**
 * Created by sdibble on 10/15/2015.
 *
 * Initialize basic jQuery Libraries.
 */

$( document ).ready(function() {

    //Material Design Initializers
    $(".button-collapse").sideNav();
    $(".tooltipped").tooltip();
    $(".modal-trigger").leanModal();
    $(".dropdown-button").dropdown({
        hover: true,
        belowOrigin: true,
    });
    $(".datepicker").pickadate({
        onSet: function( arg ){
            if ( "select" in arg ){ //prevent closing on selecting month/year
                this.close();
            }
        },
        format: "yyyy-mm-dd",
        container: "body",
        selectYears: true,
        selectMonths: true
    });

    //data table
    $(".data-table").DataTable( {
        paging: false
    });

    //Hide the alerts after a few seconds.
    $("div.alert").not(".note").delay(8000).slideUp(500);

    //If we have an ajax call, send our CSRF token.
    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content") }
    });

    //upload training/user documents
    $("#js-upload").change(function() {
        $("#attachments-form").submit();
    });

    $("select").material_select();
});
