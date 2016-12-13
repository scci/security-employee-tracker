/**
 * Created by sdibble on 1/22/2016.
 *
 * When a group is selected, query the server for a list of users in that group.
 * Update the user select box with checked users.
 */

$(function(){
    //when we pick a group, begin.
    $("#group_field select").on("change", function (e){

        //get our values
        var token = $("meta[name=csrf-token]").attr("content");
        var selected = $(this).val();

        //process a post request
        $.ajax({
            url: root + "/group-user-id",
            type: "post",
            data: {
                _method: "post",
                _token : token,
                groups : selected
            },
            success: function(result) {
                // if we get a string, then deselect users.
                // if we get an array, select those users.
                // otherwise do nothing.
                if (typeof result == "string") {
                    $("select.User-select").val("");
                } else if (typeof result == "object") {
                    $("select.User-select").val(result);
                }
                $("select").material_select();
            }
        });
    });
});