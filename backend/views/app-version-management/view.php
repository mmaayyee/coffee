<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\ScmEquipType;
/* @var $this yii\web\View */
/* @var $model backend\models\AppVersionManagement */

$this->title = $model->Id;
$this->params['breadcrumbs'][] = ['label' => 'App版本号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-version-management-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->user->can('编辑App版本号') ? Html::a('修改', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) : '' ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            'big_screen_version',
            'small_screen_version',
            [
                'attribute' => 'equip_type_id',
                'value' => ScmEquipType::getEquipTypeDetail("*", ['id'=>$model->equip_type_id])['model']
            ],

        ],
    ]) ?>

</div>
