/**
 * Created by sdibble on 10/14/2015.
 */

$(document).ready(function(){
    $('.delete-record').click(function(){
        delete_record(this, 'profile');
    });
    $('.delete-group').click(function(){
        delete_record(this, 'group');
    });
    $('.delete-user').click(function(){
        delete_record(this, 'user');
    });
    $('.delete-training').click(function(){
        delete_record(this, 'training');
    });
    $('.delete-training-user').click(function(){
        delete_record(this, 'training-user')
    });
    $('.delete-duty').click(function(){
        delete_record(this, 'duty');
    });
    $('.delete-news').click(function(){
        delete_record(this, 'news');
    });
    $('.chip .close').click(function(){
        delete_record(this, 'attachment')
    });

});

function delete_record($this, page)
{
    var token = $("meta[name=csrf-token]").attr("content");

    var url, selector;

    if (page == 'profile' || page == 'training-user')
    {
        url = $($this).data('url');
        var type = $($this).data('type');
        selector = (page == 'profile') ? $($this).closest('li') : $($this).closest('tr');
    }else {
        var record_id = $($this).data('id');
        var type = page;
        selector = $('.'+ page + '-' + record_id);
        url = "/" + page + "/" + record_id;
    }

    selector.addClass('red accent-1');

    if (confirm('Are you sure you wish to delete this '+ type +'?')){
        $.ajax({
            url: root + url,
            type: 'post',
            data: {_method: 'delete', _token : token},
            success: function(result) {
                selector.remove();
                Materialize.toast('The selected ' + type + ' was deleted successfully.', 4000);
            }
        });
    }
    else {
        selector.removeClass('red accent-1');
    }
}