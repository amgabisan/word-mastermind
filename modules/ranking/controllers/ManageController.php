<?php

namespace app\modules\ranking\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Ranks;

class ManageController extends Controller
{
    public $layout = '/main';
    public $viewPath = 'app/modules/ranking/views';
    /**;
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // Pages that are included in the rule set
                'only'  => ['index'],
                'rules' => [
                    [ // Pages that can be accessed when logged in
                        'allow'     => true,
                        'actions'   => [ 'index'],
                        'roles'     => ['@']
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // Action when user is denied from accessing a page
                    if (Yii::$app->user->isGuest) {
                        $this->goHome();
                    } else {
                        $this->redirect(['/dashboard']);
                    }
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ]
        ];
    }

    /**
    * This function renders the rank page.
    *
    * @param $type = "personal" | "world" type;
    *
    */
    public function actionIndex($type)
    {
        $user = Yii::$app->user->identity;
        $rankModel = new Ranks;
        $rankList = $rankModel->getAllList($type, $user);

        return $this->render('index',[
            'rankLists' => $rankList,
            'type'      => $type
        ]);
    }
}
