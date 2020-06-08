<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Grind */

$this->title = $model->grind_id;
$this->params['breadcrumbs'][] = ['label' => 'Grinds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grind-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->grind_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->grind_id], [
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
            'grind_id',
            'grind_type',
            'grind_time:datetime',
            'interval_time:datetime',
            'grind_where',
        ],
    ]) ?>

</div>
