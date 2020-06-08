<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ManagerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '管理员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manager-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
<?php if (Yii::$app->user->can('添加管理员')): ?>
    <p>
        <?=Html::a('添加管理员', ['create'], ['class' => 'btn btn-success'])?>
    </p>
<?php endif;?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'username',
        //'auth_key',
        //'password_hash',
        //'password_reset_token',
        // 'email:email',
        'role',
        [
            'attribute' => 'status',
            'format'    => 'text',
            'value'     => function ($model) {return $model->getStatus();},
        ],
        'realname',
        [
            'attribute' => 'branch',
            'format'    => 'text',
            'value'     => function ($model) {
                if (!empty($model->branch)) {
                    return $model->getBranch();
                }
            },
        ],
        [
            'attribute' => 'userid',
            'format'    => 'text',
            'value'     => function ($model) {
                if ($model->wxMemberName) {
                    return $model->wxMemberName->name;
                }
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑管理员') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
                'delete' => function ($url, $model, $key) {
                    $options = [
                        'onclick' => 'return confirm("确定删除吗？");',
                    ];
                    return !\Yii::$app->user->can('删除管理员') || $model->username === 'admin' ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],

    ],
]);?>

</div>
