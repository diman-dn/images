<?php
/* @var $this yii\web\View */
/* @var $post frontend\models\Post */
/* @var $currentUser frontend\models\User */

use yii\helpers\Html;
?>

<div class="post-default-index">
    <div class="row">

        <div class="col-md-12">
            <?php if($post->user): ?>
            <h5><?= $post->user->username ?></h5>
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
    </div>

    <hr>

    <div class="col-md-12">
        <a href="#" class="btn btn-primary button-like <?= ($currentUser && $post->isLikedBy($currentUser)) ? "display-none" : "" ?>" data-id="<?= $post->id ?>">
            Like&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span>
        </a>
        <a href="#" class="btn btn-primary button-unlike <?= ($currentUser && $post->isLikedBy($currentUser)) ? "" : "display-none" ?>" data-id="<?= $post->id ?>">
            Unlike&nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
        </a>
    </div>
</div>


<?php $this->registerJsFile('@web/js/likes.js', [
        'depends' => \yii\web\JqueryAsset::className(),
]);