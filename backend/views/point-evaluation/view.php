<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PointEvaluation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Point Evaluations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-evaluation-view">

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
            'point_name',
            'org_id',
            'point_applicant',
            'point_position',
            'point_level',
            'cooperate',
            'point_status',
            'build_type_id',
            'build_record_id',
            'point_basic_info',
            'point_score_info',
            'point_other_info',
            'point_licence_pic',
            'point_position_pic',
            'point_company_pic',
            'point_plan',
            'created_at',
        ],
    ]) ?>

</div>
