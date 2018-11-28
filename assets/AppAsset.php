<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
        'app\assets\NiftyAsset',
    ];

    // add more js file to html
    public static function addScript($view, $jsFile, $option = [])
    {
        $view->registerJsFile($jsFile, $option);
    }

    // add more css file to html
    public static function addCss($view, $cssFile, $option = [])
    {
        $view->registerCssFile($cssFile, $option);
    }
}
