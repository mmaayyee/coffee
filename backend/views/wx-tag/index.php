<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WxTagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '标签管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-tag-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->can('添加标签')) {?>
            <?= Html::a('新建标签', ['create'], ['class' => 'btn btn-success']) ?>
        <?php } if (Yii::$app->user->can('同步标签')) { ?>
            <?= Html::a('从微信同步标签', ['sync-tag'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'tagid',
            'tagname',
            [
                'label' => '标签下的用户',
                'format' => 'raw',
                'value' => function($model){
                    $res = \common\models\WxMemberTagAssoc::getMemberVal($model->tagid);
                    $res = \common\models\WxMember::getMemberName($res);
                    if ($res)
                        return implode('<br/>',$res);
                    else
                        return '';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{update}{delete}',
                'buttons' => [
                    'update' => function($url, $model, $key) {
                        return  Yii::$app->user->can('编辑标签') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                    },
                    'delete' => function($url, $model, $key) {
                        return  Yii::$app->user->can('删除标签') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-trash', 'title' => '删除']) : '';
                    }
                ]

            ],
            [
                'label'=>'标签用户操作',
                'format'=>'raw',
                'value' => function($model){
                    if (Yii::$app->user->can('编辑标签成员') && Yii::$app->user->can('删除标签成员')) {
                        return Html::a('', '/wx-tag/tag-user-add?id='.$model->tagid, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑标签成员']) . Html::a('', '/wx-tag/tag-user-del?id='.$model->tagid, ['class' => 'glyphicon glyphicon-trash', 'title' => '删除标签成员']);
                    }
                    if (Yii::$app->user->can('编辑标签成员')) {
                        return Html::a('', '/wx-tag/tag-user-add?id='.$model->tagid, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑标签成员']);
                    }
                    if (Yii::$app->user->can('删除标签成员')) {
                        return Html::a('', '/wx-tag/tag-user-del?id='.$model->tagid, ['class' => 'glyphicon glyphicon-trash', 'title' => '删除标签成员']);
                    }
                }
            ]
        ],
    ]); ?>

</div>
