<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLanguage */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '咖语管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-language-view">

    <h1><?=Html::encode($this->title)?></h1>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => '咖语名称',
            'value'     => $model->language_name,
        ],
        [
            'attribute' => '咖语类型',
            'value'     => $model->language_type,
        ],
        [
            'attribute' => '咖语状态',
            'value'     => $model->language_static,
        ],
        [
            'attribute' => '对应饮品',
            'value'     => $model->language_product,
        ],
        [
            'attribute' => '支持设备',
            'value'     => $model->language_equipment,
        ],
        [
            'attribute' => '咖语内容',
            'format'    => 'text',
            'value'     => $model->language_content,
        ],
        [
            'attribute' => '添加时间',
            'format'    => ['date', 'php:Y-m-d H:i:s'],
            'value'     => $model->language_time,
        ],
    ],
])?>

</div>
