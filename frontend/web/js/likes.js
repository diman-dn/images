$(document).ready(function () {
    $('a.button-like').on('click', function (e) {
        e.preventDefault();
        var params = {
            'id': $(this).attr('data-id')
        };
        $.post('/post/default/like', params, function (data) {
            if(data.success) {
                $('.button-like').hide();
                $('.button-unlike').show();
                $('.likes-count').text(data.likesCount);
            }
        });
    });
    $('a.button-unlike').on('click', function (e) {
        e.preventDefault();
        var params = {
            'id': $(this).attr('data-id')
        };
        $.post('/post/default/unlike', params, function (data) {
            if(data.success) {
                $('.button-unlike').hide();
                $('.button-like').show();
                $('.likes-count').text(data.likesCount);
            }
        });
    });
});
