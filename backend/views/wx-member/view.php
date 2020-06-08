<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\WxDepartment;
use common\models\WxMember;
use backend\models\Organization;
/* @var $this yii\web\View */
/* @var $model backend\models\WxMember */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '成员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-member-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('编辑成员')) {?>
            <?= Html::a('编辑成员', ['update', 'id' => $model->userid], ['class' => 'btn btn-primary']) ?>
        <?php } if (Yii::$app->user->can('删除成员')) { ?>
            <?= Html::a('删除成员', ['delete', 'id' => $model->userid], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '确定要删除吗?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php } ?>
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'userid',
            'name',
            [
                'attribute' => 'org_id',
                'value' => Organization::getField('org_name',['org_id' => $model->org_id]) ? Organization::getField('org_name',['org_id' => $model->org_id]) : ''
            ],
            [
                'attribute' => 'department_id',
                'value' => WxDepartment::getDepartName($model->department_id)
            ],
            [
                'attribute' => 'position',
                'value' => $model->position ? WxMember::$position[$model->position] : '',
            ],
            [
                'attribute' => 'gender',
                'value' => $model->gender == 2 ? '女' : '男'
            ],
            'mobile',
            'email:email',
            'weixinid',
            [
                'attribute' => 'parent_id',
                'value' => $model->parent_id ? $model::getNameOne($model->parent_id) : '',
            ],
            [
                'attribute' => 'supplier_id',
                'value' => $model->supplier ? $model->supplier->name : '',
            ],
            [
                'attribute' => 'avatar_mediaid',
                'value'=>$model->avatar_mediaid,
                'format' => ['image',['width'=>'100','height'=>'100']],
            ],
            [
                'attribute' => 'create_time',
                'value' => $model->create_time ? date('Y-m-d H:i:s',$model->create_time) : ''
            ]
        ],
    ]) ?>

</div>
