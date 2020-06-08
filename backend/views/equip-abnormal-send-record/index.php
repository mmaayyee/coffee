<?php

use backend\models\EquipWarn;
use common\models\WxMember;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipAbnormalSendRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '异常报警发送记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-abnormal-send-record-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'abnormal_id',
            'value'     => function ($model) {
                return EquipWarn::$warnContent[$model->abnormal_id];
            },
        ],
        [
            'label' => '楼宇名称',
            'value' => function ($model) {
                return isset($model->build->name) ? $model->build->name : '';
            },
        ],
        'equip_code',
        [
            'attribute' => 'send_users',
            'value'     => function ($model) {
                return WxMember::getWxMemberName($model->send_users);
            },
        ],
        [
            'attribute' => 'is_process_success',
            'value'     => function ($model) {
                return $model::$processResult[$model->is_process_success];
            },
        ],
        [
            'label' => '是否发送成功',
            'value' => function ($model) {
                return $model->send_time ? '是' : '否';
            },
        ],
        [
            'attribute' => 'send_time',
            'value'     => function ($model) {
                return $model->send_time ? date('Y-m-d H:i:s', $model->send_time) : '';
            },
        ],
        [
            'attribute' => 'process_time',
            'value'     => function ($model) {
                return $model->process_time ? date('Y-m-d H:i:s', $model->process_time) : '';
            },
        ],
    ],
]);?>
</div>
