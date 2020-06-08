<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingRecord */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Building Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-record-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'creator_id',
            'creator_name',
            'org_id',
            'building_name',
            'build_type_id',
            'building_status',
            'province',
            'city',
            'area',
            'address',
            'floor',
            'business_circle',
            'build_longitude',
            'build_latitude',
            'contact_name',
            'contact_tel',
            'build_public_info',
            'build_special_info',
            'build_appear_pic',
            'build_hall_pic',
            'created_at',
        ],
    ]) ?>

</div>
