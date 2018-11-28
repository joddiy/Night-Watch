<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionGpu()
    {
        // cluster and gpu amount
        $sql = <<<EOF
select cluster as name, count(1) as amount
from gpu_list
group by cluster;
EOF;
        $clusters = \Yii::$app->getDb()->createCommand($sql)->query();
        return $this->render('gpu', [
            'clusters' => $clusters,
        ]);
    }

    public function actionCpu()
    {
        return $this->render('working');
    }

    public function actionDisk()
    {
        return $this->render('working');
    }
}
