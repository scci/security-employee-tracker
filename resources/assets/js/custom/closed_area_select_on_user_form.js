/**
 * Created by sdibble on 9/16/2016.
 * resources/views/user/_new_user.blade.php
 * When a user updates the group selection,
 * show/hide closed area select fields.
 */

$(document).ready(function() {
    $('#_new_user_groups_field').change(function(){
        console.log("Value Changed");
        var selectVal = $(this).val();
        $('.closed-area').hide();
        $.each(selectVal, function( index, value) {
            console.log('access-'+value);
            $('#access-' + value).show();
        });
    });
});