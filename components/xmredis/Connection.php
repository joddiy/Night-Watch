<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace app\components\xmredis;

use Predis\Client;
use yii\base\Component;
use yii\db\Exception;

/**
 * Class Connection    use php-redis
 *
 * @package app\components\xmredis
 * @author  YangKe <yangke@xiaomi.com>
 */
class Connection extends Component
{
    /**
     * @event Event an event that is triggered after a DB connection is established
     */
    const EVENT_AFTER_OPEN = 'afterOpen';

    /**
     * @var string the hostname or ip address to use for connecting to the redis server. Defaults to 'localhost'.
     * If [[unixSocket]] is specified, hostname and port will be ignored.
     */
    public $hostname = 'localhost';
    /**
     * @var integer the port to use for connecting to the redis server. Default port is 6379.
     * If [[unixSocket]] is specified, hostname and port will be ignored.
     */
    public $port = 6379;
    /**
     * @var string the password for establishing DB connection. Defaults to null meaning no AUTH command is send.
     * See http://redis.io/commands/auth
     */
    public $password;
    /**
     * @var float timeout to use for connection to redis. If not set the timeout set in php.ini will be used: ini_get("default_socket_timeout")
     */
    public $connectionTimeout = 1;
    /**
     * @var resource redis socket connection
     */
    private $_socket;


    /**
     * Closes the connection when this component is being serialized.
     *
     * @return array
     */
    public function __sleep()
    {
        $this->close();

        return array_keys(get_object_vars($this));
    }

    /**
     * Returns a value indicating whether the DB connection is established.
     *
     * @return boolean whether the DB connection is established
     */
    public function getIsActive()
    {
        return $this->_socket !== null;
    }

    /**
     * Establishes a DB connection.
     * It does nothing if a DB connection has already been established.
     *
     * @throws Exception if connection fails
     */
    public function open()
    {
        if ($this->_socket !== null) {
            try {
                $this->_socket->ping();
                return;
            } catch (\Exception $e) {
                //go on
            }
        }
        $connection = $this->hostname . ':' . $this->port . ', namespace=' . $this->password;
        //connecting redis use php-redis
        try {
            \Yii::trace('Opening redis DB connection: ' . $connection, __METHOD__);
            $parameters = [
                'host'    => $this->hostname,
                'port'    => $this->port,
                'timeout' => $this->connectionTimeout,
            ];
            is_null($this->password) ?: $parameters['password'] = $this->password;
            $this->_socket = new Client($parameters);

        } catch (\Exception $e) {
            \Yii::error("Failed to open redis DB connection ($connection): " . $e->getMessage(), __CLASS__);
            throw $e;
        }
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     */
    public function close()
    {
        if ($this->_socket !== null) {
            $connection = $this->hostname . ':' . $this->port . ', namespace=' . $this->password;
            \Yii::trace('Closing DB connection: ' . $connection, __METHOD__);
            $this->_socket->close();
            $this->_socket = null;
        }
    }

    /**
     * Initializes the DB connection.
     * This method is invoked right after the DB connection is established.
     * The default implementation triggers an [[EVENT_AFTER_OPEN]] event.
     */
    protected function initConnection()
    {
        $this->trigger(self::EVENT_AFTER_OPEN);
    }

    /**
     * Returns the name of the DB driver for the current [[dsn]].
     *
     * @return string name of the DB driver
     */
    public function getDriverName()
    {
        return 'redis';
    }

    /**
     * <caret>  call Reids 方法
     *
     * @param string $name
     * @param array  $params
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $params)
    {

        try {
            $this->open();
            return call_user_func_array(array($this->_socket, $name), $params);
        } catch (\Exception $e) {
            \Yii::trace($name . ":" . $e->getMessage(), __METHOD__);
            throw new Exception($e->getMessage());
        }
    }
}
