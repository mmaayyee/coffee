<?php

use backend\models\EquipDelivery;
use backend\models\Organization;
use common\models\Building;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipDeliverySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '销售投放管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-delivery-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加销售投放') ? Html::a('添加设备投放', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'build_id',
            'format'    => 'text',
            'value'     => function ($model) {
                return Building::getBuildingDetail('name', ['id' => $model->build_id])['name'];
            },
        ],
        [
            'attribute' => 'org_id',
            'value'     => function ($model) {
                return $model->build->org_id ? Organization::getOrgNameByID($model->build->org_id) : '';
            },
        ],
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return $model->create_time ? date("Y-m-d", $model->create_time) : '暂无';
            },
        ],
        [
            'attribute' => 'update_time',
            'value'     => function ($model) {
                return $model->update_time ? date('Y-m-d', $model->update_time) : '暂无';
            },
        ],
        [
            'attribute' => 'delivery_time',
            'value'     => function ($model) {
                return !empty($model->delivery_time) ? date('Y-m-d', $model->delivery_time) : '';
            },
        ],

        [
            'attribute' => 'sales_person',
            'value'     => function ($model) {
                return $model->sales_person;
            },
        ],

        [
            'attribute' => 'delivery_status',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->getDeliveryStatus();
            },
        ],
        'grounds_refusal',
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {delivery-check}',
            'buttons'  => [
                'delete'         => function ($url, $model, $key) {
                    $options = [
                        'onclick' => 'return confirm("确定删除吗？");',
                    ];
                    return !\Yii::$app->user->can('删除销售投放') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
                'update'         => function ($url, $model, $key) {
                    $options = [

                    ];
                    return !\Yii::$app->user->can('编辑销售投放') || in_array($model->delivery_status, [EquipDelivery::TRAFFICK_SUCCESS, EquipDelivery::UN_TRAFFICK_SUCCESS, EquipDelivery::DELIVERY_FAILURE, EquipDelivery::TERMINATION]) ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                },

                'delivery-check' => function ($url, $model, $key) {
                    if ($model->delivery_status == EquipDelivery::PENDING) {
                        return !\Yii::$app->user->can('审核销售投放') ? '' : Html::a('<span class="glyphicon glyphicon-ok"><input class="equip" type="hidden" value="' . $model->Id . '"/></span>', Url::to(['equip-delivery/view', 'id' => $model->Id, 'sign' => 'check']));
                    }
                },

            ],
        ],

    ],
]);?>

</div>
