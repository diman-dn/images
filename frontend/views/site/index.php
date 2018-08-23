<?php

/* @var $this yii\web\View */
/* @var $currentUser frontend\models\User */
/* @var $feedItems[] frontend\models\Feed */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;
use yii\web\JqueryAsset;

$this->title = 'Images.com';
?>

<div class="site-index">
    <div class="row">
<?php if($feedItems): ?>
    <?php foreach ($feedItems as $feedItem): ?>
    <div class="col-md-12 feed-block">

        <div class="col-md-12">
            <img src="<?= $feedItem->author_picture ?>" width="30" height="30" style="border-radius: 50%">
            <a href="<?= Url::to(['/user/profile/view', 'nickname' => ($feedItem->author_nickname) ? $feedItem->author_nickname : $feedItem->author_id]) ?>">
                <?= Html::encode($feedItem->author_name) ?>
            </a>
        </div>

        <img src="<?= Yii::$app->storage->getFile($feedItem->post_filename) ?>" style="max-width: 100%">

        <div class="col-md-12">
            <a href="#" class="button-like <?= ($currentUser && $currentUser->likesPost($feedItem->post_id)) ? "display-none" : "" ?>" data-id="<?= $feedItem->post_id ?>">
                <span class="glyphicon glyphicon-heart-empty"></span>
            </a>
            <a href="#" class="button-unlike <?= ($currentUser && $currentUser->likesPost($feedItem->post_id)) ? "" : "display-none" ?>" data-id="<?= $feedItem->post_id ?>">
                <span class="glyphicon glyphicon-heart" style="color: red"></span>
            </a>
        </div>

        <div class="col-md-12">
            Likes: <span class="likes-count" data-id="<?= $feedItem->post_id ?>"><?= $feedItem->countLikes() ?></span>
        </div>

        <div class="col-md-12">
            <?= HtmlPurifier::process($feedItem->post_description) ?>
        </div>

        <div class="col-md-12">
            <?= Yii::$app->formatter->asDatetime($feedItem->post_created_at) ?>
        </div>

    </div>
    <div class="col-md-12"><hr></div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-md-12">
        Nobody posted yet!
    </div>
<?php endif; ?>
    </div>

</div>

<?php $this->registerJsFile('@web/js/likes.js', [
        'depends' => JqueryAsset::className(),
]);