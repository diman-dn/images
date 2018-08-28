<?php
/* @var $this yii\web\View */
/* @var $user frontend\models\User */
/* @var $currentUser frontend\models\User */
/* @var $modelPicture frontend\modules\user\models\forms\PictureForm */
/* @var $posts frontend\models\Post */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\HTMLPurifier;
use dosamigos\fileupload\FileUpload;
$this->title = Html::encode($user->username) . ' profile - Images';
?>


<div class="page-posts no-padding">
    <div class="row">
        <div class="page page-post col-sm-12 col-xs-12 post-82">


            <div class="blog-posts blog-posts-large">

                <div class="row">

                    <!-- profile -->
                    <article class="profile col-sm-12 col-xs-12">
                        <div class="profile-title">
                            <img src="<?= $user->getPicture() ?>" class="author-image" />
                            <div class="author-name"><?= Html::encode($user->username) ?></div>

                            <?php if($currentUser && $currentUser->equals($user)): ?>

                                <?= FileUpload::widget([
                                    'model' => $modelPicture,
                                    'attribute' => 'picture',
                                    'url' => ['/user/profile/upload-picture'],
                                    'options' => ['accept' => 'image/*'],
                                    'clientOptions' => [
                                        'maxFileSize' => 2000000
                                    ],
                                    'clientEvents' => [
                                        'fileuploaddone' => 'function(e, data) {
            if(data.result.success) {
                $("#profile-image-success").show();
                $("#profile-image-fail").hide();
                $("#profile-picture").attr("src", data.result.pictureUri);
            } else {
                $("#profile-image-fail").html(data.result.errors.picture).show();
                $("#profile-image-success").hide();
            }
        }',
                                    ],
                                ]); ?>
<!--                                <a href="--><?//= Url::to(['/user/profile/delete-picture']) ?><!--" class="btn btn-danger">Delete picture</a>-->
                                <a href="#" class="btn btn-default">Edit profile</a>

                            <?php else: ?>

                                <?php if($currentUser && $user->isFollowing($currentUser)): ?>
                                    <a href="<?= Url::to(['/user/profile/unsubscribe', 'id' => $user->getId()]) ?>" class="btn btn-default btn-sm">Unsubscribe</a>
                                <?php else: ?>
                                    <a href="<?= Url::to(['/user/profile/subscribe', 'id' => $user->getId()]) ?>" class="btn btn-default btn-sm">Subscribe</a>
                                <?php endif; ?>

                            <?php endif; ?>

                            <br><br>
                            <div class="alert alert-success display-none" id="profile-image-success">Profile image updated</div>
                            <div class="alert alert-danger display-none" id="profile-image-fail"></div>
                        </div>

                        <?php if($user->about): ?>
                        <div class="profile-description">
                            <p><?= HTMLPurifier::process($user->about) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($currentUser && count($currentUser->getMutualSubscriptionsTo($user)) > 0 && ($currentUser['id'] != $user['id'])): ?>
                            <h5>Friends, who are also following <?= Html::encode($user->username) ?>:</h5>
                            <div class="row">
                                <?php foreach ($currentUser->getMutualSubscriptionsTo($user) as $item): ?>
                                    <div class="col-md-12">
                                        <a href="<?= Url::to(['/user/profile/view', 'nickname' => ($item['nickname']) ? $item['nickname'] : $item['id']]) ?>">
                                            <?= Html::encode($item['username']) ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="profile-bottom">
                            <div class="profile-post-count">
                                <span><?= $user->getPostCount() ?> posts</span>
                            </div>
                            <div class="profile-followers">
                                <a href="#" data-toggle="modal" data-target="#modalFollowers"><?= $user->countFollowers() ?> followers</a>
                            </div>
                            <div class="profile-following">
                                <a href="#" data-toggle="modal" data-target="#modalSubscriptions"><?= $user->countSubscriptions() ?> following</a>
                            </div>
                        </div>
                    </article>

                    <div class="col-sm-12 col-xs-12">
                        <div class="row profile-posts">
                            <?php foreach ($posts as $post): ?>
                                <div class="col-md-4 profile-post">
                                    <a href="<?= Url::to(['/post/default/view', 'id' => $post->id]) ?>">
                                        <img src="<?= Yii::$app->storage->getFile($post->filename) ?>" class="author-image">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>


                </div>

            </div>
        </div>

    </div>
</div>

<!-- Modal subscriptions -->
<div class="modal fade" id="modalSubscriptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Subscriptions</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($user->getSubscriptions() as $subscription): ?>
                    <div class="col-md-12">
                        <a href="<?= Url::to(['/user/profile/view', 'nickname' => ($subscription['nickname']) ? $subscription['nickname'] : $subscription['id']]) ?>">
                            <?= Html::encode($subscription['username']) ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End modal subscriptions -->


<!-- Modal followers -->
<div class="modal fade" id="modalFollowers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Followers</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($user->getFollowers() as $follower): ?>
                        <div class="col-md-12">
                            <a href="<?= Url::to(['/user/profile/view', 'nickname' => ($follower['nickname']) ? $follower['nickname'] : $follower['id']]) ?>">
                                <?= Html::encode($follower['username']) ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End modal followers -->