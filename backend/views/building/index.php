<?php

use common\models\Building;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '点位列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'orgIdNameList' => $orgIdNameList]); ?>
    <p>
        <?php if (Yii::$app->user->can('添加点位')) {?>
            <?=Html::a('添加点位', ['/building/create'], ['class' => 'btn btn-success'])?>
        <?php }?>
    </p>


    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'name',
        [
            'attribute' => 'org_id',
            'value'     => function ($model) use ($searchModel) {
                return isset($searchModel->orgArr[$model->org_id]) ? $searchModel->orgArr[$model->org_id] : '';
            },
        ],
        [
            'attribute' => '上级机构',
            'value'     => function ($model) use ($orgList) {
                return $model->getParentOrgName($model->org_id, $orgList);
            },
        ],
        [
            'attribute' => 'bd_maintenance_user',
            'value'     => function ($model) use ($bdMaintenanceUser) {
                return $bdMaintenanceUser[$model->build_number] ?? '';
            },
        ],
        [
            'attribute' => '点位编号',
            'value'     => function ($model) {
                return $model->build_number;
            },
        ],
        [
            'attribute' => '渠道类型',
            'value'     => function ($model) {
                return \backend\models\BuildType::getBuildType($model->build_type);
            },
        ],
        [
            'attribute' => 'equip_code',
            'format'    => 'html',
            'value'     => function ($model) {
                return isset($model->equip->equip_code) ? Html::a($model->equip->equip_code, ['/equipments/view?id=' . $model->equip->id]) : '';
            },
        ],

        [
            'attribute' => 'build_status',
            'value'     => function ($model) {
                return $model::$build_status[$model->build_status];
            },

        ],
        [
            'attribute' => '点位级别',
            'value'     => function ($model) {
                return $model::getBuildLevel($model->building_level);
            },

        ],
        [
            'attribute' => 'is_share',
            'value'     => function ($model) {
                return $model::getShareArr(2, $model->is_share);
            },
        ],
        [
            'attribute' => 'is_delivery',
            'value'     => function ($model) {
                return $model::getShareArr(2, $model->is_delivery);
            },
        ],
        [
            'attribute' => '开始运营时间',
            'value'     => function ($model) use ($operationDate) {
                return $operationDate[$model->build_number] ?? '';
            },
        ],
        [
            'attribute' => 'sign_org_id',
            'value'     => function ($model) use ($searchModel) {
                return isset($searchModel->orgArr[$model->sign_org_id]) ? $searchModel->orgArr[$model->sign_org_id] : '';
            },
        ],
        [
            'attribute' => 'source_org_id',
            'value'     => function ($model) use ($searchModel) {
                return isset($searchModel->orgArr[$model->source_org_id]) ? $searchModel->orgArr[$model->source_org_id] : '';
            },
        ],
        [
            'attribute' => 'business_type',
            'value'     => function ($model) {
                return isset(Building::$businessTypeList[$model->business_type]) ? Building::$businessTypeList[$model->business_type] : '';
            },
        ],
        [
            'attribute' => 'first_free_strategy',
            'value'     => function ($model) use ($firstStagegyNameList) {
                return $firstStagegyNameList[$model->first_free_strategy] ?? '无';
            },
        ],

        [
            'attribute' => 'strategy_change_date',
            'value'     => function ($model) {
                return $model->strategy_change_date ?? '';
            },
        ],
        [
            'attribute' => 'first_backup_strategy',
            'value'     => function ($model) use ($firstStagegyNameList) {
                return $firstStagegyNameList[$model->first_backup_strategy] ?? '无';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '操作',
            'template' => '{update} {edit} {copy} {view}',
            'buttons'  => [
                'update' => function ($url, $model) {
                    return !\Yii::$app->user->can('编辑点位') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
                'edit'   => function ($url, $model) {
                    return !\Yii::$app->user->can('更新优惠策略') ? '' : Html::a('<span class="glyphicon glyphicon-circle-arrow-right"></span>', '/building/offers-edit?id=' . $model->id, ['title' => '更新优惠策略']);
                },
                'copy'   => function ($url, $model) {
                    return !\Yii::$app->user->can('编辑点位') ? '' : Html::a('<span class="glyphicon glyphicon-copyright-mark"></span>', '/building/update?id=' . $model->id . '&isCopy=1', ['title' => '复制点位']);
                },
            ],
        ],
    ],
]);?>

</div>
