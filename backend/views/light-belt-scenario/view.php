<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\LightBeltScenario;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltScenario */

$this->title = '';
$this->params['breadcrumbs'][] = ['label' => '灯带场景管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-belt-scenario-view">

    
    <table id="w0" class="table table-striped table-bordered detail-view">
        <tbody>
            <tr><th>场景名称</th><td><?php echo $data['scenario_name'] ?></td></tr>
            <tr><th>流程场景</th><td><?php echo LightBeltScenario::$equipScenarioNameArr[$data['equip_scenario_name']] ?></td></tr>
            <tr><th>饮品组</th><td><?php echo isset($data['product_group_list']) ? $data['product_group_name'] ."：". $data['product_group_list'] : "" ?></td></tr>
            <tr><th>策略</th><td><?php echo $data['strategy_name'] ?></td></tr>
            <tr><th>开始时间</th><td><?php echo $data['start_time'] ." 时"?></td></tr>
            <tr><th>结束时间</th><td><?php echo $data['end_time'] ." 时"?></td></tr>
        </tbody>
    </table>
</div>
