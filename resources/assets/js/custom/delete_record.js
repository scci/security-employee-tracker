/**
 * Created by sdibble on 10/14/2015.
 */

function deleteRecord($this, page)
{
    var token = $("meta[name=csrf-token]").attr("content");

    var url, selector, type;

    var recordId = $($this).data("id");
    type = page;
    selector = page === "attachment" ? $($this).parent(".chip") : $("."+ page + "-" + recordId);
    url = "/" + page + "/" + recordId;

    if (page === "profile" || page === "training-user")
    {
        url = $($this).data("url");
        type = $($this).data("type");
        selector = (page === "profile") ? $($this).closest("li") : $($this).closest("tr");
    }


    selector.addClass("red accent-1");

    if (confirm("Are you sure you wish to delete this "+ type +"?")){
        $.ajax({
            url: root + url,
            type: "post",
            data: {_method: "delete", _token : token},
            success: function() {
                selector.remove();
                Materialize.toast("The selected " + type + " was deleted successfully.", 4000);
            }
        });
    }
    selector.removeClass("red accent-1");
}

$(document).ready(function(){
    $(".delete-record").click(function(){
        deleteRecord(this, "profile");
    });
    $(".delete-group").click(function(){
        deleteRecord(this, "group");
    });
    $(".delete-user").click(function(){
        deleteRecord(this, "user");
    });
    $(".delete-training").click(function(){
        deleteRecord(this, "training");
    });
    $(".delete-training-user").click(function(){
        deleteRecord(this, "training-user");
    });
    $(".delete-duty").click(function(){
        deleteRecord(this, "duty");
    });
    $(".delete-news").click(function(){
        deleteRecord(this, "news");
    });
    $(".delete-attachment").click(function(){
        deleteRecord(this, "attachment");
    });

});