<?php

use common\models\Api;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DiscountHolicySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '优惠策略管理';
$this->params['breadcrumbs'][] = ['label' => '支付渠道管理', 'url' => ['/discount-building-assoc/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discount-holicy-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'payTypeList' => $payTypeList]); ?>


    <p>
        <?=Yii::$app->user->can('优惠策略添加') ? Html::a('添加', ['create'], ['class' => 'btn btn-success']) : '';?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '策略名称',
            'value' => function ($model) {return $model->holicy_name;},
        ],
        [
            'label' => '支付方式',
            'value' => function ($model) use ($payTypeList) {return isset($payTypeList[$model->holicy_payment]) ? $payTypeList[$model->holicy_payment] : '';},
        ],
        [
            'label' => '优惠策略类型',
            'value' => function ($model) {return isset($model->holicy_type_list[$model->holicy_type]) ? $model->holicy_type_list[$model->holicy_type] : '';},
        ],
        [
            'label' => '优惠价格',
            'value' => function ($model) {
                if ($model->holicy_type == 2) {
                    return intval($model->holicy_price) . "折";
                } else {
                    return $model->holicy_price . '元';
                }
            },
        ],
        [
            'label' => '创建时间',
            'value' => function ($model) {return date('Y年m月d日', $model->holicy_time);},
        ],
        [
            'label' => '策略说明',
            'value' => function ($model) {return $model->holicy_introduction;},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{delete} {update}',
            'buttons'  => [
                // 'release' => function ($url, $model) {
                //     return (Yii::$app->user->can('优惠策略发布')) && ($model->holicy_status == 0) ? Html::a('', '/discount-holicy/release?id=' . $model->holicy_id . '&status=' . ($model->holicy_status ? 0 : 1), ['class' => 'glyphicon glyphicon-send', 'title' => '发布']) : '';
                // },
                'delete' => function ($url, $model) {
                    $options = [
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'/discount-holicy/delete?id=' . $model->holicy_id . '\','
                        . 'function(data){'
                        . 'if(data == 1){location.reload()}'
                        . 'else{alert(\'删除失败，请检查该策略是否绑定楼宇\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return Yii::$app->user->can('优惠策略删除') && (($model->holicy_status == 0) || Api::discountHolicyBuildingIsExistence(array('holicy_id' => $model->holicy_id))) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', '/discount-holicy/delete?id=' . $model->holicy_id . '&status=2', $options) : '';
                },
                'update' => function ($url, $model) {
                    return Yii::$app->user->can('优惠策略修改') ? Html::a('', '/discount-holicy/update?id=' . $model->holicy_id, ['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']) : '';
                },
            ],
        ],
    ],
]);?>

</div>
