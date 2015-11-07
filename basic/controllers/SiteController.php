<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\Users;

class SiteController extends Controller
{
	public function actionSignup()
	{	
		$model = new SignupForm();
		
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$new_user = new Users();//Users::findOne($model->username);
			
			if($new_user->findOne($model->username)){
				$model->password = '';
				return $this->render('signup', ['model' => $model]);	
			} else {
				if($new_user->addNewUser($model)) {
					return $this->render('say', ['model' => $model]);
				} else {
					/*
					$model->username = 'Unknown Error';
					$model->email = 'Unknown Error';
					$model->password = '********';
					$model->f_name = 'Unknown Error';
					$model->l_name = 'Unknown Error';
					*/
					return $this->render('say', ['model'=>$model.releaseErrorModel]);
				}
			}
		} else {
			return $this->render('signup', ['model' => $model]);
        }
    }
		
	public function actionSay($model)
	{
		return $this->render('say', ['model'=>$model]);
	}
/* Sample Code
	public function actionEntry()
    {
        $model = new EntryForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // valid data received in $model

            // do something meaningful here about $model ...

            return $this->render('entry-confirm', ['model' => $model]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('entry', ['model' => $model]);
        }
    }	
*/ 
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
		$addr = [];
        //return $this->render('index');
		return $this->render('index', ['addr'=> $addr,
				]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
    public function actionRecipes()
    {
        return $this->render('recipes');
    }
}
