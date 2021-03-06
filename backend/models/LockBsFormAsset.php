<?php

/*
 * This file is part of the light/yii2-lock-form.
 *
 * (c) lichunqiang <light-li@hotmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace backend\models;

use yii\web\AssetBundle;

/**
 * Asset bundle for the widget.
 *
 * Use this bundle when you have install yii2-bootstrap
 *
 * @version 1.0.2
 *
 * @author lichunqiang <light-li@hotmail.com>
 * @license MIT
 */
class LockBsFormAsset extends AssetBundle {
    /**
     * {@inheritdoc}
     */
    public $js = ['lock.js'];

    /**
     * {@inheritdoc}
     */
    public $sourcePath = __DIR__;

    /**
     * {@inheritdoc}
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
