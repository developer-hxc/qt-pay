<?php

namespace Hxc\Pay;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use think\Config;
use think\Log;
use think\Request;
use think\response\Json;
use Yansongda\Pay\Exceptions\InvalidArgumentException;

/**
 * Trait Pay
 * @package Hxc\Pay
 * @method mixed notify($data, $flag)
 * @method array getOrder($type)
 */
trait Pay
{
    private $ali_config;//支付宝配置
    private $wx_config;//微信配置
    private $env;//dev开发环境：不走微信/支付宝支付，直接支付成功；production线上环境：走微信/支付宝支付
    private $validate;//验证器
    private $func = [
        //微信支付
        'wechat' => [
            'app' => 'app',//app支付
            'mp' => 'mp',//公众号支付
            'wap' => 'wap',//h5支付
            'mini' => 'miniapp',//小程序支付
        ],
        //支付宝支付
        'alipay' => [
            'app' => 'app', //app支付
            'mini' => 'mini', //小程序支付
            'wap' => 'wap', //手机网站支付
            'web' => 'web',//网页支付
        ]
    ];

    /**
     * 支付
     * @param Request $request
     * @return Json
     */
    public function pay(Request $request)
    {
        $params = $request->only(['type', 'func']);
        $className = $this->validate;
        $validate = new $className();
        if (!$validate->check($params)) {
            $this->error($validate->getError());
        }
        if ($this->env === 'dev') {//开发环境
            $order = $this->getOrder('dev');
            $this->notify($order, 'dev');
        } else {//正式环境
            $func = $this->func[$params['type']][$params['func']];
            $config = ($params['type'] == 'wechat' ? $this->wx_config : $this->ali_config);
            $order = $this->getOrder($params['type']);
            $pay = \Yansongda\Pay\Pay::{$params['type']}($config)->{$func}($order);
            if ($params['type'] == 'wechat') {
                return $pay;
            } else {
                return $pay->send();
            }
        }
    }

    /**
     * 微信回调
     * @return Response
     * @throws InvalidArgumentException
     */
    public function WXNotify()
    {
        $pay = \Yansongda\Pay\Pay::wechat($this->wx_config);
        try {
            $data = $pay->verify();
            $this->notify($data->all(), 'wx');//处理回调
        } catch (Exception $e) {
            Log::record('微信支付回调异常：' . $e->getMessage());
        }
        return $pay->success()->send();
    }

    /**
     * 支付宝回调
     */
    public function ALiNotify()
    {
        $pay = \Yansongda\Pay\Pay::alipay($this->ali_config);
        try {
            $data = $pay->verify();
            $this->notify($data->all(), 'ali');//处理回调
        } catch (Exception $e) {
            Log::record('支付宝支付回调异常：' . $e->getMessage());
        }
        return $pay->success()->send();
    }

    /**
     * 初始化
     */
    protected function _initialize()
    {
        $this->getConfig();
    }

    /**
     * 获取配置
     */
    public function getConfig()
    {
        $config = Config::get('pay');
        $wx_config = $config['wx'];
        $ali_config = $config['ali'];
        $wx_config['notify_url'] = url('WXNotify', '', true, true);
        $ali_config['notify_url'] = url('ALiNotify', '', true, true);
        $this->wx_config = array_merge($wx_config, $this->wx_config);
        $this->ali_config = array_merge($ali_config, $this->ali_config);
        $this->env = $config['env'];
        if (!class_exists($this->validate)) {
            $this->validate = Validate::class;
        }
    }
}