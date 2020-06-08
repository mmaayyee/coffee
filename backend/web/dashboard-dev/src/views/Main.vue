<template>
  <div class="main">
    <div class="row head">
      <div class="logo"><img src="../assets/img/logo-black.png"></div>
    </div>
  </div>
</template>
<script>
const websocket = new WebSocket('wss://sockettest.coffee08.com/socket');
export default {
  data () {
    return {
      data: ['aa', 'bb', '其他']
    }
  },
  computed: {
    menuList: function() {
      return [];
    }
  },
  created(){
    this.init();
  },
  methods: {
    init () {
      window.console.log("init...");
      websocket.onopen = () => {
        window.console.log('websocket已连接');
        websocket.send('{"msg":"connect test"}');
      };
      websocket.onclose = () => {
        window.console.log('close');
      };
      websocket.onmessage = evt => {
        const result = JSON.parse(evt.data);
        window.console.log('websocket.onmessage..',result);
        this.data = {
          "todayConsumeCups": "607",
          "todayConsumeAmount": "4850.66",
          "theMonthConsumeCups": "19024",
          "theMonthConsumeAmount": "129950.79",
          "todayOrderCups": "602",
          "todayOrderAmount": "4902.24",
          "totalConsumeCups": "5640622",
          "buildTops": [{
            "name": "四川城市职业学院第六教学楼大堂",
            "num": "20"
          }, {
            "name": "学院国际大厦水牌旁边",
            "num": "16"
          }, {
            "name": "锐创国际中心A座大堂",
            "num": "14"
          }, {
            "name": "北京肿瘤医院门诊大厅心电图等待区",
            "num": "12"
          }, {
            "name": "中国银联研发大楼二层餐厅门口外",
            "num": "12"
          }, {
            "name": "中共西安市委员会1号楼一楼大厅",
            "num": "11"
          }, {
            "name": "海淀科技大厦大堂",
            "num": "10"
          }, {
            "name": "天津市南开医院住院部一层通道",
            "num": "9"
          }, {
            "name": "中国银联股份有限公司1号楼一层大厅入口左侧",
            "num": "9"
          }, {
            "name": "上海高智科技大厦2楼阅览室入口处",
            "num": "9"
          }],
          "prodTops": [{
            "name": "精品冰美式",
            "num": "77"
          }, {
            "name": "精品美式咖啡",
            "num": "72"
          }, {
            "name": "拿铁咖啡",
            "num": "59"
          }, {
            "name": "香草拿铁咖啡",
            "num": "41"
          }, {
            "name": "冰拿铁咖啡",
            "num": "29"
          }, {
            "name": "冰卡布奇诺",
            "num": "26"
          }],
          "consumeProduct": {
            "productName": "",
            "productPrice": 0,
            "fetchTime": "",
            "buildName": ""
          }
        };
      }
      this.onResize();
      window.addEventListener('resize',() => {
        this.onResize();
      });
    },
    onResize () {
      const ww = window.innerWidth;
      const hh = window.innerHeight;
      let fontSize = 100;
      if ((ww / hh) > (1920 / 1080)) {
        fontSize = hh * 100 / 1080;
      } else {
        fontSize = ww * 100 / 1920;
      }
      document.documentElement.style.fontSize = fontSize + 'px';
    }
  }
}
</script>
<style lang="less" scoped>
.main {
  width: 19.2rem;
  height: 10.8rem;
  background-color: #051091;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
  .head {
    .logo {
      background-color: #fbbf4f;
      cursor: pointer;
      img {
        display: block;
        width: 1.3rem;
        height: .5rem;
      }
    }
  }
}
</style>
