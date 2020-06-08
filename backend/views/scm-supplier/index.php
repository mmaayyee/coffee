<?php

use backend\models\ScmSupplier;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmSupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '供应商管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-supplier-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->can('创建供应商')) {?>
        <?=Html::a('添加供应商', ['create'], ['class' => 'btn btn-success'])?>
        <?php }?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        'realname',
        'name',
        'supplier_code',
        [
            'attribute' => 'type',
            'value'     => function ($model) {
                return $model->getSupplyType();
            },
        ],
        'username',
        'tel',
        [
            'attribute' => 'org_id',
            'value'     => function ($model) {
                return !empty($model->org_id) ? ScmSupplier::getOrgNameStr($model->org_id) : '';
            },
        ],
        'email',
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],

        [

            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'view'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看供应商') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                },
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑供应商') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
            ],
        ],
    ],
]);?>

</div>
