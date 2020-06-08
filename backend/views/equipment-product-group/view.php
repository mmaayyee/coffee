<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\EquipmentProductGroup;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipmentProductGroup */

$this->title = $model->product_group_id;
$this->params['breadcrumbs'][] = ['label' => '设备产品组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-product-group-view">
    <p>
        <?=!Yii::$app->user->can('查看产品组单品') ? '' : Html::a('查看产品', ['product', 'id'=> $model->product_group_id], ['class' => 'btn btn-primary'])?>
        
        <?=!Yii::$app->user->can('查看产品组楼宇') ? '' : Html::a('查看楼宇', ['building', 'id'=> $model->product_group_id], ['class' => 'btn btn-primary'])?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'group_name',
            'group_desc',
            [
                'label' => '发布状态',
                'value' => $model->release_status ? "已发布" : "未发布",
            ],
            'release_version',
            [
                'label' =>  '产品组料仓信息',
                'format'=>  'html',
                'value' =>  isset($model->pro_group_stock_info_id) ? Html::a(EquipmentProductGroup::getProGroupStockInfoByStockId($model->pro_group_stock_info_id)['product_group_stock_name'], ['/product-group-stock-info/view?id=' . $model->pro_group_stock_info_id]) : '',
            ],
            [
                'label' => '设备类型',
                'format'=> 'html',
                'value' => isset($model->pro_group_stock_info_id) ? Html::a(EquipmentProductGroup::getEquipTypeByStockInfoId($model->pro_group_stock_info_id) , ['/scm-equip-type/view?id=' . EquipmentProductGroup::getProGroupStockInfoByStockId($model->pro_group_stock_info_id)['equip_type_id'] ]) : '',
            ],
            [
                'label' => '是否显示领取咖啡',
                'value' => $model::getSetupGetCoffee()[$model->setup_get_coffee],
            ],
            'setup_no_coffee_msg',
            [
                'label' => '是否自动刷新产品信息',
                'value' => $model->getUpdateProduct($model->is_update_product),
            ],
            [
                'label' => '是否自动刷新配方信息',
                'value' => $model->getUpdateRecipe($model->is_update_recipe),
            ],
            [
                'label' => '是否自动刷新进度条',
                'value' => $model->getUpdateProgress($model->is_update_progress),
            ],


        ],
    ]) ?>

</div>
