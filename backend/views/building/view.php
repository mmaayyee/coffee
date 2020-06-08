<?php

use backend\models\BuildType;
use common\models\Building;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title                   = $model->name;
$this->params['breadcrumbs'][] = ['label' => '点位列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-view">

    <h1><?=Html::encode($this->title);?></h1>
    <?php if ($model->build_status == Building::PRE_DELIVERY) {?>
    <p>
        <?=Yii::$app->user->can('编辑点位') ? Html::a('修改点位', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '';?>
    </p>
    <?php }?>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'name',
        [
            'attribute' => '点位编号',
            'value'     => $model->build_number,
        ],
        'address',
        'contact_name',
        'contact_tel',
        'people_num',
        'province',
        'city',
        'area',
        'longitude',
        'latitude',
        [
            'attribute' => 'build_type',
            'value'     => BuildType::getBuildType($model->build_type),
        ],
        [
            'attribute' => 'build_status',
            'value'     => $model::$build_status[$model->build_status],
        ],
        [
            'attribute' => 'create_time',
            'value'     => $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '',
        ],
        [
            'attribute' => 'org_id',
            'value'     => empty($orgIdNameList[$model->org_id]) ? '' : $orgIdNameList[$model->org_id],
        ],
        [
            'attribute' => '上级机构',
            'value'     => $model->getParentOrgName($model->org_id, $orgList),
        ],
        [
            'attribute' => 'BD维护人员',
            'value'     => $bdMaintenanceUser,
        ],
        [
            'attribute' => 'first_free_strategy',
            'value'     => !isset($model->getFirstStagegyNameArray()[$model->first_free_strategy]) ? '无' : $model->getFirstStagegyNameArray()[$model->first_free_strategy],
        ],

        [
            'attribute' => 'strategy_change_date',
            'value'     => $model->strategy_change_date ? $model->strategy_change_date : '',
        ],

        [
            'attribute' => 'first_backup_strategy',
            'value'     => !isset($model->getFirstStagegyNameArray()[$model->first_backup_strategy]) ? '无' : $model->getFirstStagegyNameArray()[$model->first_backup_strategy],
        ],
        [
            'attribute' => 'is_share',
            'value'     => $model::getShareArr(2, $model->is_share),
        ],
        [
            'attribute' => 'is_delivery',
            'value'     => $model::getShareArr(2, $model->is_delivery),
        ],
        [
            'attribute' => 'program_id',
            'value'     => $model->program_id,
        ],
        [
            'attribute' => '点位级别',
            'value'     => $model::getBuildLevel($model->building_level),
        ],
        [
            'attribute' => 'sign_org_id',
            'value'     => empty($orgIdNameList[$model->sign_org_id]) ? '' : $orgIdNameList[$model->sign_org_id],
        ],
        [
            'attribute' => 'source_org_id',
            'value'     => empty($orgIdNameList[$model->source_org_id]) ? '' : $orgIdNameList[$model->source_org_id],
        ],
        [
            'attribute' => 'business_type',
            'value'     => empty($model::$businessTypeList[$model->business_type]) ? '' : $model::$businessTypeList[$model->business_type],
        ],

    ],
]);?>

</div>
