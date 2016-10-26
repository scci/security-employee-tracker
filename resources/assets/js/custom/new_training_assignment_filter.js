/**
 * Created by sdibble on 2/10/2016.
 */

$(function(){
    trainingAssignmentFilters('none');

    $('#ab-due-date').click(function(){
        trainingAssignmentFilters('due_date');
    });
    $('#ab-meeting').click(function(){
        trainingAssignmentFilters('meeting');
    });
    $('#ab-none').click(function(){
        trainingAssignmentFilters('none');
    });
    $('#training-reset').click(function() {
        trainingAssignmentFilters('none');
    })
});

function trainingAssignmentFilters(name) {
    $('#assignment_buttons .btn').removeClass('active');

    var trainingDueDate = $('#new-training #training_due_date');
    var trainingLocation = $('#new-training #training_location');
    var trainingStart = $('#new-training #training_start');
    var trainingEnd = $('#new-training #training_end');
    var userField = $('#new-training #user_field');
    var groupField = $('#new-training #group_field');
    
    switch (name) {
        case 'due_date':
            trainingDueDate.show();
            trainingLocation.hide();
            trainingStart.hide();
            trainingEnd.hide();
            userField.show();
            groupField.show();
            break;

        case 'meeting':
            trainingDueDate.hide();
            trainingLocation.show();
            trainingStart.show();
            trainingEnd.show();
            userField.show();
            groupField.show();
            break;

        default:
            trainingDueDate.hide();
            trainingLocation.hide();
            trainingStart.hide();
            trainingEnd.hide();
            userField.hide();
            groupField.hide();
    }
}