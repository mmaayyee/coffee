<?php
namespace frontend\modules\api;

/**
 * 
 * @author wlw
 * @date   2018-09-13
 * 
 * 处理erp api调用模块
 *
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'frontend\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
