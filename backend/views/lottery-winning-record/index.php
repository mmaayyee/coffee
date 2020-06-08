<?php

use backend\models\Activity;
use backend\models\LotteryWinningRecord;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LotteryWinningRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '参与活动记录管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lottery-winning-record-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'label'  => '活动类型',
            'format' => 'text',
            'value'  => function ($model) use ($activityTypeList) {
                return !empty($activityTypeList[$model->activity_type_id]) ? $activityTypeList[$model->activity_type_id] : "";
            },
        ],

        [
            'label'  => '抽奖活动名称',
            'format' => 'text',
            'value'  => function ($model) use ($activityNameList) {
                return !empty($activityNameList[$model->activity_id]) ? $activityNameList[$model->activity_id] : "";
            },
        ],

        [
            'label'  => '奖项名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->awards_name ? $model->awards_name : "";
            },
        ],
        [
            'label'  => '奖品名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->prizes_name ? $model->prizes_name : "";
            },
        ],
        [
            'label'  => '奖品类型',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->prizes_type ? Activity::prizesTypeList()[$model->prizes_type] : "";

                // return $model->prizes_type==1 ? '优惠券套餐' : ($model->prizes_type == 2 ? "实物" : "");
            },
        ],
        [
            'label'  => '用户名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->user_id ? LotteryWinningRecord::getUserNameById($model->user_id) : "";
            },
        ],

        [
            'label'  => '收货人名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->receiver_name ? $model->receiver_name : "";
            },
        ],
        [
            'label'  => '收货人电话',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->user_phone ? $model->user_phone : "";
            },
        ],
        [
            'label'  => '用户地址信息',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->user_addr_info ? $model->user_addr_info : "";
            },
        ],
        [
            'label'  => '中奖时间',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->create_time ? date("Y-m-d H:i", $model->create_time) : "";
            },
        ],
        [
            'label'  => '是否中奖',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->is_winning == 0 ? "未中奖" : ($model->is_winning == 1 ? "已中奖" : "");
            },
        ],
        [
            'label'  => '是否发货',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->is_ship ? Activity::shipList()[$model->is_ship] : "";
            },
        ],
        // ['class' => 'yii\grid\ActionColumn'],
    ],
]);?>

</div>
