<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MaterielBoxSpeed */

$this->title = $model->materiel_box_speed_id;
$this->params['breadcrumbs'][] = ['label' => 'Materiel Box Speeds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-box-speed-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->materiel_box_speed_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->materiel_box_speed_id], [
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
            'materiel_box_speed_id',
            'equip_type_id',
            'material_type_id',
            'speed',
        ],
    ]) ?>

</div>
