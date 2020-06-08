<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\WxMember;
use backend\models\EquipRfidCard;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipRfidCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RFID卡管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-rfid-card-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>

        <?=Yii::$app->user->can('添加RFID门禁卡') ? Html::a('添加RFID卡', ['create'], ['class' => 'btn btn-success']) : "" ?>

        <?=Yii::$app->user->can('批量添加门禁卡') ? Html::a('批量添加RFID卡', ['equip-rfid-card-upload/index'], ['class' => 'btn btn-success']) : '';?>
        
        <?=Yii::$app->user->can('检测门禁卡开门') ? Html::a('检测门禁卡开门', ['equip-rfid-card/check-open-door'], ['class' => 'btn btn-success']) : '';?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'rfid_card_code',
            [
                'attribute' => 'member_id',
                'value'     => function ($model) {
                    return isset($model->member->name) ? $model->member->name : "";
                },
            ],

            [
                'attribute' => 'org_id',
                'format'    => 'html',
                'value'     => function ($model){
                    return $model->getOrgName($model->org_id);
                },
            ],

            [
                'attribute' => 'area_type',
                'value'     => function ($model) {
                    return $model->area_type ? EquipRfidCard::$areaType[$model->area_type] : "";
                },
            ],

            [
                'attribute' => 'create_time',
                'value'     => function ($model) {
                    return date("Y-m-d H:i", $model->create_time);
                },
            ],

            [
                'attribute' => 'rfid_state',
                'value'     => function ($model) {
                    return EquipRfidCard::$rfidState[$model->rfid_state];
                },
            ],
            [
                'attribute' => 'is_bluetooth',
                'value'     => function($model) {
                      return $model->is_bluetooth == 1 ? '禁止' : '授权';
                },
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons'  => [
                    // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                    'view'   => function ($url, $model, $key) {
                        return !\Yii::$app->user->can('查看RFID门禁卡') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                    },
                    'update' => function ($url, $model, $key) {
                        return !\Yii::$app->user->can('编辑RFID门禁卡') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'onclick' => 'return confirm("确定删除吗？");',
                        ];
                        return !\Yii::$app->user->can('删除RFID门禁卡') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
