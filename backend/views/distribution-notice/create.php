<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DistributionNotice */

$this->title = '添加配送通知';
$this->params['breadcrumbs'][] = ['label' => '配送通知', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-notice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'wxMemberArr'=> $wxMemberArr,
    ]) ?>

</div>
