/**
 * Created by sdibble on 1/21/2016.
 *
 * Handles the processing of the groups functionality on the Users show page.
 */

$(function(){
    $('#edit_groups').click(function(){
        $('#groups').toggle();
        $('#group_select').toggleClass('hide');
    });


    $('#group_select .submit').click(function(){
        var selectValues = new Array();
        $('#group_select').find('select').each(function() {
            selectValues = $(this).val();
        });
        var url = window.location.pathname;
        url = url.split("/");
        id = url[url.length-1];

        $.ajax({
            url: root+'/user/'+ id,
            type: 'post',
            data: {
                _method: 'put',
                _token: $('meta[name="csrf-token"]').attr('content'),
                groups: selectValues
            },
            success: function(msg){
                afterSubmission(msg);
            },
            error: function(){
                afterSubmission(new Array("None"));
            }
        });
    });


    function afterSubmission(msg){
        $('#groups').empty().toggle();
        $.each(msg, function(i){
            $('<li/>').text(msg[i]).appendTo('#groups');
        });
        $('#group_select').toggleClass('hide');
    }

});