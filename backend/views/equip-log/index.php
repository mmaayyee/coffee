<?php

use backend\models\EquipLog;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
if ($searchModel['type'] != 2) {
    $this->title                   = '日志列表';
    $this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="equip-log-index">

    <h1><?=Html::encode($this->title);?></h1>
    <?php if ($searchModel['type'] != 2) {echo $this->render('_search', ['model' => $searchModel]);}?>
    <p>
        <?=Html::a('返回上一页', '/equipments/view?id=' . $_GET['equipId'], ['class' => 'btn btn-success']);?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        // ['class' => 'yii\grid\SerialColumn'],
        'equip_code',
        [
            'attribute' => 'log_type',
            'value'     => function ($model) {
                return $model->log_type ? EquipLog::$log_type[$model->log_type] : '';
            },
        ],
        [
            'attribute' => 'equip_status',
            'value'     => function ($model) {
                return $model->equip_status ? EquipLog::$equip_status[$model->equip_status] : '';
            },
        ],
        'error_code',
        'content',
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],
    ],
]);?>

</div>
