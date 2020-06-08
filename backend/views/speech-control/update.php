<?php

/* @var $this yii\web\View */
/* @var $model backend\models\SpeechControl */

$this->title                   = '编辑语音控制';
$this->params['breadcrumbs'][] = ['label' => '语音控制', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="speech-control-update">

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
