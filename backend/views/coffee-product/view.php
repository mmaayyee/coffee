<?php

use backend\models\CoffeeProduct;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CoffeeProduct */

$this->title                   = $model->cf_product_id;
$this->params['breadcrumbs'][] = ['label' => '单品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    p.panel-heading {
        margin: 0;
        font-weight: bold;
        text-align: center;
    }
</style>
<div class="coffee-product-view">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?php if (Yii::$app->user->can('编辑单品')): ?>
            <?=Html::a('编辑', ['update', 'id' => $model->cf_product_id], ['class' => 'btn btn-primary'])?><?php endif;?><?php if (Yii::$app->user->can('删除单品')): ?>
            <?=Html::a('删除', ['delete', 'id' => $model->cf_product_id], [
    'class' => 'btn btn-danger',
    'data'  => [
        'confirm' => '确定删除吗?',
        'method'  => 'post',
    ],
])?><?php endif;?>
    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'cf_product_id',
        'cf_product_name',
        'cf_product_english_name',
        'cf_texture',
        'cf_product_price',
        [
            'attribute' => 'cf_product_status',
            'value'     => $model->getStatus(),
        ],
        [
            'attribute' => 'cf_product_type',
            'value'     => $model->getProductType(),
        ],
        [
            'attribute' => 'cf_product_kind',
            'value'     => $model->getProductKind(),
        ],
        // [
        //     'attribute' => '单品成份',
        //     'format'    => 'html',
        //     'value'     => function ($model) use ($checkedIngredient) {
        //         return !empty($checkedIngredient) ? implode('、', $checkedIngredient) : '';
        //     },
        // ],
        [
            'attribute' => 'cf_product_thumbnail',
            'format'    => 'raw',
            'value'     => function ($model) {
                return $model->getCover($model->cf_product_thumbnail);
            },
        ],
        [
            'attribute' => 'cf_product_cover',
            'format'    => 'raw',
            'value'     => function ($model) {
                return $model->getCover($model->cf_product_cover);
            },
        ],
        [
            'attribute' => 'cf_product_hot',
            'value'     => function ($cofProStockRecipeList) {
                return CoffeeProduct::$coldOrHot[$cofProStockRecipeList['cf_product_hot']];
            },
        ],
        [
            'attribute' => 'cf_product_price',
            'value'     => function ($cofProStockRecipeList) {
                return $cofProStockRecipeList['cf_special_price'];
            },
        ],
        [
            'attribute' => 'price_start_time',
            'value'     => function ($cofProStockRecipeList) {
                return $cofProStockRecipeList['price_start_time'] > 0 ? date('Y-m-d H:i', $cofProStockRecipeList['price_start_time']) : '';
            },
        ],
        [
            'attribute' => 'price_end_time',
            'value'     => function ($cofProStockRecipeList) {
                return $cofProStockRecipeList['price_end_time'] > 0 ? date('Y-m-d H:i', $cofProStockRecipeList['price_end_time']) : '';
            },
        ],
    ],
])?>
    <?php foreach ($cofProStockRecipeList['proStockRecipe'] as $equipTypeId => $equipmentStock): ?>
        <div class="panel panel-default">
            <p class="panel-heading">设备类型：<?=$equipmentStock['equip_type_name']?></p>
            <table class="panel-body table table-bordered">
                <?php if (isset($equipTypeStockList[$equipTypeId]['readableAttribute'])) {?>
                <tr>
                    <?php foreach ($equipTypeStockList[$equipTypeId]['readableAttribute'] as $field => $label) {?>
                        <th><?=$label;?></th>
                    <?php }?>
                </tr>
                <?php foreach ($equipmentStock['productSetUp'] as $formula): ?>
                    <tr>
                        <?php foreach ($equipTypeStockList[$equipTypeId]['readableAttribute'] as $field => $label) {?>
                            <td>
                                <?php if ($field == 'stock_code') {?>
                                    <?php echo isset($equipTypeStockList[$formula['equip_type_id']]['stock'][$formula['stock_code']]) ? $equipTypeStockList[$formula['equip_type_id']]['stock'][$formula['stock_code']] : ''; ?>
                                <?php } else {?>
                                    <?=$formula[$field];?><?php }?>
                            </td>
                        <?php }?>
                    </tr>
                <?php endforeach;}?>

                <tr>
                    <th>是否选糖</th>
                    <?php if ($equipmentStock['proConfigList']['cf_choose_sugar'] == 1) {?>
                        <th>输入半糖出糖量(秒)</th>
                        <th>输入全糖出糖量(秒)</th>
                        <th>半糖出糖总量(克)</th>
                        <th>全糖出糖总量(克)</th>
                        <th>冲泡时间上限</th>
                        <th>冲泡时间下限</th>
                    <?php } else {?>
                        <th>冲泡时间上限</th>
                        <th>冲泡时间下限</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    <?php }?>
                    <th></th>
                </tr>
                <tr>
                    <td><?=$equipmentStock['proConfigList']['cf_choose_sugar'] == 0 ? '否' : '是';?></td>
                    <?php if ($equipmentStock['proConfigList']['cf_choose_sugar'] == 1) {?>
                        <td><?=$equipmentStock['proConfigList']['half_sugar'];?></td>
                        <td><?=$equipmentStock['proConfigList']['full_sugar'];?></td>
                        <td><?=$equipmentStock['proConfigList']['half_sugar_total'];?></td>
                        <td><?=$equipmentStock['proConfigList']['full_sugar_total'];?></td>
                        <td><?=$equipmentStock['proConfigList']['brew_up'];?></td>
                        <td><?=$equipmentStock['proConfigList']['brew_down'];?></td>
                    <?php } else {?>
                        <td><?=$equipmentStock['proConfigList']['brew_up'];?></td>
                        <td><?=$equipmentStock['proConfigList']['brew_down'];?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    <?php }?>
                    <td></td>
                </tr>
            </table>
        </div>
    <?php endforeach;?>
</div>

