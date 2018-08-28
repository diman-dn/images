<?php

/* @var $this yii\web\View */
/* @var $currentUser frontend\models\User */
/* @var $feedItems [] frontend\models\Feed */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;
use yii\web\JqueryAsset;

$this->title = 'NewsFeed - Images';
?>
    <div class="page-posts no-padding">
        <div class="row">
            <div class="page page-post col-sm-12 col-xs-12">
                <div class="blog-posts blog-posts-large">

                    <div class="row">
                        <?php if ($feedItems): ?>
                        <?php foreach ($feedItems as $feedItem): ?>

                        <!-- feed item -->
                        <article class="post col-sm-12 col-xs-12">
                            <div class="post-meta">
                                <div class="post-title">
                                    <img src="<?= $feedItem->author_picture ?>" class="author-image"/>
                                    <div class="author-name">
                                        <a href="<?= Url::to(['/user/profile/view', 'nickname' => ($feedItem->author_nickname) ? $feedItem->author_nickname : $feedItem->author_id]) ?>">
                                            <?= Html::encode($feedItem->author_name) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="post-type-image">
                                <a href="<?= Url::to(['/post/default/view', 'id' => $feedItem->post_id]) ?>">
                                    <img src="<?= Yii::$app->storage->getFile($feedItem->post_filename) ?>" alt="">
                                </a>
                            </div>
                            <div class="post-description">
                                <p><?= HtmlPurifier::process($feedItem->post_description) ?></p>
                            </div>
                            <div class="post-bottom">
                                <div class="post-likes">
                                    <a href="#"
                                       class="btn btn-secondary button-like" <?= ($currentUser && $currentUser->likesPost($feedItem->post_id)) ? 'style="display: none;"' : '' ?>
                                       data-id="<?= $feedItem->post_id ?>">
                                        <i class="fa fa-lg fa-heart-o"></i>
                                    </a>
                                    <a href="#"
                                       class="btn btn-secondary button-unlike <?= ($currentUser && $currentUser->likesPost($feedItem->post_id)) ? '' : 'style="display: none;"' ?>"
                                       data-id="<?= $feedItem->post_id ?>">
                                        <i class="fa fa-lg fa-heart" style="color: red;"></i>
                                    </a>
                                    <span><span class="likes-count" data-id="<?= $feedItem->post_id ?>"><?= $feedItem->countLikes() ?></span> Likes</span>
                                </div>
                                <div class="post-comments">
                                    <a href="<?= Url::to(['/post/default/view', 'id' => $feedItem->post_id]) ?>"><?= $feedItem->countComments() ?? 0 ?> Comments</a>

                                </div>
                                <div class="post-date">
                                    <span><?= Yii::$app->formatter->asDatetime($feedItem->post_created_at) ?></span>
                                </div>
                                <div class="post-report">
                                    <a href="#">Report post</a>
                                </div>
                            </div>
                        </article>
                        <!-- feed item -->

                    <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-md-12">
                            Nobody posted yet!
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->registerJsFile('@web/js/likes.js', [
    'depends' => JqueryAsset::className(),
]);