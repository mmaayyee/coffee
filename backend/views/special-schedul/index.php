<?php

use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\SpecialSchedulSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '设备端活动';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="special-schedul-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=!\Yii::$app->user->can('添加设备端活动') ? '' : Html::a('添加设备端活动', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '活动名称',
            'value' => function ($model) {return $model->special_schedul_name;},
        ],
        [
            'label' => '开始时间',
            'value' => function ($model) {return date("Y-m-d H:i", $model->start_time);},
        ],
        [
            'label' => '结束时间',
            'value' => function ($model) {return date("Y-m-d H:i", $model->end_time);},
        ],
        [
            'label' => '活动状态',
            'value' => function ($model) {return $model->getState();},
        ],
        [
            'label' => '是否支持优惠券',
            'value' => function ($model) {return $model->getIsCoupon();},
        ],
        [
            'label' => '用户类型',
            'value' => function ($model) {return $model->getUserType();},
        ],
        // [
        //     'label' => '发布状态',
        //     'value' => function ($model) use ($releaseStatusArray) {
        //         if (isset($releaseStatusArray[$model->id]) && $releaseStatusArray[$model->id] == 1 && $model->state == SpecialSchedul::ACTIVE_ONLINE) {
        //             return '已发布';
        //         } elseif (isset($releaseStatusArray[$model->id]) && $releaseStatusArray[$model->id] == 0 && $model->state == SpecialSchedul::ACTIVE_ONLINE) {
        //             return '未发布';
        //         } else {
        //             return '不可发布';
        //         }
        //     },
        // ],

        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {delete} {copy} {release} {product} {building}',
            'buttons'  => [
                'view'     => function ($url, $model) {
                    return !\Yii::$app->user->can('查看设备端活动') ? '' : Html::a('查看', $url);
                },
                'update'   => function ($url, $model) {
                    return $model->end_time < time() || !\Yii::$app->user->can('编辑设备端活动') ? '' : Html::a('编辑', $url);
                },
                'delete'   => function ($url, $model) {
                    return $model->start_time < time() || !\Yii::$app->user->can('删除设备端活动') ? '' : Html::a('删除', $url, ['onclick' => 'return confirm("确定要删除吗?")']);
                },
                'copy'     => function ($url, $model) {
                    return !\Yii::$app->user->can('编辑设备端活动') ? '' : Html::a('复制', '/special-schedul/update?id=' . $model->id . '&isCopy=1');
                },
                // 'release'  => function ($url, $model) use ($releaseStatusArray) {
                //     return !\Yii::$app->user->can('发布设备端活动') || !isset($releaseStatusArray[$model->id]) || $releaseStatusArray[$model->id] == 1 || $model->state == SpecialSchedul::ACTIVE_DOWN ? '' : Html::a('发布', '/special-schedul/release?id=' . $model->id, ['onclick' => 'return confirm("确定发布?")']);
                // },
                'product'  => function ($url, $model) {
                    return Html::a('单品列表', '/special-schedul/product-details-list?id=' . $model->id);
                },
                'building' => function ($url, $model) {
                    return Html::a('点位列表', '/special-schedul/building?id=' . $model->id);
                },
            ],
        ],
    ],
]);?>

</div>
