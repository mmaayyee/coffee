<?php

use common\models\WxDepartment;
use common\models\WxMember;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WxMemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '成员管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("
    $('.del-user').click(function(){
        var url = $(this).attr('href');
        $('#confirm-content').text('确认要删除吗？');
            cart_id = $(this).next().val();
            $('#confirm').modal();
            $('#confirm-sure').click(function (){
                location.href=url;
            })
        return false;
    })
");
?>
<div class="wx-member-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (Yii::$app->user->can('添加成员')) {?>
            <?=Html::a('新建成员', ['create'], ['class' => 'btn btn-success'])?>
        <?php }if (Yii::$app->user->can('同步成员')) {?>
            <?=Html::a('从微信同步成员', ['sync-user'], ['class' => 'btn btn-success'])?>
        <?php }?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'userid',
        'name',
        [
            'attribute' => 'position',
            'value'     => function ($model) {
                return $model->position ? WxMember::$position[$model->position] : '';
            },
        ],
        'mobile',
        [
            'attribute' => 'gender',
            'value'     => function ($model) {
                return $model->gender == 2 ? '女' : '男';
            },
        ],
        [
            'attribute' => 'department_id',
            'value'     => function ($model) {
                return WxDepartment::getDepartName($model->department_id);
            },
        ],
        [
            'attribute' => 'org_id',
            'value'     => function ($model) use($searchModel){
                return isset($searchModel->orgArr[$model->org_id]) ? $searchModel->orgArr[$model->org_id] : '';
            },
        ],

        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view}{update}{delete}',
            'buttons'  => [
                'view'   => function ($url, $model, $key) {
                    return Yii::$app->user->can('查看成员') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-eye-open', 'title' => '查看']) : '';
                },
                'update' => function ($url, $model, $key) {
                    return Yii::$app->user->can('编辑成员') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                },
                'delete' => function ($url, $model, $key) {
                    return Yii::$app->user->can('删除成员') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-trash del-user', 'title' => '删除']) : '';
                },
            ],
        ],
    ],
]);?>

</div>
<?=$this->render('/layouts/confirm');?>