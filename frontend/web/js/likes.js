$(document).ready(function () {
    $('a.button-like').on('click', function (e) {
        e.preventDefault();
        let like = $(this);
        let unlike = $("a.button-unlike[data-id$='" + $(this).attr('data-id') + "']");
        let likeCount = $("span.likes-count[data-id$='" + $(this).attr('data-id') + "']");
        let params = {
            'id': $(this).attr('data-id')
        };
        $.post('/post/default/like', params, function (data) {
            if(data.success) {
                like.hide();
                unlike.show();
                likeCount.text(data.likesCount);
            }
        });
    });
    $('a.button-unlike').on('click', function (e) {
        e.preventDefault();
        let unlike = $(this);
        let like = $("a.button-like[data-id$='" + $(this).attr('data-id') + "']");
        let likeCount = $("span.likes-count[data-id$='" + $(this).attr('data-id') + "']");
        let params = {
            'id': $(this).attr('data-id')
        };
        $.post('/post/default/unlike', params, function (data) {
            if(data.success) {
                unlike.hide();
                like.show();
                likeCount.text(data.likesCount);
            }
        });
    });
});
