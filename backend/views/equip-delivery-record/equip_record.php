<?php

use backend\models\Organization;
use common\models\Equipments;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipDeliveryRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '设备投放记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-delivery-record-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=Html::a('楼宇投放记录', ['index'], ['class' => 'btn btn-success'])?>
    </p>
    <?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'label'  => '设备编号',
        'format' => 'raw',
        'value'  => function ($model) {
            return $model->equip ? "<a target='_blank' href='" . Url::to(['equipments/view', 'id' => $model->equip->id]) . "'>" . $model->equip->equip_code . "</a>" : "";
        },
    ],

    [
        'label' => '设备类型',
        'value' => function ($model) {
            if ($model->equip) {return $model->equip->equipTypeModel ? $model->equip->equipTypeModel->model : '';}
        },
    ],

    [
        'label' => '出厂编号',
        'value' => function ($model) {
            return $model->equip ? $model->equip->factory_code : '';
        },
    ],

    [
        'label' => '出厂设备型号',
        'value' => function ($model) {
            return $model->equip ? $model->equip->factory_equip_model : '';
        },
    ],

    [
        'label' => '设备状态',
        'value' => function ($model, $equipModel) {
            return $model->equip ? ($model->equip->equipment_status == Equipments::NORMAL ? '正常' : "故障") : '';
        },
    ],
    [
        'label' => '供应商',
        'value' => function ($model) {
            if ($model->equip) {
                if ($model->equip->equipTypeModel) {
                    return $model->equip->equipTypeModel->supplier ? $model->equip->equipTypeModel->supplier->name : '';
                }
            }
        },
    ],

    [
        'label' => '分公司',
        'value' => function ($model) {
            if ($model->build) {return $model->build ? Organization::getOrgNameByID($model->build->org_id) : '';}
        },
    ],

    [
        'label'  => '楼宇名称',
        'format' => 'raw',
        'value'  => function ($model) {
            return $model->build ? "<a target='_blank' href='" . Url::to(['building/view', 'id' => $model->build->id]) . "'>" . $model->build->name . "</a>" : "";
        },
    ],

    [
        'label' => '数量',
        'value' => function ($model) {
            return 1;
        },
    ],

    [
        'label' => '投放时间',
        'value' => function ($model) {
            return $model->create_time ? date('Y-m-d', $model->create_time) : '';
        },
    ],

    [
        'label' => '撤回时间',
        'value' => function ($model) {
            return $model->un_bind_time ? date('Y-m-d', $model->un_bind_time) : '';
        },
    ],
];
?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => $gridColumns,
]);?>
</div>
