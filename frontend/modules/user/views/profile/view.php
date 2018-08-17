<?php
/* @var $this yii\web\View */
/* @var $user frontend\models\User */
/* @var $currentUser frontend\models\User */
/* @var $modelPicture frontend\modules\user\models\forms\PictureForm */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\HTMLPurifier;
use dosamigos\fileupload\FileUpload;
?>

<h3><?= Html::encode($user->username) ?></h3>
<p><?= HTMLPurifier::process($user->about) ?></p>
<hr>

<img src="<?= $user->getPicture() ?>" id="profile-picture">

<?php if($currentUser && $currentUser->equals($user)): ?>

<div class="alert alert-success display-none" id="profile-image-success">Profile image updated</div>
<div class="alert alert-danger display-none" id="profile-image-fail"></div>

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
<a href="<?= Url::to(['/user/profile/delete-picture']) ?>" class="btn btn-danger">Delete picture</a>
<hr>

<?php else: ?>

<?php if($currentUser && $user->isFollowing($currentUser)): ?>
<a href="<?= Url::to(['/user/profile/unsubscribe', 'id' => $user->getId()]) ?>" class="btn btn-info">Unsubscribe</a>
<?php else: ?>
<a href="<?= Url::to(['/user/profile/subscribe', 'id' => $user->getId()]) ?>" class="btn btn-info">Subscribe</a>
<?php endif; ?>
<hr>
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
<hr>
<?php endif; ?>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalSubscriptions">
    Subscriptions: <?= $user->countSubscriptions() ?>
</button>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalFollowers">
    Followers: <?= $user->countFollowers() ?>
</button>

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