$('.getFiltersBtn').click(function () {
    $.ajax('/index/getDocFilters?docId=' + $(this).attr('datafld'), {
        method: 'get',
        headers: {
            'Content-Type': 'text/html'
        },
        success: (response) => {
            $('#filters').empty().append(response)
            $('.getFiltersBtn').show().prop('disabled', false)
            $(this).hide().prop('disabled', true)
        }
    })
})