<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title = '生成零售活动人员二维码';
$this->params['breadcrumbs'][] = ['label' => '零售活动人员列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-build-assoc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
