## 使用Predis实现的Yii2-redis
* 原因：Yii2自带的redis使用了事务，lua等高级特性，通用cache很挫的没有支持，只好用predis来自己实现了
