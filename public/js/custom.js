/**
 * Created by sketa on 6/24/2019.
 * resources/views/user/_new_user.blade.php
 * When a user updates the cont_eval selection,
 * show/hide cont_eval_date field.
 */

$(document).ready(function() {
    $('#cont_eval_field').change(function(){
        $( "#cont_eval_field option:selected").each(function() {
            if ($(this).attr("value") == '1') {
                $("#cont_eval_date_field").show();
            }
            else {
                $("#cont_eval_date_field").hide();
            }
        });
    }).change();
    $('#sipr_issued_field').change(function(){
        $( "#sipr_issued_field option:selected").each(function() {
            if ($(this).attr("value") == '1') {
                $("#sipr_issue_date_field").show();
                $("#sipr_expiration_date_field").show();
                $("#sipr_return_date_field").show();
            }
            else {
                $("#sipr_issue_date_field").hide();
                $("#sipr_expiration_date_field").hide();
                $("#sipr_return_date_field").hide();
            }
        });
    }).change();
    $('#cac_issued_field').change(function(){
        $( "#cac_issued_field option:selected").each(function() {
            if ($(this).attr("value") == '1') {
                $("#cac_issue_date_field").show();
                $("#cac_return_date_field").show();
                $("#cac_expiration_date_field").show();
            }
            else {
                $("#cac_issue_date_field").hide();
                $("#cac_return_date_field").hide();
                $("#cac_expiration_date_field").hide();
            }
        });
    }).change();
});

/**
 * Created by sdibble on 9/16/2016.
 * resources/views/user/_new_user.blade.php
 * When a user updates the group selection,
 * show/hide closed area select fields.
 */

$(document).ready(function() {
    $("#_new_user_groups_field").change(function(){
        var selectVal = $(this).val();
        $(".closed-area").hide();
        $.each(selectVal, function( index, value) {
            $("#access-" + value).show();
        });
    });
});
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
    $(".delete-training-type").click(function(){
        deleteRecord(this, "trainingtype");
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
/**
 * Created by sdibble on 11/20/2015.
 *
 * Used on the form to create a new user.
 * When a key is pressed in the email field, we copy that into the userfield.
 * View: user._new_user
 *
 */

$(function(){
    $("#email").keypress(function(){
        var email = $("#email").val();
        var username = email.split("@");
        $("#username").val(username[0]);
    });
});
/*
    Code from http://www.runningcoder.org/jquerytypeahead/demo/
    Requirements: jquery.typeahead.min.js
 */

$(document).ready(function($) {
    // Set the Options for "Bloodhound" suggestion engine
    var engine = new Bloodhound({
        prefetch: {
            url: root + "/search",
            filter: function (data) {
                return $.map(data.data.user, function (user) {
                    return {
                        name: user.last_name + ", " + user.first_name,
                        id: user.id,
                        employeeNumber: user.emp_num
                    };
                });
            }
        },
        datumTokenizer: function (datum) {
            var nameToken = Bloodhound.tokenizers.whitespace(datum.name);
            var employeeNumToken = Bloodhound.tokenizers.whitespace(datum.employeeNumber);
            return nameToken.concat(employeeNumToken);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace
    });

    // initialize the bloodhound suggestion engine
    engine.initialize();

    $(".search-input").typeahead({
        minLength: 2,
        highlight: true
    },{
        source: engine.ttAdapter(),
        display: "name",
        name: "usersList",

        // the key from the array we want to display (name,id,email,etc...)
        templates: {
            empty: [
                "<div class='list-group search-results-dropdown'><div class='list-group-item' style='color:black'>Nothing found.</div></div>"
            ],
            suggestion: function (data) {

               return "<div><a href=" + root + "/user/" + data.id + " class='list-group-item'>" + data.name + " <small>(" + data.employeeNumber + ")</small></a></div>";

            }
        }
    });
});
/**
 * Created by sdibble on 10/15/2015.
 *
 * Initialize basic jQuery Libraries.
 */

$( document ).ready(function() {

    //Material Design Initializers
    $(".button-collapse").sideNav();
    $(".tooltipped").tooltip();
    $(".modal-trigger").leanModal();
    $(".dropdown-button").dropdown({
        hover: true,
        belowOrigin: true,
    });
    $(".datepicker").pickadate({
        onSet: function( arg ){
            if ( "select" in arg ){ //prevent closing on selecting month/year
                this.close();
            }
        },
        format: "yyyy-mm-dd",
        container: "body",
        selectYears: true,
        selectMonths: true
    });

    //data table
    $(".data-table").DataTable( {
        paging: false
    });

    //Hide the alerts after a few seconds.
    $("div.alert").not(".note").delay(8000).slideUp(500);

    //If we have an ajax call, send our CSRF token.
    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content") }
    });

    //upload training/user documents
    $("#js-upload").change(function() {
        $("#attachments-form").submit();
    });

    $("select").material_select();
});

/**
 * Created by sdibble on 2/10/2016.
 */

function trainingAssignmentFilters(name) {
    $("#assignment_buttons .btn").removeClass("active");

    var trainingDueDate = $("#new-training #training_due_date");
    var userField = $("#new-training #user_field");
    var groupField = $("#new-training #group_field");

    if (name === "due_date") {
        trainingDueDate.show();
        userField.show();
        groupField.show();
    } else {
        trainingDueDate.hide();
        userField.hide();
        groupField.hide();
    }
}

$(function(){
    trainingAssignmentFilters("none");

    $("#ab-due-date").click(function(){
        trainingAssignmentFilters("due_date");
    });
    $("#ab-none").click(function(){
        trainingAssignmentFilters("none");
    });
});
/**
 * Generates a string for today"s date in the format of yyyy-mm-dd
 *
 * @returns {string}
 */
function formatToday() {
    var d = new Date(),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) {
        month = "0" + month;
    }
    if (day.length < 2) {
        day = "0" + day;
    }

    return [year, month, day].join("-");
}

$(function(){
    $(".completed-today").click(function(){
        var today = formatToday();
        var trainingUserId = $(this).data("id");
        var userId = $(this).data("user");
        var token = $("meta[name=csrf-token]").attr("content");

        //Set value on page.
        $(this).closest("tr").children(".training_completed_date").text(today);

        //hide completed today button & reminder button.
        $(this).hide();
        $(this).closest("td").children("a").hide();

        //Make an ajax call to update the record.
        $.ajax({
            url: root + "/user/" + userId + "/training/" + trainingUserId,
            type: "post",
            data: {
                _method: "put",
                _token : token,
                completed_date : today
            },
            success: function(result) {
            }
        });

    });
});
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
//# sourceMappingURL=custom.js.map
