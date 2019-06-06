<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\UploadedFile;
use common\models\Element;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'alex'],
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
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionElement()
    {
        if (!Yii::$app->user->isGuest) {
            $model = new Element();
            $res = Element::find()->asArray()->all();
            // Получаем доступ к csv файлу (если доступен) и открываем его
            $file_csv = $_SERVER['DOCUMENT_ROOT'] . '/test/table.csv';

            if (file_exists($file_csv)) {
                if ($model->load(Yii::$app->request->post())) {

                    $model->file = UploadedFile::getInstance($model, 'file');
                    $file_name = 'table' . $model->file->extension;

                    $model->file->saveAs($_SERVER['DOCUMENT_ROOT'] . '/test/table' .'.' . $model->file->extension);
                    Yii::$app->session->setFlash('success', 'Файл успешно загружен на сервер.');
                   // return $this->refresh();
                }

                $file = fopen($file_csv, "rt");
                // В цикле for проходимся по строкам в файле таблицы
                for ($i = 0; $data = fgetcsv($file, 1000, ";"); $i++) {

                    // Созаем новый объект
                    $el = new Element();
                    if ($i > 0) {
                        // Наполняем бъект данными (поля в таблице)
                        $el->title = $data[0];
                        $el->category1 = $data[1];
                        $el->category2 = $data[2];
                        $el->category3 = $data[3];
                        $el->vendor_name = $data[4];
                        $el->description = $data[5];
                        $el->save();
                    }
                }
                fclose($file);
                $id =Yii::$app->request->get('id');

                if( $id == 53 ){
                    Element::deleteAll();
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/test/table.csv');
                }
                return $this->render('element', compact('res', 'model'));
            } else {


                if ($model->load(Yii::$app->request->post())) {

                    $model->file = UploadedFile::getInstance($model, 'file');
                    $file_name = 'table' . $model->file->extension;
                   // $model->file->saveAs($_SERVER['DOCUMENT_ROOT'] . '/test/table' . '.' . $model->file->extension);
                    $model->file->saveAs($_SERVER['DOCUMENT_ROOT'] . '/test/table' . '.' . $model->file->extension);
                    Yii::$app->session->setFlash('success', 'Файл успешно загружен на сервер.');
                    return $this->refresh();
                }
                return $this->render('element', compact('model', 'res'));
            }

        } else {
            Yii::$app->session->setFlash('error', 'Вам необходимо авторизоваться, чтобы просматривать этот раздел.');
            return $this->redirect('login');
        }
    }

    public function actionBinarySearch()
    {
        return $this->render('binary-search');
    }

    /**
     * @Binary Search
     * @return string
     */
    public function actionBinarySearch2(): string
    {
        return $this->render('binary-search2');
    }
}
