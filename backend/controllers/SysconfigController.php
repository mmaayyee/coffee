<?php

namespace backend\controllers;

use common\models\Sysconfig;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * SysconfigController implements the CRUD actions for Sysconfig model.
 */
class SysconfigController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'download-file'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sysconfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('系统设置列表')) {
            return $this->redirect(['site/login']);
        }
        $query = Sysconfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->andFilterWhere([
            'config_edit' => Sysconfig::CANEDIT,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Sysconfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "系统设备", \backend\models\ManagerLog::UPDATE, $model->config_desc);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 下载远程文件
     * @author zhenggangwei
     * @date   2019-02-22
     * @return [type]     [description]
     */
    public function actionDownloadFile()
    {
        if (!Yii::$app->user->can('下载远程文件')) {
            return $this->redirect(['site/login']);
        }
        set_time_limit(0);
        // 获取远程文件路径
        $fileUrl = Yii::$app->request->post('filePth');
        if ($fileUrl) {
            $fileUrl = Yii::$app->params['downloadUrl'] . $fileUrl;
            // 获取远程文件后缀
            $filePathInfo = explode('.', $fileUrl);
            $postfix      = $filePathInfo[count($filePathInfo) - 1];
            $fileSuffix   = substr($postfix, 0, 3);
            // 将远程文件保存到本地
            $downloadFileName = '../web/uploads/download.' . $fileSuffix;
            $ch               = curl_init($fileUrl);
            $fp               = fopen($downloadFileName, "wb");
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $res = curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            // 下载保存到本地的文件
            $type = filetype($downloadFileName);
            header("Content-type: $type");
            header("Content-Disposition: attachment;filename=download_" . date("Y-m-d") . "." . $fileSuffix);
            header("Content-Transfer-Encoding: binary");
            header('Pragma: no-cache');
            header('Expires: 0');
            readfile($downloadFileName);
            // 删除保存到本地的文件
            unlink($downloadFileName);
            die;
        }
        return $this->render('download');
    }

    /**
     * Finds the Sysconfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sysconfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sysconfig::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
