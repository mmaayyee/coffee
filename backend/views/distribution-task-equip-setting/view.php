<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Building;
use backend\models\ScmEquipType;
use backend\models\ScmSupplier;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskEquipSetting */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '日常任务设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-task-equip-setting-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id, 'flag' => $flag], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认删除该项吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'build_id',
                'value' => !empty($model->build_id) ? Building::getBuildingDetail("name", ['id' => $model->build_id])['name'] : '',
            ],
            [
                'attribute' => 'equip_type_id',
                'value' => !empty($model->equip_type_id) ? ScmEquipType::getEquipTypeDetail("*", ['id' => $model->equip_type_id])['model'] : '',
            ],
            [
                'attribute' => 'org_id',
                'value' => !empty($model->org_id) ? ScmSupplier::getOrgNameStr($model->org_id) : '',
            ],
            [
                'attribute' => 'material_id',
                'value' => !empty($model->material_id) ? \backend\models\ScmMaterial::getScmMaterialList()[$model->material_id] : '',
            ],
            'cleaning_cycle',
            'refuel_cycle',
            'day_num',
        ],
    ]) ?>

</div>
