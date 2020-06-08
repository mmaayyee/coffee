<?php

use common\models\EquipLightBoxRepair;
use yii\grid\GridView;
use yii\helpers\Html;
?>
<div class="equip-light-box-repair-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('返回上一页', '/equipments/view?id=' . $_GET['EquipLightBoxRepairSearch']['equip_id'], ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'build_id',
            'value'     => function ($model) {
                return $model->build ? $model->build->name : '';
            },
        ],
        [
            'attribute' => 'supplier_id',
            'value'     => function ($model) {
                return $model->wxMember ? $model->wxMember->name : '';
            },
        ],
        'remark',
        [
            'attribute' => 'process_result',
            'value'     => function ($model) {
                return EquipLightBoxRepair::$process_result[$model->process_result];
            },
        ],
        [
            'attribute' => 'process_time',
            'value'     => function ($model) {
                return $model->process_time ? date('Y-m-d H:i:s', $model->process_time) : '';
            },
        ],

        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'update' => function ($url, $model, $key) {
                    return $model->process_result != 8 ? '' : Html::a('发起灯箱验收任务', '/equip-task/add-light-box-acceptance?build_id=' . $model->build_id . '&equip_id=' . $model->build->equip->id . '&light_box_repair_id=' . $model->id);
                },
            ],
        ],
    ],
]);?>

</div>
