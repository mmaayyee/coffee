<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipProcess */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备工序', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-process-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'process_name',
            'process_english_name',
            'process_color',
        ],
    ]) ?>

</div>
