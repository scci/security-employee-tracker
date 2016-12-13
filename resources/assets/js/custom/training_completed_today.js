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

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
}

$(function(){
    $(".completed-today").click(function(){
        var today = formatToday();
        var training_user_id = $(this).data("id");
        var user_id = $(this).data("user");
        var token = $("meta[name=csrf-token]").attr("content");

        //Set value on page.
        $(this).closest("tr").children(".training_completed_date").text(today);

        //hide completed today button & reminder button.
        $(this).hide();
        $(this).closest("td").children("a").hide();

        //Make an ajax call to update the record.
        $.ajax({
            url: root + "/user/" + user_id + "/training/" + training_user_id,
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