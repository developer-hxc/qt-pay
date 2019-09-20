桥通天下支付
============

## 使用要求
```
php >= 7.0.0
ThinkPHP 5.0.*
```

## 安装

```shell
$ composer require hxc/qt-pay
```
安装后将在`APP_PATH/app/controller/Qtpay.php`中生成代码，文件已存在则不覆盖。

## 配置文件

配置文件位于`APP_PATH/extra/pay.php`，详细说明查看文件内注释。重点关注以下配置项。
```php
'env' => 'production' //值：dev，production；dev开发环境：不走微信/支付宝支付，直接支付成功；production线上环境：走微信/支付宝支付
```
也可以在控制器初始化的时候传入配置，优先级高于配置文件。
```php
protected function _initialize()
{
    $this->wx_config = [];//自定义微信支付所需参数，优先级高于配置文件
    $this->ali_config = [];//自定义支付宝所需参数，优先级高于配置文件
    $this->validate = '';  //自定义验证器，需填写完整类名
    $this->getConfig();//读取配置
}
```

## 如何使用
- 只需要关心生成订单和支付成功回调的逻辑即可
-     支付接口：/app/qtpay/pay
      * 参数：
      * 【tpye】：wechat，alipay
      * 【func】:app（app支付，微信/支付宝）,mp（公众号支付，微信）,wap（手机网站支付，微信/支付宝）,mini（小程序支付，微信/支付宝）,web（网页支付，支付宝）

**必须实现以下方法**

- getOrder($type)  
说明：获取订单参数
参数：`$type`为`wechat`、`alipay`或`dev`，标记支付方式  
返回：返回订单所需参数数组，参考支付宝或微信支付文档。  

- notify($data, $flag)  
说明：处理支付回调  
参数：`$data`为回调数据；`$flag`为`wx`、`ali`或`dev`，标记支付方式  
返回：处理成功时必须返回布尔值`true`，其他值认为回调处理失败。支付平台将会按照他们的规则重试。  

同时，提供以下方法

- queryOrder(array/string $order_sn, string $type)  
说明：查询订单接口  
参数：`$order` 为 `string` 类型时，请传入系统订单号，对应支付宝或微信中的 `out_trade_no`； `array` 类型时，参数请参考支付宝或微信官方文档。`$type`请传入'wechat'或'alipay'。  
返回：查询成功，返回 `Yansongda\Supports\Collection` 实例，可以通过 `$colletion->xxx` 或 `$collection['xxx']` 访问服务器返回的数据。  
异常：`GatewayException` 或 `InvalidSignException`  

- refund(array $order, $type)  
说明：退款接口  
参数：`$order` 数组格式，退款参数。`$type`请传入'alipay'或'wechat'。  
返回：退款成功，返回 `Yansongda\Supports\Collection` 实例，可以通过 `$colletion->xxx` 或 `$collection['xxx']` 访问服务器返回的数据。  
异常：`GatewayException` 或 `InvalidSignException`
      