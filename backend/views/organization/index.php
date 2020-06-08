<?php

use backend\models\Organization;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrganizationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '机构列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <br/>
    <p>
        <?=!Yii::$app->user->can('添加机构') ? '' : Html::a('创建', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '机构ID',
            'value' => function ($model) {
                return $model->org_id;
            },
        ],
        [
            'label' => '机构名称',
            'value' => function ($model) {
                return $model->org_name;
            },
        ],
        [
            'label' => '所在城市',
            'value' => function ($model) {
                return $model->org_city;
            },
        ],
        [
            'label' => '上级代理商',
            'value' => function ($model) {
                if ($model->org_id == 1) {
                    return Organization::getOrgNameByID(1);
                }
                return Organization::getOrgNameByID($model->parent_id);
            },
        ],
        [
            'label' => '是否代维护',
            'value' => function ($model) {
                return $model->getInstead();
            },
        ],
        [
            'label' => '机构类型',
            'value' => function ($model) {
                return Organization::$organizationType[$model->organization_type] ? Organization::$organizationType[$model->organization_type] : '-';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {view}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'update' => function ($url, $model) {
                    return Yii::$app->user->can('编辑机构') ? Html::a('', '/organization/update?id=' . $model->org_id, ['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']) : '';
                },
                'view'   => function ($url, $model) {
                    return Html::a('', '/organization/view?id=' . $model->org_id, ['class' => 'glyphicon glyphicon-eye-open', 'title' => '详情']);
                },
            ],
        ],
    ],
]);?>
</div>
