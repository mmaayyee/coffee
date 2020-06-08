<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MaterielLog */

$this->title = $model->materiel_log_id;
$this->params['breadcrumbs'][] = ['label' => 'Materiel Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->materiel_log_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->materiel_log_id], [
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
            'materiel_log_id',
            'operaction_type',
            'activity_type',
            'desc:ntext',
            'create_at',
        ],
    ]) ?>

</div>
