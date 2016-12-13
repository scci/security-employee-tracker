/**
 * Handle the /eod/building and /eod/lab pages
 * When an admin clicks on 2 swap buttons, submit a form to the controller to update/reload the page.
 */

$(function(){
    var id = [];
    var date = [];
    var duty = "";
    var type = "";
    $(".duty-swap").click(function(){

        //highlight the button clicked so the user knows.
        $(this).removeClass("btn-default").addClass("btn-success");
        var index = id.length;
        //push the id and date for use later.
        id[index] =  $(this).data("id") ;
        date[index] = $(this).data("date") ;
        duty = $(this).data("duty");
        type = $(this).data("type");

        //If user hit the same button twice, let"s drop the second data
        if(index === 1 && id[0] === id[1]){
            id.pop();
            date.pop();
        }

        //Once we have 2 records to swap, let"s create/process the form.
        if(index === 1) {
            var newForm = $("<form>",{
                "method":"POST",
                "action": root+"/duty-swap",
                "target": "_top"
            }).append($("<input>", {
                "name": "_token",
                "type": "hidden",
                "value": $("meta[name=csrf-token]").attr("content")
            })).append($("<input>", {
                "name": "id",
                "type": "hidden",
                "value": id
            })).append($("<input>", {
                "name": "date",
                "type": "hidden",
                "value": date
            })).append($("<input>", {
                "name": "duty",
                "type": "hidden",
                "value": duty
            })).append($("<input>", {
                "name": "type",
                "type": "hidden",
                "value": type
            }));
            //append the form to the body and submit it.
            newForm.appendTo("body").submit();
        }
    });
});