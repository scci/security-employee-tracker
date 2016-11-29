/**
 * Created by sdibble on 2/10/2016.
 */

$(function(){
    trainingAssignmentFilters('none');

    $('#ab-due-date').click(function(){
        trainingAssignmentFilters('due_date');
    });
    $('#ab-none').click(function(){
        trainingAssignmentFilters('none');
    });
});

function trainingAssignmentFilters(name) {
    $('#assignment_buttons .btn').removeClass('active');

    var trainingDueDate = $('#new-training #training_due_date');
    var userField = $('#new-training #user_field');
    var groupField = $('#new-training #group_field');
    
    if (name == 'due_date') {
        trainingDueDate.show();
        userField.show();
        groupField.show();
    } else {
        trainingDueDate.hide();
        userField.hide();
        groupField.hide();
    }
}