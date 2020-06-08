<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\WxMember;
use backend\models\Manager;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionNotice */

$this->title = $model->Id;
$this->params['breadcrumbs'][] = ['label' => '配送通知', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-notice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'create_time',
                'value' => !empty($model->create_time) ? date("Y-m-d H:i:s", $model->create_time) : '暂无',
            ],
            [
                'attribute' => 'sender',
                'value' => Manager::getUserName($model->sender),
            ],
            'content',
            'send_num',
            [
                'attribute' => 'receiver',
                'value' => !empty($model->receiver) ? \backend\models\DistributionNotice::getReceiverStr($model->receiver) : '暂无',
            ],
        ],
    ]) ?>


    <h1>配送人员相关阅读情况</h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'userId',
                'value' => function($model) {
                    return !empty($model->userId) ? WxMember::getMemberDetail('name', array('userid'=>$model->userId))["name"] : '暂无';
                }
            ],
            [
                'attribute' => 'read_status',
                'value' => function($model) {
                    return !empty($model->read_status) ? "已阅读" : '未阅读';
                }
            ],
            [
                'attribute' => 'read_time',
                'value' => function($model){
                    return !empty($model->read_time) ? date('Y-m-d H:i', $model->read_time) : '';
                }
            ],
            'read_feedback',
        ],
    ]); ?> 


</div>
