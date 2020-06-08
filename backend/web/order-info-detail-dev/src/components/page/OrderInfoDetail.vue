<template>
  <div class="content-body">
    <div class="sub-title" style="margin-top:0">订单信息</div>
    <el-table :data="orderInfoData" border style="width: 100%;margin:10px 0;">
      <el-table-column prop="order_id" label="订单ID">
      </el-table-column>
      <el-table-column prop="order_code" label="订单编号">
      </el-table-column>
      <el-table-column prop="order_type" label="订单类型">
      </el-table-column>
      <el-table-column prop="order_status" label="订单状态">
      </el-table-column>
      <el-table-column prop="user_id" label="用户ID">
      </el-table-column>
      <el-table-column prop="user_mobile" label="用户手机">
      </el-table-column>
    </el-table>
    <el-table :data="orderInfoData" border style="width: 90%;margin:10px 0;">
      <el-table-column prop="created_at" label="订单生成时间">
      </el-table-column>
      <el-table-column prop="pay_at" label="支付时间">
      </el-table-column>
      <el-table-column prop="refund_at" label="退款时间">
      </el-table-column>
    </el-table>
    <div class="sub-title">商品支付信息</div>
    <el-table :data="orderInfoData" border style="width: 100%;margin:10px 0;">
      <el-table-column prop="pay_type" label="支付方式">
      </el-table-column>
      <el-table-column prop="source_price" label="订单原价">
      </el-table-column>
      <el-table-column prop="total_fee" label="订单总价">
      </el-table-column>
      <el-table-column prop="order_total_fee" label="实际支付(含咖豆,优惠券,配送费)">
      </el-table-column>
      <el-table-column prop="real_fee" label="实际支付(含咖豆,优惠券)">
      </el-table-column>
      <el-table-column prop="discount_fee" label="用户优惠">
      </el-table-column>
      <el-table-column prop="source_price_discount" label="公司优惠">
      </el-table-column>
      <el-table-column prop="actual_fee" label="付款金额">
      </el-table-column>
      <el-table-column prop="beans_num" label="咖豆数量">
      </el-table-column>
      <el-table-column prop="beans_amount" label="抵用价值">
      </el-table-column>
      <el-table-column prop="beans_real_amount" label="咖豆实际价值">
      </el-table-column>
      <el-table-column prop="coupon_value" label="优惠券价值">
      </el-table-column>
      <el-table-column prop="order_cups" label="订单数量">
      </el-table-column>
      <el-table-column prop="refund_actual_fee" label="退款金额">
      </el-table-column>
      <el-table-column prop="refund_order_cups" label="退款数量">
      </el-table-column>
    </el-table>
    <div class="sub-title">配送费支付信息</div>
    <el-table :data="orderDeliveryCostInfoData" border style="width: 100%;margin:10px 0;">
      <el-table-column prop="delivery_total_cost" label="总配送费">
      </el-table-column>
      <el-table-column prop="coupon_discount" label="优惠券优惠">
      </el-table-column>
      <el-table-column prop="delivery_cost" label="付款金额">
      </el-table-column>
      <el-table-column prop="beans_num" label="咖豆数量">
      </el-table-column>
      <el-table-column prop="beans_deduction_value" label="咖豆抵用价值">
      </el-table-column>
      <el-table-column prop="beans_amount" label="咖豆实际价值">
      </el-table-column>
      <el-table-column prop="beans_discount_fee" label="咖豆优惠">
      </el-table-column>
    </el-table>
    <div class="sub-title">商品信息</div>
    <el-table :data="goodsInfoData" border style="width: 100%;margin:10px 0;">
      <el-table-column prop="goods_type" label="来源类型">
      </el-table-column>
      <el-table-column prop="source_type" label="具体来源活动名称">
      </el-table-column>
      <el-table-column prop="product_name" label="商品名称">
      </el-table-column>
      <el-table-column prop="order_goods_type" label="商品类型">
      </el-table-column>
      <el-table-column prop="cf_product_price" label="商品原价">
      </el-table-column>
      <el-table-column prop="source_price" label="商品标价">
      </el-table-column>
      <el-table-column prop="payable_price" label="购买应付金额">
      </el-table-column>
      <el-table-column prop="actual_fee" label="实际支付(含咖豆,优惠券)">
      </el-table-column>
      <el-table-column prop="source_number" label="数量">
      </el-table-column>
      <el-table-column prop="delivery_cost" label="配送费">
      </el-table-column>
    </el-table>
    <div class="sub-title">单品详情列表</div>
    <el-table :data="singleProductDetailData" border style="width: 100%;margin:10px 0;">
      <el-table-column prop="order_goods_type" label="来源">
      </el-table-column>
      <el-table-column prop="product_name" label="商品名称">
      </el-table-column>
      <el-table-column prop="source_type" label="商品类型">
      </el-table-column>
      <el-table-column prop="coffee_product_source_price" label="单品原价">
      </el-table-column>
      <el-table-column prop="real_price" label="实付金额(含咖豆)">
      </el-table-column>
      <el-table-column prop="beans_num" label="咖豆数量">
      </el-table-column>
      <el-table-column prop="beans_real_amount" label="咖豆实际价值">
      </el-table-column>
      <el-table-column prop="coupon_value" label="优惠券价值">
      </el-table-column>
      <el-table-column prop="payment_price" label="付款价格">
      </el-table-column>
      <el-table-column label="消费记录ID">
        <template slot-scope="scope">
          <a :href='scope.row.order_record_url' v-if='scope.row.order_record_url!=""'><span>{{ scope.row.order_record }}</span></a>
          <span v-else>{{ scope.row.order_record }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="order_pay_at" label="消费时间">
      </el-table-column>
      <el-table-column prop="order_status" label="状态">
      </el-table-column>
      <el-table-column prop="delivery_cost" label="配送费">
      </el-table-column>
    </el-table>
    <div class="sub-title">使用优惠券信息</div>
    <el-table :data="useCouponInfoData" border style="width: 100%;margin:10px 0;">
      <el-table-column prop="coupon_name" label="优惠券名称">
      </el-table-column>
      <el-table-column prop="coupon_value" label="实际价值">
      </el-table-column>
      <el-table-column prop="coupon_type" label="类型">
      </el-table-column>
      <el-table-column prop="coupon_discount_price" label="优惠金额">
      </el-table-column>
      <el-table-column label="来源">
        <template slot-scope="scope">
          <a :href='scope.row.coupon_source_url' v-if='scope.row.coupon_source_url!=""'><span>{{ scope.row.coupon_source }}</span></a>
          <span v-else>{{ scope.row.coupon_source }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="coupon_get_time" label="领取时间">
      </el-table-column>
    </el-table>
    <div class="sub-title">活动及其优惠</div>
    <el-table :data="getDiscountSummaryData" border style="width: 100%;margin:10px 0;">
<!--       <el-table-column prop="discount_type" label="类型">
</el-table-column> -->
      <el-table-column prop="discount_detailed_type" label="活动类型">
      </el-table-column>
      <el-table-column prop="activity_name" label="活动名称">
      </el-table-column>
      <el-table-column prop="activity_discount_price" label="优惠金额">
      </el-table-column>
    </el-table>
    <div class="sub-title">获得优惠券</div>
    <el-table :data="userGetCouponData" border style="width: 100%;margin:10px 0;">
      <el-table-column prop="scene" label="场景">
      </el-table-column>
      <el-table-column prop="activity_name" label="活动名称">
      </el-table-column>
      <el-table-column prop="coupon_name" label="优惠券名称">
      </el-table-column>
      <el-table-column prop="coupon_num" label="数量">
      </el-table-column>
      <el-table-column prop="coupon_type" label="类型">
      </el-table-column>
      <el-table-column prop="coupon_value" label="优惠券价值(单张)">
      </el-table-column>
      <el-table-column prop="coupon_time" label="时间">
      </el-table-column>
    </el-table>
    <div class="div-center">
        <el-button type="primary" plain @click="goBack">返回</el-button>
    </div>
  </div>
</template>
<script>
/* eslint-disable */
import axios  from  'axios'
export default {
  data() {
    return {
      orderInfoData: [],
      orderDeliveryCostInfoData: [],
      goodsInfoData: [],
      singleProductDetailData: [],
      useCouponInfoData: [],
      getDiscountSummaryData: [],
      userGetCouponData: [],
      listUrl: '/index.php/order-info/index'
    }
  },
  computed: {
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      this.getOrderInfo();
    },
    goBack() {
      window.location.href = this.listUrl;
    },
    // 获取订单信息 支付信息
    getOrderInfo() {
      axios.get(rootCoffeeStieUrl+"order-info-api/order-info-by-info-id.html?orderInfoID="+rootOrderID)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.orderInfoData = [response.data.data];
          this.getOrderDeliveryCostInfoData();
        } else {
          this.alertMsg('获取订单信息错误');
          this.getOrderDeliveryCostInfoData();
        }
      })
      .catch((error)=> {
        this.alertMsg('订单信息接口错误');
        this.getOrderDeliveryCostInfoData();
      });
    },
    // 获取配送费支付信息
    getOrderDeliveryCostInfoData() {
      axios.get(rootCoffeeStieUrl+"order-info-api/get-order-delivery-cost-info.html?orderId="+rootOrderID)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.orderDeliveryCostInfoData = [response.data.data];
          this.getGoodsInfo();
        } else {
          this.alertMsg('获取配送费支付信息错误');
          this.getGoodsInfo();
        }
      })
      .catch((error)=> {
        this.alertMsg('配送费支付信息接口错误');
        this.getGoodsInfo();
      });
    },
    // 获取商品信息
    getGoodsInfo() {
      axios.get(rootCoffeeStieUrl+"order-info-api/order-info-by-id-goods.html?orderInfoID="+rootOrderID)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200") {
          this.goodsInfoData = response.data.data;
          this.getSingleProductDetail();
        } else {
          this.alertMsg('获取商品信息错误');
          this.getSingleProductDetail();
        }
      })
      .catch((error)=> {
        this.alertMsg('商品信息接口错误');
        this.getSingleProductDetail();
      });
    },
    // 获取单品详情列表
    getSingleProductDetail() {
      // return false;
      axios.get(rootCoffeeStieUrl+"order-info-api/get-single-product-details.html?orderInfoID="+rootOrderID)
      .then((response)=> {
        console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.singleProductDetailData = response.data.data;
          this.useCouponInfo();
        } else {
          this.alertMsg('获取单品详情列表错误');
          this.useCouponInfo();
        }
      })
      .catch((error)=> {
        this.alertMsg('单品详情列表接口错误');
        this.useCouponInfo();
      });
    },
    // 使用优惠券信息
    useCouponInfo() {
      // return false;
      axios.get(rootCoffeeStieUrl+"order-info-api/use-coupon-information.html?orderInfoID="+rootOrderID)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.useCouponInfoData = response.data.data;
          this.getDiscountSummary();
        } else {
          this.alertMsg('获取使用优惠券信息错误');
          this.getDiscountSummary();
        }
      })
      .catch((error)=> {
        this.alertMsg('使用优惠券信息接口错误');
        this.getDiscountSummary();
      });
    },
    // 优惠汇总
    getDiscountSummary() {
      // return false;
      axios.get(rootCoffeeStieUrl+"order-info-api/get-discount-summary.html?orderInfoID="+rootOrderID)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.getDiscountSummaryData = response.data.data;
          this.userGetCoupon();
        } else {
          this.alertMsg('获取优惠汇总信息错误');
          this.userGetCoupon();
        }
      })
      .catch((error)=> {
        this.alertMsg('优惠汇总信息接口错误');
        this.userGetCoupon();
      });
    },
    // 获得优惠券
    userGetCoupon() {
      // return false;
      axios.get(rootCoffeeStieUrl+"order-info-api/user-get-coupon.html?orderInfoID="+rootOrderID)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.userGetCouponData = response.data.data;
        } else {
          this.alertMsg('获取获得优惠券信息错误');
        }
      })
      .catch((error)=> {
        this.alertMsg('获得优惠券信息接口错误');
      });
    },
    alertMsg(msg,type)
    {
      // alert(msg);
      let msgType = type?type:"error";
      this.$message({
        message: msg,
        duration:3600,
        type: msgType
      });
      this.scrollMsg();
    },
    scrollMsg()
    {
      if(self!=top){
        let scrollTop = window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop-50;
        let mycss=document.getElementsByClassName("el-message")[0];
        if(mycss){
          mycss.style.cssText="top: "+scrollTop+"px;z-index:1000;";
        }
      }
    },
  },
  components: {}
}

</script>
<style>
.el-table thead {
    color: #666;
    font-weight: 500;
}
.div-center {
    text-align: center;;
    width:100%;
    margin:0 auto;
    margin-bottom: 20px;
  }
</style>
