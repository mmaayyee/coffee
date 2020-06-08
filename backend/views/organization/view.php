<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Organization;

/* @var $this yii\web\View */
/* @var $model backend\models\Organization */

$this->title = $model->org_id;
$this->params['breadcrumbs'][] = ['label' => '机构列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->org_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'org_id',
            'org_name',
            'org_city',
            [
                'attribute' => 'parent_id',
                'value' => $model->parent_id ? Organization::getOrgNameByID($model->parent_id) : '',
            ],
            [
                'attribute' => 'organization_type',
                'value'     => Organization::$organizationType[$model->organization_type] ? Organization::$organizationType[$model->organization_type] : '-',
            ],
            [
                'attribute' => 'is_replace_maintain',
                'value'     => $model->instead[$model->is_replace_maintain] ? $model->instead[$model->is_replace_maintain] : '-',
            ],
        ],
    ]) ?>

</div>
