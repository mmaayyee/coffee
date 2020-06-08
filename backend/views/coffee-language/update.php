<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLanguage */

$this->title                   = '修改当前咖语';
$this->params['breadcrumbs'][] = ['label' => '咖语管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="coffee-language-update">
    <h1><?=Html::encode($this->title)?></h1>
    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
