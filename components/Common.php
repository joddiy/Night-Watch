<?php

/**
 * author:      YangKe <yangke@xiaomi.com>
 * createTIme:  20150407 14:05
 * fileName :   Common.php
 */

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Common 公用小方法--和数据无关的方法
 *
 * @package app\components
 * @author  YangKe <yangke@xiaomi.com>
 * @author  yejintao <yejintao@xiaomi.com>
 */
class Common
{
    const KWD_START_TIME = "BI_START_TIME";
    const KWD_END_TIME = "BI_END_TIME";

    public static $multi_delimiters = array(',', ' ', "\n", "\t", '，', '。', '.', '、');

    /**
     *  等效于isset方法，只判断不改变原值
     *
     * @static
     * @param array      $current
     * @param string|int $index
     * @param int        $default
     * @return int
     */
    public static function issetExt($current, $index, $default = 0)
    {
        return isset($current[$index]) ? $current[$index] : $default;
    }

    /**
     * 判断是否传入值是空置，并返回默认值
     *
     * @static
     * @param     $current
     * @param int $default
     * @return int
     */
    public static function emptyExt($current, $default = 0)
    {
        return empty($current) ? $default : $current;
    }

    /**
     * 等效于isset方法，判断，并且改变原值
     *
     * @static
     * @param array      $current
     * @param string|int $index
     * @param int        $default
     * @return string|int
     */
    public static function issetExtW(&$current, $index, $default = 0)
    {
        return $current[$index] = isset($current[$index]) ? $current[$index] : $default;
    }

    /**
     * 改变isset麻烦的写法
     *
     * @param string|int|float $targetValue  目标值
     * @param string           $defaultValue 判定失败默认值
     * @return string
     */
    public static function issetExtend(&$targetValue, $defaultValue = '')
    {
        return isset($targetValue) ? $targetValue : $defaultValue;
    }

    /**
     * 总是返回array格式
     *
     * @static
     * @param array|string|int $par 入参
     * @return array
     */
    public static function alwaysArray($par)
    {
        return is_array($par) ? $par : array($par);
    }

    /**
     * 返回指定DB的配置参数
     *
     * @static
     * @param string $dbName DB名称
     * @return array
     */
    public static function getDbConfigArr($dbName)
    {
        $return = [
            'host'     => '',
            'port'     => '',
            'dbname'   => '',
            'username' => '',
            'password' => '',
        ];
        require(__DIR__ . '/../config/scm_config.php');
        $scmConfig = require(__DIR__ . '/../config/db.php');
        $dsn       = explode(';', explode(':', $scmConfig[$dbName]['dsn'])[1]);
        foreach ($dsn as $item) {
            $item             = explode('=', $item);
            $return[$item[0]] = $item[1];
        }
        $return['username'] = $scmConfig[$dbName]['username'];
        $return['password'] = $scmConfig[$dbName]['password'];
        return $return;
    }

    /**
     * 下载代理：收到一个需要下载的URL，代理下载，并发实时给客户端
     *
     * @todo 只支持简单http下载
     * @static
     * @param string $downloadUrl 需要下载的URL，带http头的标准格式
     */
    public static function downloadProxy($downloadUrl)
    {
        $downloadUrl = explode('://', $downloadUrl)[1];
        $tmpDown     = explode('/', $downloadUrl);
        $host        = array_shift($tmpDown);
        $fileName    = end($tmpDown);
        $getParams   = implode('/', $tmpDown);
        $out         = "GET /{$getParams} HTTP/1.1\r\n";
        $out .= "Host: {$host}\r\n";
        $out .= "Connection: Close\r\n\r\n";
        $fp = fsockopen($host, 80, $errno, $errstr, 10);
        if (!$fp) {
            echo json_encode([
                'code'    => $errno,
                'message' => $errstr,
            ]);
        } else {
            fwrite($fp, $out);
            //取出response的头部信息
            $line   = fgets($fp);
            $header = array();
            while (strspn($line, "\r\n") !== strlen($line)) {
                list($name, $value) = explode(':', $line, 2);
                $header[trim($name)] = trim($line);
                $line                = fgets($fp);
            }
            set_time_limit(0);
            //发出新头部
            header("Content-Disposition:attachment;filename={$fileName}");
            foreach ($header as $key => $value) {
                header($value);
            }
            ob_end_flush();
            while (!feof($fp)) {
                $data = fread($fp, 8192);
                echo $data;
            }
            fclose($fp);
        }
        exit(0);
    }

    /**
     *
     * @param string $reqUrl     请求url
     * @param string $sendParams 发送内容（流）
     * @param int    $timeout    超时时间
     * @return string 返回内容
     */
    public static function sendRequest($reqUrl, $sendParams, $timeout = 5)
    {
        //$params             = array();
        //$encRequest         = base64_encode(json_encode($sendParams));
        //$params['data']     = $encRequest;
        //initialize and setup the curl handler
        $ch      = curl_init();
        $options = array(
            CURLOPT_URL            => $reqUrl,
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => $sendParams,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_USERAGENT      => 'XM_MAIEV',
        );
        curl_setopt_array($ch, $options);
        //execute the request
        $result = curl_exec($ch);
        if ($errno = curl_errno($ch)) {
            $result = curl_error($ch);
            Common::WriteLog('Send msg error. Errorno: ' . $errno . '. ErrorInfo: ' . $result, self::LOGFILE, true);
        }
        return $result;
    }

    public static function getQueryParams($key, $default = '')
    {
        $value = ArrayHelper::getValue(Yii::$app->request->get(), $key);
        return empty($value) ? $default : $value;
    }

    /**
     * 根据多种条件分割字符串
     * 默认根据英文逗号分割
     *
     * @param unknown $str
     * @param array   $delimiters
     * @return multitype:
     */
    public static function explodeByArray($str, Array $delimiters = array(), $filterUnique = false)
    {
        $placeholder = ',';
        if (!empty($delimiters)) {
            $placeholder = array_shift($delimiters);
            foreach ($delimiters as $delimiter) {
                $str = str_replace($delimiter, $placeholder, $str);
            }
        }
        $result = explode($placeholder, $str);
        array_walk($result, function (&$value) {
            $value = trim($value);
        });
        if ($filterUnique) {
            $result = array_unique(array_filter($result));
        }
        return $result;
    }

    /**
     * 返回user信息相关
     *
     * @param null $attr
     * @return mixed
     */
    public static function user($attr = null)
    {
        $user = self::app('user');
        if (!is_null($attr)) {
            if (property_exists($user, $attr) || method_exists($user, 'get' . ucfirst($attr))) {
                return $user->$attr;

            } else {

                if (self::isGuest()) {
                    return false;

                } else {

                    return $user->$attr;
                }

            }
        }
        return $user;
    }

    /**
     * 获得当前app
     *
     * @param unknown_type $attr
     */
    public static function app($attr = null)
    {
        if (!is_null($attr)) {
            return \Yii::$app->$attr;
        }
        return \Yii::$app;
    }

    /**
     * 获得当前的controller
     */
    public static function controller($attr = null)
    {
        $controller = self::app('controller');
        if (!is_null($attr) && !empty($controller)) {
            return $controller->$attr;
        }
        return $controller;
    }

    /**
     * 返回当前是否是匿名访问
     *
     * @return mixed
     */
    public static function isGuest()
    {
        return self::user('isGuest');
    }

    /**
     * 判断当前用户是否是管理员
     */
    public static function isAdmin()
    {
        return self::user()->getIsSuperuser();
    }


    /**
     * 返回params配置文件中的值
     * @param $key
     * @return
     */
    public static function paramsConfig($key)
    {
        return self::app()->params[$key];
    }


    /**
     * 返回request相关
     *
     * @param null $attr
     * @return mixed
     */
    public static function request($attr = null)
    {
        if (!is_null($attr))
            return self::app('request')->$attr;
        else
            return self::app('request');
    }

    /**
     * 将table格式的字符串转化为二维数组
     *
     * @param           $str
     * @param bool|true $hasHeader 第一行是否为表头
     * @return array
     */
    public static function tableToArray($str, $hasHeader = true)
    {
        $returnArr = [];
        $lines     = explode("\n", $str);
        if (!is_array($lines)) {
            return [];
        }
        $titles = explode("\t", $lines[0]);
        if ($hasHeader) {
            unset($lines[0]);
        }
        if (!empty($lines)) {
            foreach ($lines as $line) {
                if (!$line) {
                    continue;
                }
                $eles    = explode("\t", $line);
                $lineArr = array();
                foreach ($titles as $key => $title) {
                    $k           = $hasHeader ? trim($title) : $key;
                    $lineArr[$k] = isset($eles[$key]) ? trim($eles[$key]) : '';
                }
                $returnArr[] = $lineArr;
            }
        }

        return $returnArr;
    }

    /**
     * 计算文字的在页面上的显示宽度,单位用em,正负误差1;含有标点和空格,可能会计算不准确
     *
     * @param string $content  需要测算长度的文字
     * @param string $unit     单位em/px
     * @param int    $fontSize 文字要显示的font size
     * @return int
     */
    public static function getContentWidth($content, $unit = 'px', $fontSize = 12)
    {
        $total  = mb_strlen($content, 'UTF-8');//总字符个数
        $arrNum = $arrAl = $arrCh = 0;
        preg_match_all("/[0-9]{1}/", $content, $arrNum);//数字
        preg_match_all("/[a-zA-Z]{1}/", $content, $arrAl);//字母
        preg_match_all("/([\x{4e00}-\x{9fa5}]){1}/u", $content, $arrCh);//汉字
        $arrNum  = count($arrNum[0]);
        $arrAl   = count($arrAl[0]);
        $arrCh   = count($arrCh[0]);
        $residue = $total - $arrNum - $arrAl - $arrCh;//剩余的,一般是标点符号,当成数字的宽度来处理
        $arrNum += $residue;
        if ($unit == 'em') {
            $length = ceil(0.5559 * $arrNum + 0.5739 * $arrAl + $arrCh);
        } else {
            $length = ceil(0.5559 * $arrNum + 0.5739 * $arrAl + $arrCh) * $fontSize + 1;
        }
        return $length;
    }

    /**
     *  检查传入的对象数组元素是否是指定的class的实例化
     *
     * @param $params   对象数组
     * @param $class    用命名空间指定的类
     * @return bool
     * @throws Exception
     */
    public static function checkEveryParams($params, $class)
    {
        if (is_array($params)) {
            foreach ($params as $item) {
                if (!$item instanceof $class) {
                    throw new Exception("Object must be the class {$class}'s instance", 500);
                }
            }
        }
        return TRUE;
    }

    /**
     * 将Yii Pagination 分页数据转换成Ant 前端需要的数据
     *
     * @param \yii\data\Pagination $pagination
     * @return array
     */
    public static function getAntPagination(\yii\data\Pagination $pagination)
    {
        return [
            'pageSize' => $pagination->getPageSize(),
            'total'    => $pagination->totalCount,
            'current'  => $pagination->getPage() + 1
        ];
    }

    /**
     * 获取ant一列空行header
     *
     * @static
     * @return array
     */
    public static function getEmptyColumn()
    {
        return [
            'label'     => '',
            'attribute' => 'empty',
            'value'     => function () {
                return '';
            },
            'options'   => [
            ],
        ];
    }

    /**
     * 判断是否是JSON格式的字符串
     * @param string $string
     * @return boolean
     */
    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 构造一个 FDS Client
     * @param bool|TRUE $enableHttps    是否允许Https访问
     * @return \FDS\GalaxyFDSClient
     */
    public static function initFdsClient($enableHttps = true, $timeout = 60)
    {
        require("fds/bootstrap.php");

        $appKey = Yii::$app->params['fds_app_key'];
        $appSecret = Yii::$app->params['fds_app_secret'];
        $credential = new \FDS\credential\BasicFDSCredential($appKey, $appSecret);
        $fdsConfig = new \FDS\FDSClientConfiguration();
        ////可以支持https访问，不需要可设置为false
        $fdsConfig->setConnectionTimeoutSecs($timeout);
        $fdsConfig->enableHttps($enableHttps);
        $fdsClient = new \FDS\GalaxyFDSClient($credential, $fdsConfig);

        return $fdsClient;
    }

    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 格式化 微秒时间间隔
     * @author: xieyong
     * @date: 2018/1/8
     * @param $stime 开始时间
     * @param $etime 结束实际
     *
     * @return string
     */
    public static function formatMicrotimeDiff($stime, $etime){
        return number_format(($etime - $stime) * 1000, 2);
    }

    /**
     * 替换SQL的动态时间
     * @param $script
     * @param $dataSection
     * @return mixed
     */
    public static function replaceDynamicTime($script, $dataSection)
    {
        if (strpos($script, self::KWD_START_TIME) && !empty($dataSection['start_time'])) {
            $script = str_replace(self::KWD_START_TIME, "'{$dataSection['start_time']}'", $script);
        }
        if (strpos($script, self::KWD_END_TIME) && !empty($dataSection['end_time'])) {
            $script = str_replace(self::KWD_END_TIME, "'{$dataSection['end_time']}'", $script);
        }

        return $script;
    }

    /**
     * 获取BOM文件头(写utf-8文件必须)
     * @author: xieyong
     * @date: 2018/1/22
     * @return string
     */
    public static function getBOMHeader()
    {
        return chr(239) . chr(187) . chr(191);
    }
}

