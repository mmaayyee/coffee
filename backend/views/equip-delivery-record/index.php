<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipDeliveryRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '楼宇投放记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-delivery-record-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="form-group">
        <?=Html::a('设备投放记录', ['index?type=2'], ['class' => 'btn btn-success'])?>
    <?php $gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'label'  => '楼宇名称',
        'format' => 'raw',
        'value'  => function ($model) {
            return $model->build ? "<a target='_blank' href='" . Url::to(['building/view', 'id' => $model->build->id]) . "'>" . $model->build->name . "</a>" : "";
        },
    ],
    [
        'label' => '楼宇地址',
        'value' => function ($model) {
            return $model->build ? $model->build->province . $model->build->city . $model->build->area . $model->build->address : '';
        },
    ],
    [
        'label' => '分公司',
        'value' => function ($model) use ($searchModel) {
            if ($model->build) {return isset($searchModel->orgArr[$model->build->org_id]) ? $searchModel->orgArr[$model->build->org_id] : '';}
        },
    ],
    [
        'label' => '设备类型',
        'value' => function ($model) {
            if ($model->equip) {return $model->equip->equipTypeModel ? $model->equip->equipTypeModel->model : '';}
        },
    ],
    [
        'label' => '4G流量卡号',
        'value' => function ($model) {
            return $model->acceptance ? $model->acceptance->sim_number : '';
        },
    ],
    [
        'label' => '数量',
        'value' => function ($model) {
            return 1;
        },
    ],
    [
        'label' => '电表',
        'value' => function ($model) {
            if ($model->delivery) {return $model->delivery->is_ammeter == 1 ? '是' : '否';}
        },
    ],
    [
        'label' => '外包灯箱',
        'value' => function ($model) {
            return isset($model->delivery->lightBox) ? $model->delivery->lightBox->light_box_name : '';
        },
    ],
    [
        'label' => '定时器型号',
        'value' => function ($model) {
            return $model->acceptance ? $model->acceptance->timer_model : '';
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
    </div>

    <?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => $gridColumns,
]);
?>
</div>
