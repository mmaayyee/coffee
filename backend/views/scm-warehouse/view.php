<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Organization;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouse */

$this->title                   = $model->name;
$this->params['breadcrumbs'][] = ['label' => '库信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-warehouse-view">

    <h1><?=Html::encode($this->title)?></h1>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'organization_id',
            'value'     => $model->organization_id && Organization::getField('org_name',['org_id' => $model->org_id]) ? Organization::getField('org_name',['org_id' => $model->org_id]) : '',
        ],

        'name',
        'address',
        [
            'attribute' => 'use',
            'value'     => $model->getWarehouseUse() ? $model->getWarehouseUse() : '暂无',
        ],

    ],
])?>

</div>
