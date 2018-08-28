<?php

namespace frontend\modules\post\controllers;

use frontend\models\Post;
use frontend\models\User;
use frontend\models\Comment;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\modules\post\models\forms\PostForm;

/**
 * Default controller for the `post` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the create view for the module
     * @return string
     */
    public function actionCreate()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        $model = new PostForm(Yii::$app->user->identity);

        if($model->load(Yii::$app->request->post())) {
            $model->picture = UploadedFile::getInstance($model, 'picture');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Post created!');
                return $this->goHome();
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Renders the create view for the module
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        $comments = Comment::find()->where(['post_id' => $id])->orderBy('created_at')->all();

        return $this->render('view', [
            'post' => $this->findPost($id),
            'currentUser' => $currentUser,
            'comments' => $comments,
        ]);
    }

    public function actionDelete($id)
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        // TODO Удаление поста + комментарии поста?
        $id = intval($id);
        $post = Post::findOne(['id' => $id]);
        if($currentUser->id == $post->user_id) {
            if($post->removeLikesAndComments() && $post->delete() && Comment::deleteAll(['post_id' => $id])) {
                Yii::$app->session->setFlash('success', 'Post successfully deleted!');
            } else {
                Yii::$app->session->setFlash('danger', 'Some error occured! Post is not deleted!');
            }
        } else {
            Yii::$app->session->setFlash('danger', 'You have no permissions for this operation!');
        }
        return $this->redirect(['/user/profile/view', 'nickname' => $currentUser->getNickname()]);
    }

    public function actionAddComment()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        if(Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new Comment();

            $id = intval(Yii::$app->request->post('post_id'));
            $comment = htmlspecialchars(Yii::$app->request->post('comment'));
            $parent_id = intval(Yii::$app->request->post('parent_id'));

            /* @var $currentUser User */
            $currentUser = Yii::$app->user->identity;

            $post = $this->findPost($id);

            $formData['post_id'] = $id;
            $formData['parent_id'] = $parent_id ? $parent_id : null;
            $formData['user_id'] = $currentUser->id;
            $formData['comment'] = $comment;

            $model->attributes = $formData;

            if($post && $model->validate() && $model->save()) {
                $post->addComment();
                return [
                    'success' => true,
                    'author' => $currentUser['username'],
                    'comment' => $comment,
                ];
            }
        }
        return [
            'success' => false,
            'error' => 'Server error! Your comment not be published. Please, try again later or contact us.',
        ];
    }

    public function actionRemoveComment()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $currentUser = Yii::$app->user->identity;

        $id = intval(Yii::$app->request->post('id'));
        $post_id = intval(Yii::$app->request->post('post_id'));

        $post = $this->findPost($post_id);

        // Comment to remove
        $comment = Comment::findOne(['id' => $id]);
        if($currentUser->id == $comment->user_id || $post->user_id == $currentUser->id) {
            if($comment->delete()) {
                $post->removeComment();
                return [
                    'success' => true,
                    'id' => $id,
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => 'Access denied! You don\'t have a permissions for removing this comment.'
            ];
        }
        return [
            'success' => false,
            'error' => 'Some error occured! Comment has not been deleted. Please, try again later or contact us.'
        ];
    }

    public function actionEditComment()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $currentUser = Yii::$app->user->identity;

        $id = intval(Yii::$app->request->post('id'));
        $new_comment = htmlspecialchars(Yii::$app->request->post('comment'));

        $comment = Comment::findOne(['id' => $id]);
        if($comment && $currentUser->id == $comment->user_id) {
            $comment->comment = $new_comment;
            if($comment->validate() && $comment->save()) {
                return [
                    'success' => true,
                    'comment' => $new_comment,
                ];
            }
        }
        return [
            'success' => false,
            'error' => 'Some error occured!'
        ];
    }

    /**
     * Action like handler
     * @return array|Response
     */
    public function actionLike()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $post = $this->findPost($id);

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;

        $post->like($currentUser);

        return [
            'success' => true,
            'likesCount' => $post->countLikes(),
        ];
    }

    public function actionUnlike()
    {
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $post = $this->findPost($id);

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;

        $post->unlike($currentUser);

        return [
            'success' => true,
            'likesCount' => $post->countLikes(),
        ];
    }

    /**
     * @param integer $id
     * @return Post
     * @throws NotFoundHttpException
     */
    private function findPost($id)
    {
        if($post = Post::findOne($id)) {
            return $post;
        }
        throw new NotFoundHttpException();
    }
}
