<?php

use backend\models\DistributionUser;
use common\models\Building;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '运维人员列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-user-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=Yii::$app->user->can('剩余物料申请记录') ? Html::a('剩余物料申请记录', ['/scm-user-surplus-material-sure-record/index'], ['class' => 'btn btn-success']) : ''?>
        <?=Yii::$app->user->can('人员管理') ? Html::a('人员管理', ['/distribution-user/management'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '运维人员',
            'value' => function ($model) {
                return $model->user->name;
            },
        ],
        [
            'label' => '工作状态',
            'value' => function ($model) {
                return $model::$user_status[$model->user_status];
            },
        ],
        [
            'label'  => '所属楼宇',
            'format' => 'html',
            'value'  => function ($model) {
                $buildName = '';
                $buildList = Building::getBuildNameArr($model->userid);
                foreach ($buildList as $name) {
                    if (!$name) {
                        continue;
                    }
                    $buildName .= $name . '<br/>';
                }
                return $buildName;
            },
        ],
        [
            'label' => '运维组长',
            'value' => function ($model) {
                if ($model->is_leader == 1) {
                    return $model->user->name;
                }
                return DistributionUser::getUserName($model->leader_id, $model);
            },
        ],
        [
            'label' => '运维主管',
            'value' => function ($model) {
                return $model->user->org_id ? \common\models\WxMember::getDisResponsibleFromOrg($model->user->org_id, 'name') : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '操作',
            'template' => '{view}',
        ],
    ],
]);?>

</div>
