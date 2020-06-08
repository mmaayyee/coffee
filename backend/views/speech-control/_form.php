<?php
$this->registerCssFile('/voice-control/css/app.9ba14affe948d9e60b4a31f3576f0e73.css');
$this->registerJSFile('/voice-control/js/manifest.4027344a0d8ca3e4a762.js?v=2.3');
$this->registerJSFile('/voice-control/js/vendor.fda397fc36f820ada09a.js');
$this->registerJSFile('/voice-control/js/app.17d1f5f07ebfde157ed2.js');
?>
<div id=app></div>
<style>
input[type="file"] {
    display: none;
  }
</style>
  <script type=text/javascript>
   var rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
   var rootData=<?php echo $model; ?>;
  console.log(rootCoffeeStieUrl);
  console.log(rootData);
  //城市
// var rootData={
//     "id": 1,
//     "speech_control_title": "语音控制功能调试",
//     "speech_control_content": "这里是语音控制功能调试内容",
//     "start_time": "2017-10-11",
//     "end_time": "2020-10-13",
//     "status": "5",
//     "scene_list": [
//         {
//             "scene_id": 1,
//             "scene_name": "进入选择支付方式"
//         },
//         {
//             "scene_id": 2,
//             "scene_name": "点击取咖啡"
//         },
//         {
//             "scene_id": 3,
//             "scene_name": "点击兑换码"
//         },
//         {
//             "scene_id": 4,
//             "scene_name": "开始制作"
//         },
//         {
//             "scene_id": 5,
//             "scene_name": "制作完成"
//         }
//     ],
//     "product_list": {
//         "online_product": [
//             {
//                 "product_id": 1,
//                 "product_name": "生机抹茶"
//             },
//             {
//                 "product_id": 2,
//                 "product_name": "香草拿铁"
//             }
//         ],
//         "outline_product": [
//             {
//                 "product_id": 3,
//                 "product_name": "冰萃鸳鸯"
//             },
//             {
//                 "product_id": 4,
//                 "product_name": "卡布奇诺"
//             }
//         ],
//         "selection_product": [
//             {
//                 "product_id": 5,
//                 "product_name": "玛琪雅朵"
//             },
//             {
//                 "product_id": 6,
//                 "product_name": "红丝绒拿铁"
//             }
//         ]
//     },
//     "mechanism_list": [
//         {
//             "mechanism_id": 1,
//             "mechanism_name": "北京分公司"
//         },
//         {
//             "mechanism_id": 2,
//             "mechanism_name": "成都分公司"
//         },
//         {
//             "mechanism_id": 3,
//             "mechanism_name": "上海分公司"
//         }
//     ],
//     "equip_type_list": [
//         {
//             "equip_type_id": 1,
//             "equip_type_name": "3代机"
//         },
//         {
//             "equip_type_id": 2,
//             "equip_type_name": "4代机"
//         },
//         {
//             "equip_type_id": 3,
//             "equip_type_name": "5代机"
//         }
//     ],
//     "channel_list": [
//         {
//             "channel_id": 1,
//             "channel_name": "写字楼"
//         },
//         {
//             "channel_id": 2,
//             "channel_name": "园区"
//         },
//         {
//             "channel_id": 3,
//             "channel_name": "医院"
//         },
//         {
//             "channel_id": 4,
//             "channel_name": "写字楼2"
//         },
//         {
//             "channel_id": 5,
//             "channel_name": "园区2"
//         },
//         {
//             "channel_id": 6,
//             "channel_name": "医院2"
//         },
//         {
//             "channel_id": 7,
//             "channel_name": "写字楼3"
//         },
//         {
//             "channel_id": 8,
//             "channel_name": "园区3"
//         },
//         {
//             "channel_id": 9,
//             "channel_name": "医院3"
//         }
//     ],
//     "check_build_list": [
//         {
//             "build_id": 1,
//             "build_name": "CBD万达广场1号楼大堂"
//         },
//         {
//             "build_id": 2,
//             "build_name": "CBD万达广场4-5号楼廊桥"
//         },
//         {
//             "build_id": 3,
//             "build_name": "CBD万达广场10号楼大堂"
//         }
//     ],
//    "check_product_list": {//编辑时选中的产品列表
//         "online_product": [1],
//         "outline_product": [3,4],

//         "selection_product": []

//     },
//     check_scene_list:[{scene_id:1,"scene_name": "进入选择支付方式"},{scene_id:3,"scene_name": "点击兑换码"}],
//     build_list_path:"www.baidu.com"

// }
//    console.log(rootData)
</script>
