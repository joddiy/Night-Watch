<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::addScript($this, "/js/jquery.min.js", ["position" => View::POS_HEAD]);
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div id="container" class="effect mainnav-lg">
    <div class="boxed">

        <!--CONTENT CONTAINER-->
        <!--===================================================-->
        <div id="content-container" style="padding-top: 0">

            <!--Page Title-->
            <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
            <div id="page-title">
                <h1 class="page-header text-overflow"><?= $this->title ?></h1>
            </div>
            <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
            <!--End page title-->
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>


            <!--Page content-->
            <!--===================================================-->
            <div id="page-content">
                <?= $content ?>
            </div>

        </div>
        <!--===================================================-->
        <!--END CONTENT CONTAINER-->


        <!--MAIN NAVIGATION-->
        <!--===================================================-->
        <?= $this->render('navbar') ?>
        <!--===================================================-->
        <!--END MAIN NAVIGATION-->

    </div>

    <!-- SCROLL TOP BUTTON -->
    <!--===================================================-->
    <button id="scroll-top" class="btn"><i class="fa fa-chevron-up"></i></button>
    <!--===================================================-->


</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
