<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Building;
use common\models\Equipments;
use backend\models\ScmEquipType;
use backend\models\EquipSymptom;
use common\models\WxMember;
use common\models\WxDepartment;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrganizationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '故障列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_trouble_search', ['model' => $searchModel]); ?>
    <br/>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '故障发现时间',
                'attribute' => 'create_time',
                'value' => function($model){
                    return date('Y-m-d H:i',$model->create_time);
                }
            ],
            [
                'label' => '异常来源',
                'attribute' => 'repair_id',
                'value' => function($model){
                    return $model->repair_id > 0 ? '客服' : '运维';
                }
            ],
            [
                'label' => '异常楼宇',
                'attribute' => 'build_id',
                'value' => function($model){
                    return Building::getBuildAddress($model->build_id);
                }
            ],
            [
                'label' => '设备类型',
                'attribute' => 'equip_id',
                'value' => function($model){
                    $equipType = Equipments::getEquipTypeId($model->equip_id);
                    return $equipType ? ScmEquipType::getModel($equipType) : '';
                }
            ],
            [
                'label' => '异常情况',
                'attribute' => 'content',
                'value' => function($model){
                    return str_replace('<br/>','、',trim(EquipSymptom::getSymptomNameStr($model->content),'<br/>'));
                }
            ],
            [
                'label' => '处理人员',
                'attribute' => 'assign_userid',
                'value' => function($model){
                    return $model->assign_userid ? WxMember::getNameOne($model->assign_userid) : '';
                }
            ],
            [
                'label' => '处理部门',
                'attribute' => 'assign_userid',
                'value' => function($model){
                    $departmentId = WxMember::getFiled('department_id',['userid' => $model->assign_userid]);
                    return $departmentId ? WxDepartment::getDepartName($departmentId) : '';
                }
            ],
            [
                'label' => '处理过程',
                'attribute' => 'process_method',
                'value' => function($model){
                    return $model->process_method ? $model->process_method : '';
                }
            ],
        ],
    ]); ?>
</div>
