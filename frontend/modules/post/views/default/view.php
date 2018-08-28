<?php
/* @var $this yii\web\View */
/* @var $post frontend\models\Post */
/* @var $currentUser frontend\models\User */
/* @var $comments frontend\models\Comment */

use yii\helpers\Html;
use yii\helpers\Url;
$this->title = $post->user->nickname . ' user post ' . $post->id . ' - Images';
?>


    <div class="page-posts no-padding">
    <div class="row">
        <div class="page page-post col-sm-12 col-xs-12 post-82">


            <div class="blog-posts blog-posts-large">

                <div class="row">

                    <!-- feed item -->
                    <article class="post col-sm-12 col-xs-12">
                        <div class="post-meta">
                            <div class="post-title">
                                <img src="<?= $post->user->getPicture() ?>" class="author-image" />
                                <div class="author-name"><a href="<?= Url::to(['/user/profile/view', 'nickname' => $post->user->nickname]) ?>"><?= $post->user->username ?></a></div>
                            </div>
                            <div>
                            <?php if($post->user_id == $currentUser->id): ?>
                                <a href="<?= Url::to(['/post/delete/' . $post->id]) ?>" class="btn btn-sm btn-danger">Delete</a>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="post-type-image">
                            <a href="#">
                                <img src="<?= $post->getImage() ?>" alt="">
                            </a>
                        </div>
                        <div class="post-description">
                            <p><?= Html::encode($post->description) ?></p>
                        </div>
                        <div class="post-bottom">
                            <div class="post-likes">

                                <a href="#" class="btn btn-secondary button-like" data-id="<?= $post->id ?>" <?= ($currentUser && $post->isLikedBy($currentUser)) ? 'style="display: none;"' : '' ?>><i class="fa fa-lg fa-heart-o" style="color: #000;"></i></a>
                                <a href="#" class="btn btn-secondary button-unlike"  data-id="<?= $post->id ?>" <?= ($currentUser && $post->isLikedBy($currentUser)) ? '' : 'style="display: none;"' ?>><i class="fa fa-lg fa-heart" style="color: red;"></i></a>

                                <span><span class="likes-count" data-id="<?= $post->id ?>"><?= $post->countLikes() ?></span> Likes</span>
                            </div>
                            <div class="post-comments">
                                <a href="#"><?= $post->countComments() ?? 0 ?> comments</a>

                            </div>
                            <div class="post-date">
                                <span><?= Yii::$app->formatter->asDatetime($post->created_at) ?></span>
                            </div>
                            <div class="post-report">
                                <a href="#">Report post</a>
                            </div>
                        </div>
                    </article>
                    <!-- feed item -->

                    <div class="col-sm-12 col-xs-12">
                        <h4><?= $post->countComments() ?? 0 ?> comments</h4>
                        <div class="comments-post">

                            <div class="single-item-title"></div>
                            <div class="row">
                                <ul class="comment-list" id="comments">
                                <?php foreach ($comments as $comment): ?>
                                    <!-- comment item -->
                                    <li class="comment" data-id="<?= $comment->id ?>">
                                        <div class="comment-user-image">
                                            <img src="<?= $comment->user->getPicture() ?>" width="50" height="50" style="border-radius: 50%;">
                                        </div>
                                        <div class="comment-info">
                                            <h4 class="author"><a href="<?= Url::to(['/user/profile/view', 'nickname' => $comment->user->nickname]) ?>"><?= $comment->user->nickname ?></a> <span>(<?= date('d M Y', $comment->created_at) ?>)</span></h4>
                                            <p data-id="<?= $comment->id ?>" post-id="<?= $post->id ?>">
                                                <span><?= Html::encode($comment['comment']) ?></span>
                                                <?php if($currentUser && $comment->user_id == $currentUser->id): ?><a href="<?=Url::to(['/post/default/edit-comment'])?>" class="edit-comment"><i class="glyphicon glyphicon-pencil"></i></a><?php endif; ?>
                                                <?php if($currentUser && ($comment->user_id == $currentUser->id || $post->user_id == $currentUser->id)): ?><a href="<?=Url::to(['/post/default/remove-comment'])?>" class="remove-comment"><i class="glyphicon glyphicon-remove"></i></a><?php endif; ?>
                                            </p>
                                            <small><a href="javascript:void(0);" author="<?= $comment->user->nickname ?>" class="reply">Reply</a></small>
                                        </div>
                                    </li>
                                    <!-- comment item -->
                                <?php endforeach; ?>
                                </ul>
                            </div>

                        </div>
                        <!-- comments-post -->
                    </div>

                    <div class="col-sm-12 col-xs-12" id="leave-comment">
                        <div class="col-md-12" id="comment-error"></div>
                        <div class="comment-respond">
                            <h4>Leave a Reply</h4>
                            <form method="post">
                                <p class="comment-form-comment">
                                    <textarea name="comment" rows="6" class="form-control" placeholder="Leave your comment here..." aria-required="true" id="comment"></textarea>
                                </p>
                                <span class="reply-to"></span>
                                <p class="form-submit">
                                    <button type="submit" name="submit" id="submit" class="btn btn-default" data-id="<?= $post->id ?>">Send</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

<?php $this->registerJsFile('@web/js/likes.js', [
        'depends' => \yii\web\JqueryAsset::className(),
]);
$this->registerJsFile('@web/js/comments.js', [
    'depends' => \yii\web\JqueryAsset::className(),
]);