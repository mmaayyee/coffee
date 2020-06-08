<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\EquipLightBoxRepair;
/**
 * Site controller
 */
class LightBoxRepairController extends BaseController
{
    /**
     * 获取任务信息
     * @return [type] [description]
     */
    public function actionIndex() {
        $type = Yii::$app->request->get('type',1);
        $where = ['and',"supplier_id = '".$this->userinfo['userid']."'"];
        if ($type < 8) {
            $where[] = ['<','process_result',8];
        }else {
            $where[] = ['>','process_result',7];
        }
        $task_count = EquipLightBoxRepair::getCount($where);
        $task_list = EquipLightBoxRepair::getList('*',$where);
        return $this->render('index', [
            'task_list' => $task_list,
            'task_count' => $task_count,
            'type' => $type,
        ]);
    }
    /**
     * 任务详情
     * @return [type] [description]
     */
    public function actionDetail() {
        $id = Yii::$app->request->get('id');
        $task_detail = EquipLightBoxRepair::getDetail('*',['id'=>$id, 'supplier_id'=>$this->userinfo['userid']]);
        if (!$task_detail) {
            return $this->render('/site/error', ['message' => '您没有此操作权限']);
        }
        return $this->render('detail', [
            'task_detail' => $task_detail
        ]);
    }

    /**
     * 维修处理结果
     * @return [type] [description]
     */
    public function actionChangeProcessResult() {
        $id = Yii::$app->request->get('id');
        $process_result = Yii::$app->request->get('process_result',2);
        $model = EquipLightBoxRepair::findOne($id);
        $model->process_time = time();
        $model->process_result = $process_result;
        if ($model->save()) {
            echo 0;
        } else {
            echo 1;
        }
    }

    /**
     * 维修记录详情
     * @return [type] [description]
     */
    public function actionRepairRecordDetail()
    {
        $id = Yii::$app->request->get('id');
        $task_detail = EquipLightBoxRepair::getDetail('*',['id'=>$id, 'supplier_id'=>$this->userinfo['userid']]);
        if (!$task_detail) {
            return $this->render('/site/error', ['message' => '您没有此操作权限']);
        }
        return $this->render('repair_record_detail', [
            'task_detail' => $task_detail,
        ]);
    }

}
