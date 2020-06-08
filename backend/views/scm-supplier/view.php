<?php

use backend\models\ScmSupplier;
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmSupplier */

$this->title                   = $model->name;
$this->params['breadcrumbs'][] = ['label' => '供应商管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-supplier-view">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?php if (Yii::$app->user->can('编辑供应商')) {?>
        <?=Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
        <?php }?>
    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'realname',
        'name',
        'supplier_code',
        [
            'attribute' => 'type',
            'value'     => $model->getSupplyType(),
        ],
        'username',
        'tel',
        [
            'attribute' => 'org_id',
            'value'     => !empty($model->org_id) ? ScmSupplier::getOrgNameStr($model->org_id) : '',
        ],
        'email:email',
        [
            'attribute' => 'create_time',
            'value'     => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '',
        ],
    ],
])?>

</div>
