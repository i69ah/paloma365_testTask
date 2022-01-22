jQuery.datetimepicker.setLocale('ru');
$.datetimepicker.setDateFormatter({
    parseDate: function (date, format) {
        let d = moment(date, format);
        return d.isValid() ? d.toDate() : false;
    },
    formatDate: function (date, format) {
        return moment(date).format(format);
    },
});
$('#date_begin').datetimepicker({
    format:'DD.MM.YYYY HH:mm',
    formatTime:'HH:mm',
    formatDate:'DD.MM.YYYY'
})
$('#date_end').datetimepicker({
    format:'DD.MM.YYYY HH:mm',
    formatTime:'HH:mm',
    formatDate:'DD.MM.YYYY'
});
