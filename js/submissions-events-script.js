jQuery(document).on('nfFormReady', function () {

    // Ensure both datepicker fields exist before proceeding
    const $startDatePicker = jQuery('.ninja-forms-field.event_date_start');
    const $endDatePicker = jQuery('.ninja-forms-field.event_date_end');

    if (!$startDatePicker.length || !$endDatePicker.length) {
        return;
    }

    const minDate = new Date();
    const maxDate = new Date().fp_incr(30);

    const startPicker = $startDatePicker[0]._flatpickr;
    const endPicker = $endDatePicker[0]._flatpickr;

    // Set initial date range for both pickers
    [startPicker, endPicker].forEach(picker => {
        picker.set('minDate', minDate);
        picker.set('maxDate', maxDate);
    });

    // Update end date picker when the start date changes
    startPicker.config.onChange.push(function (selectedDates) {
        const selectedDate = selectedDates[0];
        if (selectedDate) {
            endPicker.set('minDate', selectedDate);
        }
    });
});
