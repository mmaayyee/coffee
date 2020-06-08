<?php

use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipDelivery */

$this->title                   = $model->Id;
$this->params['breadcrumbs'][] = ['label' => '投放记录管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-delivery-view">

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'build_id',
            'value'     => \common\models\Building::getBuildingDetail('name', ['id' => $model->build_id])['name'] ? \common\models\Building::getBuildingDetail('name', ['id' => $model->build_id])['name'] : '暂无',
        ],
        [
            'attribute' => 'equip_type_id',
            'value'     => \backend\models\ScmEquipType::getEquipTypeDetail('model', ['id' => $model->equip_type_id])['model'] ? \backend\models\ScmEquipType::getEquipTypeDetail('model', ['id' => $model->equip_type_id])['model'] : '暂无',
        ],
        [
            'attribute' => 'delivery_time',
            'value'     => !empty($model->delivery_time) ? date('Y-m-d', $model->delivery_time) : '暂无',
        ],
        'sales_person',
        [
            'attribute' => 'delivery_status',
            'value'     => $model->equipDeliveryStatusArray($model->delivery_status)[$model->delivery_status],
        ],
        'reason',
        'remark',
        [
            'attribute' => 'create_time',
            'value'     => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '暂无',
        ],
        [
            'attribute' => 'is_ammeter',
            'value'     => $model->getIsNeedArr(\common\models\Building::getBuildingDetail('is_ammeter', ['id' => $model->build_id])['is_ammeter'])[\common\models\Building::getBuildingDetail('is_ammeter', ['id' => $model->build_id])['is_ammeter']],
        ],
        [
            'attribute' => 'is_lightbox',
            'value'     => $model::getLightBoxArr(\common\models\Building::getBuildingDetail('is_lightbox', ['id' => $model->build_id])['is_lightbox'])[\common\models\Building::getBuildingDetail('is_lightbox', ['id' => $model->build_id])['is_lightbox']],
        ],
        'special_require',
        [
            'attribute' => 'update_time',
            'value'     => !empty($model->update_time) ? date('Y-m-d H:i:s', $model->update_time) : '暂无',
        ],
        [
            'attribute' => 'delivery_result',
            'value'     => !empty($model->delivery_result) ? $model->deliveryResultArray($model->delivery_result)[$model->delivery_result] : '暂无',
        ],

        'grounds_refusal',
    ],
])?>

</div>
