/*
    Code from http://www.runningcoder.org/jquerytypeahead/demo/
    Requirements: jquery.typeahead.min.js
 */

$(document).ready(function($) {
    // Set the Options for "Bloodhound" suggestion engine
    var engine = new Bloodhound({
        prefetch: {
            url: root + '/search',
            filter: function (data) {
                console.log(data);
                return $.map(data.data.user, function (user) {
                    return {
                        name: user.last_name + ', ' + user.first_name,
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
        display: 'name',
        name: 'usersList',

        // the key from the array we want to display (name,id,email,etc...)
        templates: {
            empty: [
                '<div class="list-group search-results-dropdown"><div class="list-group-item" style="color:black">Nothing found.</div></div>'
            ],
            suggestion: function (data) {

               return '<div><a href="' + root + '/user/' + data.id + '" class="list-group-item">' + data.name + ' <small>(' + data.employeeNumber + ')</small></a></div>';

            }
        }
    });
});