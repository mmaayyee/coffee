<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLanguage */

$this->title                   = '添加咖语';
$this->params['breadcrumbs'][] = ['label' => '咖语管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-language-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
