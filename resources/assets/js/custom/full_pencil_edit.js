/**
 * Created by sdibble on 10/15/2015.
 *
 * When clicking on the pencil edit icon, will expand the #new-form box, populate the fields.
 * Will have the form perform an update instead of a create.
 */


$(function(){

    /**
     * TRAINING
     */
    $('.edit_training').click(function(){
        showNewForm();

        //get the training_id value
        var row = $(this).closest('tr');
        var training_href = row.find("a").attr("href");
        var training_id = training_href.split("/");

        //set user id field via training_id value
        $('#user_field select').val(training_id[training_id.length-1]).selectpicker("refresh").focus();

        //set due date
        $('input#due_date').val(row.children('.training_due_date').text());

        //set completed date
        $('input#completed_date').val(row.children('.training_completed_date').text());

        //add hidden fields for the training id & method (so we update instead of create).
        hiddenField("id", "id", $(this).data('id'), '.assign_user');
        hiddenField("method", "_method", "PUT", '.assign_user');

        //change the submit text
        $('input[type=submit]').val("Update");
    });

    /**
     * GROUP
     */
    $('.edit_group').click(function(){
        showNewForm();

        // set row we will be using.
        var row = $(this).closest('tr');

        //create an array for training & user lists
        var training_list = new Array();
        var user_list = new Array();
        row.children('.training-list').children('a').each(function(){
            training_list.push($(this).data('id'));
        });
        row.children('.users-list').children('a').each(function(){
            user_list.push($(this).data('id'));
        });

        //set the select fields for training & users.
        $("#training_field select").val(training_list).selectpicker("refresh").focus();
        $("#user_field select").val(user_list).selectpicker("refresh");

        //set the group name field
        var name = row.children('.group-name').text();
        $('#name').val(name);
        if(name.substr(0,4) == 'Lab_') $('#name').prop("readonly", "readonly");

        //add hidden field so we update instead of create.
        hiddenField("method", "_method", "PUT", '.group-form');

        //set form action to go to the correct URL
        $('.group-form').attr('action', root+'/group/'+$(this).data('id'));

        //change submit text
        $('input[type=submit]').val("Update");
    });

    /**
     * NOTE
     */
    $('.edit-note').click(function(){

        //animate the slide down effect.
        showNewForm();

        //update_self is defined in layout/master.blade.php
        if(update_self){
            $('#training_id').prop('disabled', true).selectpicker("refresh");
            $('input[type=reset]').hide();
            $('#note_textarea > label').text('Optional note:');
        }

        var row = $(this).closest('.note, .alert');
        var note_type = row.data('id');
        show_hide_note_fields(note_type); //function located in user_create_note_form.js. Determines which fields show

        //disable type but make a hidden field so data gets sent to server
        $('#type').prop("disabled", true).val(note_type).selectpicker("refresh").focus();
        hiddenField("type", "type", note_type,'.note-form');

        //set values of showing fields.
        $('#private').prop('checked',row.data('private'));
        $('#note').val(row.find('.note-content').text());
        $('#due_date').val(row.find('.due-date').text());
        $('#completed_date').val(row.find('.completed-date').text());
        $('#training_id').val(row.find('.training-link').data('id')).selectpicker("refresh");
        $('#alert').prop('checked',row.hasClass('alert'));

        //add hidden field so we update instead of create.
        hiddenField("method", "_method", "PUT", '.note-form');

        //set form action to go to the correct URL
        $('.note-form').attr('action', root+'/note/'+$(this).data('id'));

        //change submit text
        $('input[type=submit]').val("Update");
    });

});

//When a user clicks on the pencil icon, we are going to show the form. If it is already open, we do nothing.
function showNewForm()
{
    $('#new-form').collapse('show');
}

/**
 * Takes 3 inputs and use them to create a hidden field. If the field exists, it gets updated instead.
 * @id = id element of hidden field.
 * @name = name of hidden field.
 * @value = value of hidden field.
 * @form = form that the hidden fields will be added onto.
 */
function hiddenField(id, name, value, form)
{
    var $hiddenId = $(form + ' input[type=hidden]#' + id);

    if ($hiddenId.length == 0)
    {
        $('<input>').attr({
            type:'hidden',
            id: id,
            name: name,
            value: value
        }).appendTo(form);
    }
    else $hiddenId.val(value);
}