<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components\xmredis;

use app\components\XMRedis;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Redis Cache implements a cache application component based on [redis](http://redis.io/) key-value store.
 *
 * Redis Cache requires redis version 2.6.12 or higher to work properly.
 *
 * It needs to be configured with a redis [[Connection]] that is also configured as an application component.
 * By default it will use the `redis` application component.
 *
 * See [[Cache]] manual for common cache operations that redis Cache supports.
 *
 * Unlike the [[Cache]], redis Cache allows the expire parameter of [[set]], [[add]], [[mset]] and [[madd]] to
 * be a floating point number, so you may specify the time in milliseconds (e.g. 0.1 will be 100 milliseconds).
 *
 * To use redis Cache as the cache application component, configure the application as follows,
 *
 * ~~~
 * [
 *     'components' => [
 *         'cache' => [
 *             'class' => 'yii\redis\Cache',
 *             'redis' => [
 *                 'hostname' => 'localhost',
 *                 'port' => 6379,
 *                 'database' => 0,
 *             ]
 *         ],
 *     ],
 * ]
 * ~~~
 *
 * Or if you have configured the redis [[Connection]] as an application component, the following is sufficient:
 *
 * ~~~
 * [
 *     'components' => [
 *         'cache' => [
 *             'class' => 'yii\redis\Cache',
 *             // 'redis' => 'redis' // id of the connection application component
 *         ],
 *     ],
 * ]
 * ~~~
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class Cache extends \yii\caching\Cache
{
    /**
     * @var Connection|string|array the Redis [[Connection]] object or the application component ID of the Redis [[Connection]].
     * This can also be an array that is used to create a redis [[Connection]] instance in case you do not want do configure
     * redis connection as an application component.
     * After the Cache object is created, if you want to change this property, you should only assign it
     * with a Redis [[Connection]] object.
     */
    public $redis = 'redis';


    /**
     * Initializes the redis Cache component.
     * This method will initialize the [[redis]] property to make sure it refers to a valid redis connection.
     *
     * @throws InvalidConfigException if [[redis]] is invalid.
     */
    public function init()
    {
        parent::init();
        if (is_string($this->redis)) {
            $this->redis = Yii::$app->get($this->redis);
        } elseif (is_array($this->redis)) {
            if (!isset($this->redis['class'])) {
                $this->redis['class'] = Connection::className();
            }
            $this->redis = Yii::createObject($this->redis);
        }
        if (!$this->redis instanceof Connection) {
            throw new InvalidConfigException("Cache::redis must be either a Redis connection instance or the application component ID of a Redis connection.");
        }
    }

    /**
     * Checks whether a specified key exists in the cache.
     * This can be faster than getting the value from the cache if the data is big.
     * Note that this method does not check whether the dependency associated
     * with the cached data, if there is any, has changed. So a call to [[get]]
     * may return false while exists returns true.
     *
     * @param mixed $key a key identifying the cached value. This can be a simple string or
     *                   a complex data structure consisting of factors representing the key.
     * @return boolean true if a value exists in cache, false if the value is not in the cache or expired.
     */
    public function exists($key)
    {
        return (bool)$this->redis->exists($key);
    }

    /**
     * @inheritdoc
     */
    protected function getValue($key)
    {
        return $this->redis->get($key);
    }

    /**
     * @inheritdoc
     */
    protected function getValues($keys)
    {
        $result = $this->redis->mget($keys);
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function setValue($key, $value, $expire)
    {
        if ($expire == 0) {
            return (bool)$this->redis->set($key, $value);
        } else {
            return (bool)$this->redis->set($key, $value) &&
            (bool)$this->redis->expire($key, $expire);
        }
    }

    /**
     * @inheritdoc
     */
    protected function setValues($data, $expire)
    {
        if ($expire == 0) {
            return (bool)$this->redis->mset($data);
        } else {
            foreach ($data as $key => $value) {
                $this->redis->set($key, $value);
                $this->redis->expire($key, $expire);
            }
            return TRUE;
        }
    }

    /**
     * @inheritdoc
     */
    protected function addValue($key, $value, $expire)
    {
        if ($expire == 0) {
            return (bool)$this->redis->add($key, $value);
        } else {
            return (bool)$this->redis->add($key, $value) &&
            (bool)$this->redis->expire($key, $expire);
        }
    }

    /**
     * @inheritdoc
     */
    protected function deleteValue($key)
    {
        return (bool)$this->redis->del($key);
    }

    /**
     * @inheritdoc
     */
    protected function flushValues()
    {
        return $this->redis->flushAll();
    }
}