<?php

use backend\models\DistributionUser;
use common\models\Building;
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionUser */

$this->title                   = $model->user->name;
$this->params['breadcrumbs'][] = ['label' => '运维员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-user-view">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?=!Yii::$app->user->can('个人数据统计') ? '' : Html::a('个人数据统计', ['user-data-sync', 'author' => $model->userid], ['class' => 'btn btn-primary'])?>

        <?=!Yii::$app->user->can('任务记录') ? '' : Html::a('任务记录', ['task-record', 'author' => $model->userid], ['class' => 'btn btn-primary'])?>

        <?=!Yii::$app->user->can('配送记录') ? '' : Html::a('配送记录', ['distribution-record', 'author' => $model->userid], ['class' => 'btn btn-primary'])?>

        <?=!Yii::$app->user->can('领料记录') ? '' : Html::a('领料记录', ['receive-material-record', 'author' => $model->userid], ['class' => 'btn btn-primary'])?>

        <?=!Yii::$app->user->can('还料记录') ? '' : Html::a('还料记录', ['return-material-record', 'author' => $model->userid], ['class' => 'btn btn-primary'])?>

        <?=!Yii::$app->user->can('剩余物料') ? '' : Html::a('剩余物料', ['/scm-user-surplus-material/index', 'ScmUserSurplusMaterialSearch[author]' => $model->userid, 'ScmUserSurplusMaterialGramSearch[author]' => $model->userid], ['class' => 'btn btn-primary'])?>

        <?=!Yii::$app->user->can('剩余物料修改申请') ? '' : Html::a('剩余物料修改申请', ['/scm-user-surplus-material-sure-record/index', 'ScmUserSurplusMaterialSureRecordSearch[author]' => $model->userid, 'ScmUserSurplusMaterialSureRecordGramSearch[author]' => $model->userid], ['class' => 'btn btn-primary'])?>
    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'userid',
            'value'     => $model->user->name,
        ],
        [
            'attribute' => 'user_status',
            'value'     => DistributionUser::$user_status[$model->user_status],
        ],
        [
            'attribute' => 'leader_id',
            'value'     => $model->is_leader == 1 ? $model->user->name : DistributionUser::getUserName($model->leader_id, $model),
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
            'attribute' => 'is_leader',
            'value'     => DistributionUser::$is_leader[$model->is_leader],
        ],
    ],
])?>

</div>
