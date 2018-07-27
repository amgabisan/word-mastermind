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

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $rankModel = new Ranks;

        $personalRank = $rankModel->getPersonalRanks($user->id);
        $globalRank = $rankModel->getGlobalRanks();

        return $this->render('index',[
            'personal'  => $personalRank,
            'global'    => $globalRank,
            'id'        => $user->id
        ]);
    }
}
