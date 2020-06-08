<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Json;
/* @var $this yii\web\View */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备类型管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-equip-type-view">

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'model',
        [
            'attribute' => 'supplier_id',
            'value'     => $model->getSupplierName($model->supplier_id) ? $model->getSupplierName($model->supplier_id) : '暂无',
        ],
        [
            'attribute' => 'miscellaneousMaterial',
            'format' => 'html',
            'value'     => $model::getMiscellaneousMaterial($model->id) ? $model::getMiscellaneousMaterial($model->id) : '暂无',
        ],

        [
            'attribute' => 'readable_attribute',
            'format' => 'html',
            'value'     => $model::getReadableAttributeValue($model->readable_attribute),
        ],

        [
            'attribute' => 'matstock',
            'value'     => $model->getmaterialStocks($model->id) ? $model->getmaterialStocks($model->id) : '暂无',
        ],
        [
            'attribute' => 'empty_box_weight',
            'value'     => function ($model) {
                $text = '';
                if(!empty($model->empty_box_weight)){
                    $emptyBoxWeight = Json::decode($model->empty_box_weight);
                    //空料盒重量显示
                    foreach($emptyBoxWeight as $stockId => $weight){
                        if($stockId == 9){
                            $text.= 'G号料仓-'.$weight.'g,';
                        }else{
                            $text.= $stockId.'号料仓-'.$weight.'g,';
                        }
                    }
                    $text = substr($text,0,-1);
                }
                return $text;
            },
        ],
    ],
])?>

</div>
