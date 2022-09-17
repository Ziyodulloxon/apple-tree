<?php

namespace backend\controllers;

use common\components\AppleFactory;
use common\models\Apple;
use common\models\search\AppleSearch;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * AppleController implements the CRUD actions for Apple model.
 */
class AppleController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['GET'],
                        'actions' => ['index'],
                        'matchCallback' => function () {
                            return !\Yii::$app->user->isGuest;
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'delete'],
                        'verbs' => ['POST'],
                        'matchCallback' => function () {
                            return !\Yii::$app->user->isGuest;
                        }
                    ]
                ],
            ],
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if ($this->action->id === "create") {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Apple models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AppleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Apple model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Apple model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(): Response
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            (new AppleFactory())->makeSeveralApples();
            $transaction->commit();
            \Yii::$app->session->setFlash("success", "Apples successfully created");
        } catch (\Exception $exception) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash("error", $exception->getMessage());
        } finally {
            return $this->redirect(["index"]);
        }
    }

    public function actionFallForm(int $id): string
    {
        return $this->renderAjax('_fall', [
            'model' => $this->findModel($id)
        ]);
    }

    public function actionFall(int $id): Response
    {
        try {
            $apple = $this->findModel($id);
            $apple->fall();
            if (!$apple->save()) {
                $errors = $apple->getFirstErrors();
                throw new \Exception(json_encode($errors));
            }
            \Yii::$app->session->setFlash("success", "Apple has successfully fallen");
        } catch (\Exception $exception) {
            \Yii::$app->session->setFlash("error", $exception->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBiteOffForm(int $id): string
    {
        return $this->renderAjax('_bite-off', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBiteOff(int $id): Response
    {
        $model = $this->findModel($id);
        try {
            $model->load(\Yii::$app->request->post());
            $model->biteOff();
            \Yii::$app->session->setFlash("success", "An apple successfully bitten off");
        } catch (\Exception $exception) {
            \Yii::$app->session->setFlash("error", $exception->getMessage());
        }

        return $this->redirect(['index']);
    }

    public function actionEatForm(int $id): string
    {
        return $this->renderAjax('_eat', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Deletes an existing Apple model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEat(int $id): Response
    {
        $apple = $this->findModel($id);

        $apple->eat();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Apple model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Apple the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Apple::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
