<?php

namespace app\assets;

use yii\web\AssetBundle;


class AlertAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/sweetalert/sweetalert.min.css'
    ];
    public $js = [
        'plugins/sweetalert/sweetalert.min.js',
        'https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
