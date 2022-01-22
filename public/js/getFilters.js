$('#getFiltersBtn').click(function () {
    $.ajax('/index/getDocFilters?docId=' + $(this).attr('datafld'), {
        method: 'get',
        headers: {
            'Content-Type': 'text/html'
        },
        success: function (response) {
            $('.filters').append(response)
            $('#getFiltersBtn').hide().prop('disabled', true)
        }
    })
})