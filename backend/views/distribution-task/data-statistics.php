<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use backend\models\DistributionTask;
use common\models\WxMember;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '运维数据统计管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<h6><?= Html::encode("若无按钮，请查看权限！") ?></h6>
    <p>
    	<?php  if (Yii::$app->user->can('水单记录管理')) { ?>
        	<?= Html::a('水单记录统计', ['distribution-water-record/index'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
		<?php  if (Yii::$app->user->can('设备月用水量统计')) { ?>
        	<?= Html::a('设备月用水量统计', ['/distribution-water-statistics/index'], ['class' => 'btn btn-success']) ?>
        <?php }?>
        <?php  if (Yii::$app->user->can('物料记录统计')) { ?>
        	<?= Html::a('物料记录统计', ['distribution-material-record/index'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        
		<?php  if (Yii::$app->user->can('开箱签到记录')) { ?>	
        	<?= Html::a('开箱签到统计', ['distribution-sign-box/index'], ['class' => 'btn btn-success']) ?>
        <?php }?>
        
        <?php if(Yii::$app->user->can('物料对比统计')){ ?>
            <?= Html::a('物料对比统计', ['distribution-material-comparison/index'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        
        <?php if(Yii::$app->user->can('运维工作数据统计')){ ?>
            <?= Html::a('运维工作数据统计', ['distribution-work-data-count/index'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        
        <?php if(Yii::$app->user->can('出库明细统计')){ ?>
            <?= Html::a('出库明细统计', ['distribution-warehousing-details/out-warehouse'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        <?php if(Yii::$app->user->can('入库明细统计')){ ?>
            <?= Html::a('入库明细统计', ['distribution-warehousing-details/in-warehouse'], ['class' => 'btn btn-success']) ?>
        <?php } ?>

    </p>

</div>
