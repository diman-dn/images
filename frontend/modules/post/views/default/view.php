<?php
/* @var $this yii\web\View */
/* @var $post frontend\models\Post */
/* @var $currentUser frontend\models\User */
/* @var $comments frontend\models\Comment */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="post-default-index">
    <div class="row">

        <div class="col-md-12">
            <?php if($post->user): ?>
            <h5><a href="<?= Url::to(['/user/profile/view', 'nickname' => $post->user->nickname]) ?>"><?= $post->user->username ?></a></h5>
            <?php endif; ?>
            <?php if($post->user_id == $currentUser->id): ?>
            <a href="<?= Url::to(['/post/delete/' . $post->id]) ?>" class="btn btn-sm btn-danger">Delete</a>
            <?php endif; ?>
        </div>

        <div class="col-md-12">
            <img src="<?= $post->getImage() ?>">
        </div>

        <div class="col-md-12">
            <?= Html::encode($post->description) ?>
        </div>

        <div class="col-md-12">
            Likes: <span class="likes-count"><?= $post->countLikes() ?></span>
        </div>


    <hr>

    <div class="col-md-12">
        <br>
        <a href="#" class="button-like <?= ($currentUser && $post->isLikedBy($currentUser)) ? "display-none" : "" ?>" data-id="<?= $post->id ?>" style="font-size: 3rem">
            <span class="glyphicon glyphicon-heart-empty"></span>
        </a>
        <a href="#" class="button-unlike <?= ($currentUser && $post->isLikedBy($currentUser)) ? "" : "display-none" ?>" data-id="<?= $post->id ?>" style="font-size: 3rem">
            <span class="glyphicon glyphicon-heart" style="color: red"></span>
        </a>
    </div>

    <!-- Comments -->
        <div class="col-md-12">
            <ul id="comments">
            <?php foreach ($comments as $comment): ?>
                <li data-id="<?= $comment->id ?>">
                    <h5><a href="<?= Url::to(['/user/profile/view', 'nickname' => $comment->user->nickname]) ?>"><?= $comment->user->nickname ?></a></h5>
                    <p data-id="<?= $comment->id ?>" post-id="<?= $post->id ?>">
                        <span><?= Html::encode($comment['comment']) ?></span>
                        <?php if($currentUser && $comment->user_id == $currentUser->id): ?><a href="<?=Url::to(['/post/default/edit-comment'])?>" class="edit-comment"><i class="glyphicon glyphicon-pencil"></i></a><?php endif; ?>
                        <?php if($currentUser && ($comment->user_id == $currentUser->id || $post->user_id == $currentUser->id)): ?><a href="<?=Url::to(['/post/default/remove-comment'])?>" class="remove-comment"><i class="glyphicon glyphicon-remove"></i></a><?php endif; ?>
                    </p>
                    <small><?= date('d M Y', $comment->created_at) ?> <a href="javascript:void(0);" author="<?= $comment->user->nickname ?>" class="reply">Reply</a></small>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-12" id="comment-error"></div>
        <div class="col-md-12" id="leave-comment">
            <form method="post" action="">
                <label for="comment">Leave your comment:</label>
                <textarea name="comment" placeholder="Leave your comment here..." class="form-control" id="comment"></textarea>
                <span class="reply-to"></span>
                <br>
                <input type="submit" name="submit" id="submit" value="Send" class="btn btn-primary" data-id="<?= $post->id ?>">
            </form>
        </div>
    </div>
    <!--/ Comments -->

</div>

<?php $this->registerJsFile('@web/js/likes.js', [
        'depends' => \yii\web\JqueryAsset::className(),
]);
$this->registerJsFile('@web/js/comments.js', [
    'depends' => \yii\web\JqueryAsset::className(),
]);