<?php
return [
    'frontend'             => 'http://erptest.coffee08.com/',
    'agentUrl'             => 'http://agentbacktest.coffee08.com/agents-api/',
    'coffeeUrl'            => 'http://wxtest.coffee08.com/service/',
    'fcoffeeUrl'           => 'http://wxtest.coffee08.com/',
    'coffeeUrlSign'        => 1, // 区分正式环境还是测试环境
    'coffeeOrgUrl'         => 'http://wxtest.coffee08.com/organization-api/',
    'erpapi'               => 'http://erpbacktest.coffee08.com/',
    'distribution_agentid' => 1, // 配送应用id
    'equip_agentid'        => 2, // 设备应用id
    'water_agentid'        => 3, // 供水应用id
    'surpplier_agentid'    => 4, // 投放应用id
    'self_helper'          => 5, // 个人助手
    'system_agentid'       => 0, // 系统助手id
    'agent_id'             => 1000003, //代理商ID
    'delivery_agentid'     => 1000017, //外卖配送id
    'building_record'      => 1000018, // 点位评估
    'corpid'               => 'wxbc92abcfc5ecd12d', // 企业号corpid
    'secret'               => [
        0              => 'RxMsfAeFsZ9G3EA1_X0b_sn3XmKLa2AofI4xr6UkWuY',
        1              => 'RmY2PWOgTbMYXwhmy8WpJ1C-ipL9I5-lDu8mRP2ezEM',
        2              => 'mi3WnpH4BL8hIGztFDk0msdVqcZQz1hZxjhL42sggFI',
        3              => 'ej4BIZcXnvovUQa3iSde-duuYj3HsxE08MTUzD523rY',
        4              => 'fUO16rQKmpIL2lTkwRCu-_vgUx8MdNEK5q4puZe8EPI',
        5              => 'dSkP1Ivp8ITgzK0mnKU-afOi1vfIpyO6w2Htljh7pOA',
        1000003        => 'vfcONdS7xcPLj6B1Xl0Vty4Mw6vaYIkj0h3kwdbtTH8',
        1000017        => 'KRKnzZ50SOlbk6QY_snBziRK18m60A8638WgoxsPhVQ',
        1000018        => 'yOVok_7YVWxw6dcO3ifSoJWo33uKawKPnRCbrWFVUD0',
        'address_book' => 'RfywXFz00D9i6mbGcrcu7H9-bbWPwMIIFSrMWrD9Q1A',
    ],
    'token'                => 'coffee08', // 回调模式验证使用的token
    'encodingAesKey'       => '1JfysUZnq1qum36YwT3PpaaP8x8cjjdgmdutH2ULtDB', // 回调模式验证使用的encodingAesKey
];
