<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ServiceQuestion */

$this->title = $question['question'];
$this->params['breadcrumbs'][] = ['label' => '问题详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-question-view">
    <!--<h1><?= Html::encode($this->title) ?></h1>
   <p>
        <?/*= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) */?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>-->
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => '关键词',
                'value'     => $key,
            ],
            [
                'attribute' => 'question',
                'value'     => $question['question'],
            ],
            [
                'attribute' => 'answer',
                'value'     => $question['answer'],
            ],
            [
                'attribute' => 'static',
                'value'     => $model->getStatus($question['static']),
            ],
            [
                'attribute' => 'create_time',
                'value' =>      date('Y-m-d',$question['create_time']),
                 
                 
                
            ],
            [
                'attribute' => 's_c_id',
                'value'     => $model->getQuestionCategoryQuestionID($question['s_c_id']),
            ],
        ],
    ]) ?>
</div>
