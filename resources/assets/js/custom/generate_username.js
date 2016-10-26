/**
 * Created by sdibble on 11/20/2015.
 *
 * Used on the form to create a new user.
 * When a key is pressed in the email field, we copy that into the userfield.
 * View: user._new_user
 *
 */

$(function(){
    $('#email').keypress(function(){
        var email = $('#email').val();
        var username = email.split('@');
        $('#username').val(username[0]);
    });
})