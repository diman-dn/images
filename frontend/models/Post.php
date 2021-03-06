<?php

namespace frontend\models;

use Yii;
use yii\redis\Connection;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string $description
 * @property int $created_at
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'filename' => 'Filename',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];
    }

    public function getImage()
    {
        return Yii::$app->storage->getFile($this->filename);
    }

    /**
     * Get author of the post
     * @return User|null
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Like current post by given user
     * @param User $user
     */
    public function like(User $user)
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $redis->sadd("post:{$this->getId()}:likes", $user->getId());
        $redis->sadd("user:{$user->getId()}:likes", $this->getId());
    }

    /**
     * Unlike current post by given user
     * @param User $user
     */
    public function unlike(User $user)
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $redis->srem("post:{$this->getId()}:likes", $user->getId());
        $redis->srem("user:{$user->getId()}:likes", $this->getId());
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Count total likes of post
     * @return mixed
     */
    public function countLikes()
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        return $redis->scard("post:{$this->getId()}:likes");
    }

    /**
     * Check whether given user liked current post
     * @param User $user
     * @return mixed
     */
    public function isLikedBy(User $user)
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        return $redis->sismember("post:{$this->getId()}:likes", $user->getId());
    }

    public function addComment()
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        return $redis->incr("post:{$this->getId()}:comments");
    }

    public function removeComment()
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        return $redis->decr("post:{$this->getId()}:comments");
    }

    public function removeLikesAndComments()
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $redis->srem("post:{$this->getId()}:comments");
        return $redis->srem("post:{$this->getId()}:likes");
    }

    public function countComments()
    {
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        return $redis->get("post:{$this->getId()}:comments") ?? 0;
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }
}
