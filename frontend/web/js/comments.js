$(document).ready(function () {
    let parent_id = null;
    // Add comment
    $('#submit').on('click', function (e) {
        e.preventDefault();
        let params = {
            'post_id': $(this).attr('data-id'),
            'comment': $('#comment').val(),
            'parent_id': parent_id
        };
        $.post('/post/default/add-comment', params, function (data) {
            if(data.success) {
                // TODO Добавление комментария без затирания остальных?
                $('#comments').append('<li>' + '<h5>' + data.author + '</h5><p>' + data.comment + '</p></li>').fadeIn(500);
                $('#comment').val('');
            } else {
                $('#comment-error').text(data.error).slideDown(500);
                setTimeout(function () {
                    $('#comment-error').fadeOut(1000);
                }, 5000);
            }
        });
    });

    // Reply to comment
    $('.reply').on('click', function (e) {
        e.preventDefault();
        let author = $(this).attr('author');
        parent_id = $(this).parent().parent().attr('data-id');
        $('#comment').empty().val(author + ', ');
        $('.reply-to').html('You reply to: ' + author + '. <a href="javascript:void(0);"><i class="glyphicon glyphicon-remove"></i></a>').show(300);
    });
    $('.reply-to').on('click', function (e) {
        e.preventDefault();
        parent_id = null;
        $('#comment').val('');
        $('.reply-to').hide();
    });

    // Remove comment
    $('.remove-comment').on('click', function (e) {
        e.preventDefault();
        let accept = confirm('Are you really want to delete this comment?');
        if(!accept) return 0;
        let params = {
            'id': $(this).parent().attr('data-id'), // comment id
            'post_id': $(this).parent().attr('post-id'), // post id
        };
        $.post('/post/default/remove-comment', params, function (data) {
            if(data.success) {
                let comment = $("li[data-id$='" + data.id + "']");
                comment.fadeOut(500);
                setTimeout(function () {
                    comment.remove();
                }, 1000);
            } else {
                $('#comment-error').text(data.error).slideDown(500);
                setTimeout(function () {
                    $('#comment-error').fadeOut(1000);
                }, 5000);
            }
        });
    });

    // Edit comment
    $('.edit-comment').on('click', function (e) {
        e.preventDefault();
        let commentId = $(this).parent().attr('data-id');
        let span = $(this).siblings('span');
        let comment = span.text();
        let p = $(this).parent();
        span.hide(500);
        p.append('<form id="edit"><input type="text" id="edit-comment" value="' + comment + '" class="form-control">&nbsp;<input type="submit" value="Save" id="save-edit" class="btn-primary">&nbsp;<button id="exit-edit" class="btn-warning">Exit</button></form>');
        $('#save-edit').on('click', function (e) {
            e.preventDefault();
            let params = {
                'id': commentId,
                'comment': $('#edit-comment').val()
            };
            $.post('/post/default/edit-comment', params, function (data) {
                if(data.success) {
                    span.text(data.comment);
                    $('#edit').remove();
                    span.show(500);
                }
            });
        });
        $('#exit-edit').on('click', function (e) {
            e.preventDefault();
            $('#edit').remove();
            span.show(500);
        });
    });
});
