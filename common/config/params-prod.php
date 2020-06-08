<?php
/** 线上环境域名配置 */
return [
    'frontend'             => 'http://erp.coffee08.com/',
    'agentUrl'             => 'http://agentback.coffee08.com/agents-api/',
    'coffeeUrl'            => 'http://wx.coffee08.com/service/',
    'fcoffeeUrl'           => 'http://wx.coffee08.com/',
    'coffeeOrgUrl'         => 'https://wx.coffee08.com/organization-api/',
    'erpapi'               => 'http://erpback.coffee08.com/',
    'downloadUrl'          => 'http://report.coffee08.com/',
    //处理跨域图片路径
    'serverUrl'            => 'http://www1.coffee08.com/js/php/controller.php', //处理跨域图片路径
    // 图片访问路径前缀
    'imageUrlPrefix'       => 'http://www1.coffee08.com', // 图片访问路径前缀
    'tagId'                => 40,
    'coffeeUrlSign'        => 2, // 区分正式环境还是测试环境
    'distribution_agentid' => 4, // 配送应用id
    'equip_agentid'        => 6, // 设备应用id
    'water_agentid'        => 7, // 供水应用id
    'surpplier_agentid'    => 8, // 投放应用id
    'self_helper'          => 13, // 个人助手
    'agent_id'             => 1000002, //代理商ID
    'delivery_agentid'     => 1000003, //外卖配送id
    'building_record'      => 1000004, // 点位评估
    'system_agentid'       => 0, // 系统助手id
    'corpid'               => 'wx398f0d55f5c0122c', // 企业号corpid
    'secret'               => [
        0              => 'cyR6UaATI4eEkKWCOVj5vzqUwvtKpXbKMuXFdh9UN4c',
        4              => 'hot9IePS2Qhfl93msejtBRURwviYsH8HQonRW4Ac6q8',
        6              => 'OfK1F5O3Xds1jTjvtqWRONWVk4rwORGAJ13U-jPpYkE',
        7              => '7Fv1wNxpCS15LZBU6Xj-g8g8PDyMfYqYOl4Jbk1MCm0',
        8              => '8XckKgQ6P7p9w41USnQ9TplMed2U5AdOkr8bF5zSwAw',
        13             => 'WIyLDFEbcM1V3hltEK6VnnNjsFFjWp8zSjilcZj6wEY',
        1000002        => 'eWvrcmzxBQ4pBRCD3F2bfd51TikIX7HSlA_asUsVeKo',
        1000003        => 'CXTQpKz-ywc_THkgSeSZq9X3MbaqgazkenVPttxzG5I',
        1000004        => 'APHOa9G2AbE-ms4b7VppsBm8BkFnslPHub2Y0zfCYw0',
        'address_book' => 'JIX0XZStMZkH_5qeKQ89d8VaPJ_pbVhPAANJD79-92A',
    ],
];
