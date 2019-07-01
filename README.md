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

## 开始使用

在项目根目录执行以下命令，此命令将在`APP_PATH/app/controller/Qtpay.php`中生成代码，文件已存在则不覆盖。
```shell
$ php think init-pay
```

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
      * 【func】:app（app支付，微信/支付宝哦）,mp（公众号支付，微信）,wap（手机网站支付，微信/支付宝）,mini（小程序支付，微信/支付宝）,web（网页支付，支付宝）

**必须实现以下方法**
- 生成订单的方法：/app/qtpay/getOrder
- 处理回调的方法：/app/qtpay/notify
      