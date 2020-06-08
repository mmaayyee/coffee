<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivityCombinPackageDelivery */

$this->title = $model->delivery_id;
$this->params['breadcrumbs'][] = ['label' => 'Activity Combin Package Deliveries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-combin-package-delivery-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->delivery_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->delivery_id], [
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
            'delivery_id',
            'activity_id',
            'order_id',
            'address_id',
            'distributio_type',
            'distribution_user_id',
            'distribution_user_name',
            'courier_number',
            'is_delivery',
            'create_time:datetime',
        ],
    ]) ?>

</div>
