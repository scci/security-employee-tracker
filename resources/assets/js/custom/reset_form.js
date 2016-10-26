/**
 * Created by sdibble on 12/22/2015.
 * When a reset_form function is called, we reset all fields.
 * Usually called when filtering tabs (user_filter_notes.js) or hitting a reset button.
 *
 */

$(function(){
    //Reset form when the reset button is clicked
    $('input[type=reset]').click(function(){
        reset_form();   //see reset_form.js
    });

    //Reset form when you click the create note/record button on top right
    $('#show-form').click(function() {
        reset_form();   //see reset_form.js
    });
});

function get_type()
{
    var type = $('.btn.filter.active').attr('id');
    if(type == 'all' || type == 'logs') type = 'general';
    return type;
}

function reset_form()
{

    show_hide_note_fields(get_type()); //new note reset
    $('#new-form #type').prop('disabled',false).val(get_type()).selectpicker("refresh"); //select for new note reset.
    $('input[type=hidden]#type').remove();
    $('input[type=submit]').val("Create"); //change update button to save button.
    $('.assign_user #id').remove(); //training hidden id
    $('.group-form').attr('action', root+'/group'); //group form action URL
    $('#name').removeProp("readonly"); //re-activate group name field
    $("#training_field select").prop('disabled', false).val('').selectpicker("refresh"); //reset select form
    $("#user_field select").val('').selectpicker("refresh"); //reset select2 form
    $('#new-form #method').remove(); //remove hidden method
    if($('#new-form form').hasClass('assign_users')) {
        $('.assign_user #method').remove(); //hidden method
        $('.assign_user #id').remove();
    }
    if( $('#new-form form').hasClass('group-form') ) $('.group-form').attr('action', root+'/group');
    if( $('#new-form form').hasClass('note-form') ) $('.note-form').attr('action', root+'/note');

}