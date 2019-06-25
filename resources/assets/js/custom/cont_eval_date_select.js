/**
 * Created by sketa on 6/24/2019.
 * resources/views/user/_new_user.blade.php
 * When a user updates the cont_eval selection,
 * show/hide cont_eval_date field.
 */

$(document).ready(function() {
    $('cont_eval_field').change(function(){
        $( "cont_eval_field option:selected").each(function() {
            if ($(this).attr("value") == '1') {
                $("#cont_eval_date_field").show();
                $("#cont_eval_date_label").show();
                $("#cont_eval_date").show();
            }
            else {
                $("#cont_eval_date_field").hide();
                $("#cont_eval_date_label").hide();
                $("#cont_eval_date").hide();
            }
        });
    }).change();
 });
