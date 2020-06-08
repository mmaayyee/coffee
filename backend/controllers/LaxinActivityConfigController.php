<?php

namespace backend\controllers;

use backend\models\LaxinActivityConfig;
use backend\models\ManagerLog;
use backend\models\QuickSendCoupon;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LaxinActivityConfigController implements the CRUD actions for LaxinActivityConfig model.
 */
class LaxinActivityConfigController extends Controller
{

    /**
     * Displays a single LaxinActivityConfig model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        if (!Yii::$app->user->can('查看拉新活动')) {
            return $this->redirect(['site/login']);
        }
        //获取拉新活动配置信息
        $model                 = LaxinActivityConfig::getLaxinActivityConfig();
        $couponGroupIdNameList = $model['couponGroupList'];
        foreach ($couponGroupIdNameList as $groupId => &$groupName) {
            $groupName = $groupId . "_" . $groupName;
        }
        unset($groupName);
        $model->start_time           = date('Y-m-d', $model->start_time); //活动开始时间
        $model->end_time             = date('Y-m-d', $model->end_time); //活动结束时间
        $model->create_time          = date('Y-m-d', $model->create_time); //活动添加时间
        $model->rebate_node          = $model->rebate_node == 1 ? '注册' : '取杯'; //返利节点
        $model->is_repeate           = $model->is_repeate == 1 ? '否' : '是'; //是否重复获取奖励
        $model->new_coupon_groupid   = $model->new_coupon_groupid <= 0 ? '无' : $couponGroupIdNameList[$model->new_coupon_groupid];
        $model->old_coupon_groupid   = $model->old_coupon_groupid <= 0 ? '无' : $couponGroupIdNameList[$model->old_coupon_groupid];
        $model->share_coupon_groupid = $model->share_coupon_groupid <= 0 ? '无' : $couponGroupIdNameList[$model->share_coupon_groupid];
        $model->backgroud_img        = empty($model->backgroud_img) ? null : Yii::$app->params['fcoffeeUrl'] . $model->backgroud_img;
        $model->cover_img            = empty($model->cover_img) ? null : Yii::$app->params['fcoffeeUrl'] . $model->cover_img;

        return $this->render('view', [

            'model' => $model,
        ]);

    }

    /**
     * Updates an existing LaxinActivityConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {

        if (!Yii::$app->user->can('编辑拉新活动')) {
            return $this->redirect(['site/login']);
        }
        $model = LaxinActivityConfig::getLaxinActivityConfig();
        //转换时间用户页面展示
        $model->start_time  = date('Y-m-d', $model->start_time); //活动开始时间
        $model->end_time    = date('Y-m-d', $model->end_time); //活动结束时间
        $model->create_time = date('Y-m-d', $model->create_time); //活动添加时间

        $model->new_coupon_groupid   = $model->new_coupon_groupid <= 0 ? '' : $model->new_coupon_groupid;
        $model->old_coupon_groupid   = $model->old_coupon_groupid <= 0 ? '' : $model->old_coupon_groupid;
        $model->share_coupon_groupid = $model->share_coupon_groupid <= 0 ? '' : $model->share_coupon_groupid;
        $model->backgroud_img        = empty($model->backgroud_img) ? null : Yii::$app->params['fcoffeeUrl'] . $model->backgroud_img;
        $model->cover_img            = empty($model->cover_img) ? null : Yii::$app->params['fcoffeeUrl'] . $model->cover_img;

        $params                = Yii::$app->request->post();
        $couponGroupIdNameList = QuickSendCoupon::getCouponPackage();
        foreach ($couponGroupIdNameList as $groupId => &$groupName) {
            $groupName = $groupId . "_" . $groupName;
        }
        unset($groupName);
        if ($params) {

            //转换时间用于数据存储
            $params['LaxinActivityConfig']['start_time']  = strtotime($params['LaxinActivityConfig']['start_time']); //活动开始时间
            $params['LaxinActivityConfig']['end_time']    = strtotime($params['LaxinActivityConfig']['end_time']); //活动结束时间
            $params['LaxinActivityConfig']['create_time'] = strtotime($params['LaxinActivityConfig']['create_time']); //活动添加时间

            $model->load(Yii::$app->request->post());

            //注册返利设置
            $params['LaxinActivityConfig']['is_repeate'] = $params['LaxinActivityConfig']['rebate_node'] == 1 ? 1 : $params['LaxinActivityConfig']['is_repeate'];

            //判断时间设置
            if ($params['LaxinActivityConfig']['start_time'] >= $params['LaxinActivityConfig']['end_time']) //开始时间大于结束时间
            {
                Yii::$app->getSession()->setFlash('error', '活动开始时间不能大于结束时间');
                return $this->render('update', [
                    'model'                 => $model,
                    'couponGroupIdNameList' => $couponGroupIdNameList,
                ]);
            } elseif ($params['LaxinActivityConfig']['create_time'] > $params['LaxinActivityConfig']['start_time']) //设置时间大于开始时间
            {
                Yii::$app->getSession()->setFlash('error', '活动开始时间不能小于活动创建时间');
                return $this->render('update', [
                    'model'                 => $model,
                    'couponGroupIdNameList' => $couponGroupIdNameList,
                ]);
            } elseif ($params['LaxinActivityConfig']['end_time'] < time()) //结束时间小于当前时间
            {
                Yii::$app->getSession()->setFlash('error', '活动结束时间不能小于当前时间');
                return $this->render('update', [
                    'model'                 => $model,
                    'couponGroupIdNameList' => $couponGroupIdNameList,
                ]);
            }

            //套餐中的请选择
            $params['LaxinActivityConfig']['new_coupon_groupid']   = $params['LaxinActivityConfig']['new_coupon_groupid'] ?? 0;
            $params['LaxinActivityConfig']['old_coupon_groupid']   = $params['LaxinActivityConfig']['old_coupon_groupid'] ?? 0;
            $params['LaxinActivityConfig']['share_coupon_groupid'] = $params['LaxinActivityConfig']['share_coupon_groupid'] ?? 0;
            $params['LaxinActivityConfig']['backgroud_img']        = $this->imageBase64('backgroud_img');
            $params['LaxinActivityConfig']['cover_img']            = $this->imageBase64('cover_img');
            $result                                                = LaxinActivityConfig::saveLaxinActivityConfig($params);

            if ($result) {
                ManagerLog::saveLog(Yii::$app->user->id, "拉新活动设置管理", ManagerLog::UPDATE, "编辑拉新活动设置");
                return $this->redirect(['view']);
            } else {
                Yii::$app->getSession()->setFlash('error', '编辑失败');
                return $this->render('update', [
                    'couponGroupIdNameList' => $couponGroupIdNameList,
                    'model'                 => $model,
                ]);
            }

            //return $this->redirect(['view', 'id' => $model->laxin_activity_id]);
        }

        return $this->render('update', [
            'model'                 => $model,
            'couponGroupIdNameList' => $couponGroupIdNameList,
        ]);
    }

    /**
     * 图片转base64
     * @author jiangfeng
     * @version 2018/10/12
     * @param $fileName 上传图片表单的name值
     * @return string
     */
    public function imageBase64($fileName)
    {
        if ($_FILES['LaxinActivityConfig']['error'][$fileName] !== 0) {
            return '';
        }
        if ($fp = fopen($_FILES['LaxinActivityConfig']['tmp_name'][$fileName], "rb", 0)) {
            $gambar = fread($fp, filesize($_FILES['LaxinActivityConfig']['tmp_name'][$fileName]));
            fclose($fp);
            $base64 = "data:{$_FILES['LaxinActivityConfig']['type'][$fileName]};base64," . chunk_split(base64_encode($gambar));
            // 输出
            return $base64;
        }
        return '';
    }
}
