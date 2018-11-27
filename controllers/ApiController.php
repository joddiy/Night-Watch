<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   11/03/2018 9:23 PM
 * fileName :    ApiController.php
 */

namespace app\controllers;

use app\components\Common;
use app\components\RestController;
use app\components\XMMitalk;
use app\components\XMPassport;
use app\models\AdminLog;
use app\models\AdminToken;
use app\models\AdminUser;
use app\components\XMCas;
use app\models\Company;
use app\models\Graph;
use app\models\LbArchitecture;
use app\models\LbBem;
use app\models\News;
use app\models\OSISoft;
use Yii;
use yii\base\ErrorException;


/**
 * Class ApiController
 * @package app\controllers
 */
class ApiController extends RestController
{

    /**
     * @return array
     */
    public function actionWatchGpu()
    {
        try {
            return $this->formatRestResult(self::SUCCESS, []);
        } catch (\Exception $e) {
            return $this->formatRestResult(self::FAILURE, $e->getMessage());
        }
    }
}