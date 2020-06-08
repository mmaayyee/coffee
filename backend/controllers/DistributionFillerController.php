<?php

namespace backend\controllers;

use backend\models\DistributionFiller;
use common\models\Equipments;
use Yii;
use yii\web\Controller;

/**
 * 物料消耗预测
 */
class DistributionFillerController extends Controller
{
    /**
     * 物料消耗预测页面展示
     * @author  zgw
     * @version 2016-11-03
     * @return  [type]     [description]
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 获取物料消耗
     * @author  zgw
     * @version 2016-11-03
     * @return  [type]     [description]
     */
    public function actionMaterialConsume()
    {
        // 获取要查看的物料分类id
        $materialTypeId = Yii::$app->request->get('materialTypeId', 1);
        // 获取要评估的台次
        $preEquipSum = Yii::$app->request->get('taici');

        // 获取最近三个月的设备台次
        $equipSum = Equipments::getEquipSum();
        // 获取最近三个月的物料消耗值
        $materialConsumeSum = DistributionFiller::getMaterialConsumeSum($materialTypeId);

        $preEquipSum = $preEquipSum ? $preEquipSum : $equipSum[2];
        return json_encode($this->prediction($equipSum, $materialConsumeSum, $preEquipSum));
    }

    /**
     * 物料预测
     * 预测算法说明
     * 假设，本月为4月，前三个月分别为1、2、3月，设1、2、3、4月的物料消耗为y1、y2、y3、y4
    1、2、3、8月的台数分别为x1、x2、x3、xt
    y4 = (y3/x3+0.618*(y3/x3-y2/x2)+0.382*(y2/x2-y1/x1))*xt
     * @author  zgw
     * @version 2016-11-03
     * @param   [type]     $equipSum           [description]
     * @param   [type]     $materialConsumeSum [description]
     * @return  [type]                         [description]
     */
    private function prediction($equipSum, $materialConsumeSum, $preEquipSum = 0)
    {
        // 最近三个月的物料消耗值
        $y1 = $materialConsumeSum[0];
        $y2 = $materialConsumeSum[1];
        $y3 = $materialConsumeSum[2];
        // 最近三个月的设备台数
        $x1 = $equipSum[0];
        $x2 = $equipSum[1];
        $x3 = $equipSum[2];

        // 最近三个月平均每台物料的消耗
        $p1 = $x1 > 0 ? $y1 / $x1 : 0;
        $p2 = $x2 > 0 ? $y2 / $x2 : 0;
        $p3 = $x3 > 0 ? $y3 / $x3 : 0;

        $preMaterialConsumeSum = ($p3 + 0.618 * ($p3 - $p2) + 0.382 * ($p2 - $p1)) * $preEquipSum;

        $equipSum[]           = intval($preEquipSum);
        $materialConsumeSum[] = $preMaterialConsumeSum > 0 ? $preMaterialConsumeSum : 0;
        return ['taici' => $equipSum, 'materialConsume' => $materialConsumeSum];
    }

}
