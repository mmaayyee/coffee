<?php
use yii\helpers\Html;
$this->title                   = '修改点位: ' . ' ' . $model->point_name;
$this->params['breadcrumbs'][] = ['label' => '点位列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->point_name, 'url' => ['view', 'id' => $model->point_id]];
$this->params['breadcrumbs'][] = '修改点位';
?>
<div class="building-update">
    <h1><?=Html::encode($this->title)?></h1>
    <?=$this->render('_form', [
    'model'         => $model,
    'pointTypeList' => $pointTypeList,
])?>
</div>
