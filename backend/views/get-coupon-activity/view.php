<?php
$this->title = '领券活动详情';
$this->params['breadcrumbs'][] = ['label' => '领券活动列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="dataContent">
<script id="dataContentTpl" type="text/html">
    <table class="table table-responsive">
        <tr>
            <td><label>活动名称</label></td>
            <td>{{d.activityData.activity_name}}</td>
        </tr>
        <tr>
            <td><label>活动id</label></td>
            <td>{{d.activityData.activity_id}}</td>
        </tr>
        <tr>
            <td><label>活动地址</label></td>
            <td>{{d.activityData.activity_url}}</td>
        </tr>
        <tr>
            <td><label>活动状态</label></td>
            <td>{{d.activityData.status}}</td>
        </tr>
        <tr>
            <td><label>是否验证关注</label></td>
            <td>{{d.is_verify_subscribe}}</td>
        </tr>
        <tr>
            <td><label>创建时间</label></td>
            <td>{{d.activityData.created_at}}</td>
        </tr>
        <tr>
            <td><label>开始时间</label></td>
            <td>{{d.activityData.start_time}}</td>
        </tr>
        <tr>
            <td><label>结束时间</label></td>
            <td>{{d.activityData.end_time}}</td>
        </tr>
        <tr>
            <td><label>活动描述</label></td>
            <td>{{d.activityData.activity_desc}}</td>
        </tr>
        {{# if(d.couponData.type==1){ }}
        <tr>
            <td><label>优惠券套餐名称</label></td>
            <td>{{d.couponData.coupon_group_id}}</td>
        </tr>
        <tr>
            <td><label>套餐详情</label></td>
            <td>
                <table class="table table-striped">
                    <tr>
                        <td><label for="">优惠券名称</label></td>
                        <td><label for="">优惠券数量</label></td>
                    </tr>
                    {{# $.each(d.couponData.sendCouponList,function(index,item){ }}
                    <tr>
                        <td><label for="">{{item.coupon_name}}</label></td>
                        <td><label for="">{{item.coupon_number}}</label></td>
                    </tr>
                    {{# }) }}
                </table>
            </td>
        </tr>
        {{# } else { }}
        <tr>
            <td><label>优惠券列表</label></td>
            <td>
                <table class="table table-striped">
                    <tr>
                        <td><label for="">优惠券名称</label></td>
                        <td><label for="">优惠券数量</label></td>
                    </tr>
                    {{# $.each(d.couponData.sendCouponList,function(index,item){ }}
                    <tr>
                        <td><label for="">{{item.coupon_name}}</label></td>
                        <td><label for="">{{item.coupon_number}}</label></td>
                    </tr>
                    {{# }) }}
                </table>
            </td>
        </tr>
        {{# } }}
    </table>
</script>
<script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/js/laytpl.js"></script>
<script type="text/javascript">
    var data = <?php echo $activity; ?>;
    console.log(data)
    var dataContentTpl = $("#dataContentTpl").html();
    laytpl(dataContentTpl).render(data,function(html){
        $("#dataContent").html(html);
    });
</script>