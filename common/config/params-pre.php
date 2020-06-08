<?php
/** 预上线环境域名配置 */
return [
    'frontend'             => 'http://erppre.coffee08.com/',
    'agentUrl'             => 'https://agentbacktest.coffee08.com/agents-api/',
    'coffeeUrl'            => 'https://wxpre.coffee08.com/service/',
    'fcoffeeUrl'           => 'https://wxpre.coffee08.com/',
    'coffeeOrgUrl'         => 'https://wxtest.coffee08.com/organization-api/',
    'erpapi'               => 'http://erpbackpre.coffee08.com/',
    'downloadUrl'          => 'https://wxpre.coffee08.com/',
    //处理跨域图片路径
    'serverUrl'            => 'https://www1pre.coffee08.com/js/php/controller.php',
    // 图片访问路径前缀
    'imageUrlPrefix'       => 'https://www1pre.coffee08.com/',
    'tagId'                => 8,
    'coffeeUrlSign'        => 2, // 区分正式环境还是测试环境
    'distribution_agentid' => 1000002, // 配送应用id
    'equip_agentid'        => 1000003, // 设备应用id
    'water_agentid'        => 1000005, // 供水应用id
    'surpplier_agentid'    => 1000006, // 投放应用id
    'self_helper'          => 1000007, // 个人助手
    'agent_id'             => 1000004, // 代理商ID
    'delivery_agentid'     => 1000009, // 外卖配送id
    'building_record'      => 1000010, // 点位评估
    'system_agentid'       => 1000008, // 系统助手id
    'corpid'               => 'ww50b60d95b62722e0', // 企业号corpid
    'secret'               => [
        1000002        => 'iePV0tU4y-o028r9gMoRM28lG1s3Sp5oQ5eqBFmrPwI',
        1000003        => 'TbXHvpuhV_jRsGztbXwFWOqW2qHccwlErCq3kw16Eec',
        1000004        => 'vlENfORxmTSgnK6ymQo8M7RiVIRTAM03sfFM-MXyklY',
        1000005        => 'J-5jw-uroeZePYmDCdvir5rrsm4Kf0pGzO-jp5sKXvU',
        1000006        => 'fi3HF6EkydP3Jh-vS_YD5AKhO3e4FFpbD4sFUhDchjk',
        1000007        => 'PxLC9ZLSfNRJV_H-pFP6cnFsvPqJynWpxMiwJs5sCHo',
        1000008        => 'g9tVDK9KFPS2eki8F_jqh7Lq99pD1Xr_DC5UQu8cDfo',
        1000009        => '7UISJryVrEFDDX9GeotFn80IxftbUVbgL3fguknJQZo',
        1000010        => '5sLs0W16w3-ekOZRFXh_kvA0lMfxViQ7IJPvsxQHbeg',
        'address_book' => 'fX7ojWHktRUMMT8E_9AI7RoiqshpS5DuqlCa18_8Ycs',
    ],
];
