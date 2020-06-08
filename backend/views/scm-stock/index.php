<?php

use yii\grid\GridView;
use yii\helpers\Html;
use backend\models\ScmStockNum;
use backend\models\ScmStockGram;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '入库信息管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-stock-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=!Yii::$app->user->can('添加入库信息') ? '' : Html::a('添加入库信息', ['create'], ['class' => 'btn btn-success'])?>
        <?=!Yii::$app->user->can('入库审核') ? '' : Html::a('批量审核', ['check-all'], ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'warehouse_id',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->warehouse ? $model->warehouse->name : '';
            },
        ],

        [
            'attribute' => 'reason',
            'format'    => 'text',
            'value'     => function ($model) {
                return !isset($model->companyReasonArray[$model->reason]) ? '' : $model->companyReasonArray[$model->reason];

            },
        ],

        [
            'attribute' => 'distribution_clerk_id',
            'value'     => function ($model) {
                return $model->user ? $model->user->name : '';
            },
        ],

        [
            'attribute' => 'material_id',
            'format'    => 'html',
            'value'     => function ($model) {
                return ScmStockNum::getScmStockNum($model->id);
                //return $model->getCompanymaterial($model->material_id);
            },
        ],
        [
            'attribute' => 'material_gram',
            'label'     => '散料',
            'format'    => 'html',
            'value'     => function($model){
                return ScmStockGram::getScmStockGram($model->id);
            },
        ],
        /*[
            'attribute' => 'ctime',
            'value'     => function ($model) {
                return !empty($model->ctime) ? date('Y-m-d H:i:s', $model->ctime) : '';
            },
        ],*/
        [
            'attribute' => 'sure_time',
            'value'     => function ($model) {
                return !empty($model->sure_time) ? date('Y-m-d H:i:s', $model->sure_time) : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {delete} {check}',
            'buttons'  => [
                // 详情
                'view'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看入库信息') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => '详情']);
                },
                // 更新
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑入库信息') || $model->is_sure != 1 ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑']);
                },
                // 删除入库信息
                'delete' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('删除入库信息') || $model->is_sure != 1 ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => '删除', 'onClick' => 'return confirm("确认删除该项吗？")']);
                },
                // 审核
                'check'  => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('入库审核') || $model->is_sure != 1 ? '' : Html::a('<span class="glyphicon glyphicon-check"></span>', '/scm-stock/check?id=' . $model->id, ['title' => '审核通过', 'onClick' => 'return confirm("确认通过审核吗？")']);
                },

            ],
        ],

    ],
]);?>

</div>