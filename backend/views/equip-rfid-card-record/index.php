<?php

use backend\models\EquipRfidCardRecord;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipRfidCardRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '开门记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-rfid-card-record-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    // 'filterModel' => $searchModel,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'equip_code',
        'rfid_card_code',
        [
            'attribute' => 'build_id',
            'value'     => function ($model) {
                return isset($model->build_id) ? (isset($model->build->name) ? $model->build->name : '') : '';
            },
        ],

        [
            'attribute' => 'open_people',
            'value'     => function ($model) {
                return isset($model->open_people) ? (isset($model->member->name) ? $model->member->name : "") : '';
            },
        ],

        [
            'attribute' => 'open_type',
            'value'     => function ($model) {
                return $model->open_type ? EquipRfidCardRecord::$openType[$model->open_type] : "";
            },
        ],

        [
            'attribute' => 'is_open_success',
            'value'     => function ($model) {
                return $model->is_open_success ? EquipRfidCardRecord::$isOpenSuccess[$model->is_open_success] : "";
            },
        ],
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return $model->create_time ? date("Y-m-d H:i:s", $model->create_time) : '';
            },
        ],

        // ['class' => 'yii\grid\ActionColumn'],
    ],
]);?>
</div>
