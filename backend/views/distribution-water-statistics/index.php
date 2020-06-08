<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use common\models\Building;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '设备月用水量统计';
$this->params['breadcrumbs'][] = ['label' => '配送数据统计管理', 'url' => ['/distribution-task/data-statistics']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css">
    .btn-right-param{
        margin-left:10px;
    }
    p{
        height: 40px;
    }
</style>
<div class="distribution-water-index">
    <?php echo $this->render('_search', ['model' => $model]); ?>
                
    <p>
    <?= Html::a('返回上一页',['/distribution-task/data-statistics'], ['class' => 'btn btn-success pull-left']) ?>
    <?= Html::a('Excel导出', ['/distribution-water-statistics/excel-export', 'param'=> isset($param)?$param:""], ['class' => 'btn btn-success btn-right-param']) ?>
    </p>
    
    <table class="table table-bordered">
        <tr>
            <td>
                楼宇
            </td>
            <?php foreach ($titleDate as $key => $value) { ?>
                <td>
                    <?php echo substr($value, -2,2); ?>
                </td>
            <?php } ?>
            <td>小计</td>
        </tr>
        <?php if(isset($waterStatisticsArr)){ ?>
        <?php foreach ($waterStatisticsArr as $waterStatisticsKey => $waterStatisticsVal) { $num = 0; ?>
            <tr>
                <td><?php echo Building::getBuildingDetail('name', ['id'=>$waterStatisticsKey])['name'] ?></td>
                <?php foreach ($waterStatisticsVal as $key => $value) { $num += $value ;?>
                    <td>
                        <?php echo doubleval($value) ?>
                    </td>
                <?php } ?>
                <td>
                    <?php echo $num; ?>
                </td>
            </tr>

        <?php } ?>
        <?php  }?>
    </table>
    <?=
        LinkPager::widget([
          'pagination' => $pages,
        ]);
    ?>
</div>
