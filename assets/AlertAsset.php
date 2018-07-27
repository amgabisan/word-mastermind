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
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
