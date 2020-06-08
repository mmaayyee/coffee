<?php
use yii\helpers\Html;
$this->title                   = '添加楼宇';
$this->params['breadcrumbs'][] = ['label' => '楼宇列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-create">
    <h1><?=Html::encode($this->title)?></h1>
    <?=$this->render('_form', ['model' => $model, 'pointTypeList' => $pointTypeList])?>
</div>
