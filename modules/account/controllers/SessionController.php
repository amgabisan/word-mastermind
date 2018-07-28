<?php

namespace app\modules\account\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\LoginForm;
//use app\components\helpers\Mail;

class SessionController extends Controller
{
    public $layout = '/emptyLayout';
    public $viewPath = 'app/modules/account/views';
    /**;
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // Pages that are included in the rule set
                'only'  => ['index', 'register', 'dashboard', 'logout'],
                'rules' => [
                    [ // Pages that can be accessed when logged in
                        'allow'     => true,
                        'actions'   => [ 'dashboard', 'logout'],
                        'roles'     => ['@']
                    ],
                    [ // Pages that can be accessed without logging in
                        'allow'     => true,
                        'actions'   => ['index', 'register'],
                        'roles'     => ['?']
                    ]
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
     * Login action.
     *
     * @return Response|string
     */
    public function actionIndex()
    {
        $model = new LoginForm;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = Yii::$app->user->getIdentity()->attributes;
            $session = Yii::$app->session;
            $session->open();
            foreach ($user as $userKey => $userValue) {
                $session[$userKey] = $userValue;
            }
            $session->close();

            $this->redirect(['/dashboard']);
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function actionRegister()
    {
        $model = new User;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if($model->validate()) {
                 $account = Yii::$app->request->post('User');
                if ($model->saveUser($account)) {
                    Yii::$app->session->setFlash('success', 'Registration successful. Please try to login.');
                    return $this->redirect('/');
                } else {
                    Yii::$app->session->setFlash('error', 'There was an error while registering your account. Please try again later.');
                }
            } else {
                $errors = $model->getErrors();
                foreach ($errors as $key => $error) {
                    $save = false;
                    $model->addError($key, $error[0]);
                }
            }

        }

        return $this->render('register', [
            'model'     => $model,
        ]);
    }

    public function actionDashboard()
    {
        $this->layout = '/main';

        return $this->render('dashboard');
    }

    public function actionLogout()
    {
        $session = Yii::$app->session;
        $session->removeAll();          // Removes all the session variables
        $session->destroy();            // Destroy session
        Yii::$app->response->clear();   // Clears the headers, cookies, content, status code of the response.
        Yii::$app->user->logout();

       return $this->redirect('/');
    }
}
