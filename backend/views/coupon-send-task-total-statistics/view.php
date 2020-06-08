<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\UserSelectionTask;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use backend\models\CouponSendTask;
/* @var $this yii\web\View */
/* @var $model backend\models\ActiveBuy */

$this->title = '发券任务详情';
$this->params['breadcrumbs'][] = ['label' => '发券任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
  <style>
  table.coupon-table {
    font-family: verdana, arial, sans-serif;
    font-size: 14px;
    color: #333;
    border-width: 1px;
    border-color: #666;
    border-collapse: collapse;
    width: 90%;
  }

  table.coupon-table th {
    border-width: 1px;
    padding: 10px;
    border-style: solid;
    border-color: #666;
    background-color: #eee;
  }

  table.coupon-table td {
    border-width: 1px;
    padding: 10px;
    border-style: solid;
    border-color: #666;
    background-color: #fff;
  }
  </style>


<div class="">
    <table class="table table-responsive">
        <tr>
            <td><label for="">任务名称</label></td>
            <td><?php echo $couponSendTaskInfo['task_name']; ?></td>
        </tr>

		<?php if(!empty($couponSendTaskInfo['coupon_group_detail'])):?>
        <tr>
            <td><label for="">优惠券套餐名称</label></td>
            <td>
                <?php echo $couponSendTaskInfo['coupon_group_name']; ?>
            </td>
        </tr>
        
        <tr>
            <td><label for="">套餐详情</label></td>
            <td>
                <table class="table table-striped">
                    <tr>
                        <td><label for="">优惠券名称</label></td>
                        <td><label for="">优惠券数量</label></td>
                    </tr>
                    <?php foreach ($couponSendTaskInfo['coupon_group_detail'] as $key => $couponGroupList) { ?>
                    <tr>
                        <td><?php echo $couponGroupList['name'] ?></td>
                        <td><?php echo $couponGroupList['number'] ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
        <?php else:?>
        <tr>
            <td><label for="">优惠券列表</label></td>
            <td>
                <table class="table table-striped">
                    <tr>
                    	<td><label for="">优惠券ID</label></td>
                        <td><label for="">优惠券名称</label></td>
                        <td><label for="">优惠券数量</label></td>
                    </tr>
                    <?php foreach ($couponSendTaskInfo['coupon_list'] as $couponId => $coupon) { ?>
                    <tr>
                    	<td><?php echo $coupon['coupon_id'] ?></td>
                        <td><?php echo $coupon['coupon_name'] ?></td>
                        <td><?php echo $coupon['number'] ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
        <?php endif;?>
        <tr>
            <td><label for="">用户使用总数</label></td>
            <td>
                <?php echo $couponSendTaskInfo['user_total_num'] ?>
            </td>
        </tr>
        <tr>
            <td><label for="">已使用优惠券总数</label></td>
            <td>
                <?php echo $couponSendTaskInfo['user_coupn_total_num'] ?>
            </td>
        </tr>
        <tr>
            <td><label for="">发券时间</label></td>
            <td>
                <?php echo $couponSendTaskInfo['send_time'] ?>
            </td>
        </tr>
    </table>
</div>


<!-- 优惠券统计数量 -->
<div class="coupon-product">
    <label for="">优惠券列表统计：</label>
    <table class="coupon-table">
        <tr>
            <td><label for="">优惠券列表</label></td>
            <td><label for="">优惠券种类</label></td>
            <td><label for="">套餐内数量</label></td>
            <td><label for="">发送总数</label></td>
            <td><label for="">使用总数</label></td>
            <td><label for="">剩余可用数量</label></td>
        </tr>
        <?php foreach ($couponStatisticsInfo as $key => $couponStatistics) { ?>
        <tr>
            <td><?php echo $couponStatistics['coupon_name'] ?></td>
            <td><?php echo $couponStatistics['coupon_type'] ?></td>
            <td><?php echo $couponStatistics['coupon_num'] ?></td>
            <td><?php echo $couponStatistics['send_total_num'] ?></td>
            <td><?php echo $couponStatistics['use_total_num'] ?></td>
            <td><?php echo $couponStatistics['surplus_total_num'] ?></td>
        </tr>
        <?php  } ?>
    </table>
</div>


<!-- 发券单品统计 -->
<div>
    <label for="">单品统计：</label>
    <table class="coupon-table">
    <thead>
      <tr>
        <th rowspan="2">单品名称</th>
        <?php foreach ($productStatisticsInfo['productInfo'] as $couponName): ?>
        	<th colspan="2"><?php echo $couponName['coupon_name']; ?></th>
        <?php endforeach ?>
        <th colspan="2">合计</th>
      </tr>
      <tr>
        <?php foreach ($productStatisticsInfo['productInfo'] as $couponName): ?>
        <th>销量</th>
        <th>销售额 </th>
        <?php endforeach ?>
        <th>销量</th>
        <th>销售额 </th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($productStatisticsInfo['couponList'] as $productName => $couponList): ?>
      <tr>
        <td><?php echo $productName; ?></td>
        <?php foreach ($couponList as $couponId => $statisticsInfo): ?>
            <td><?php echo $statisticsInfo['sales_volume']; ?></td>
            <td><?php echo $statisticsInfo['sales_quota']; ?></td>
        <?php endforeach ?>
        <td><?php echo $productStatisticsInfo['productSaleTotal'][$productName]['total_sales_volume']?></td>
        <td><?php echo $productStatisticsInfo['productSaleTotal'][$productName]['total_sales_quota']?></td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>
