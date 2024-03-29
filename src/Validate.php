<?php

namespace Hxc\Pay;

class Validate extends \think\Validate
{
    protected $rule = [
        'type' => 'require|checkType:wechat,alipay',
        'func' => 'require|checkFunc:app,mp,wap,mini,web',
    ];

    protected $message = [
        'type.require' => '支付类型不能为空',
        'func.require' => '支付方式不能为空',
        'type.checkType' => '支付类型参数有误',
    ];

    // 自定义验证规则
    protected function checkFunc($value, $rule, $data)
    {
        return in_array($value, explode(',', $rule)) ? true : '支付方式错误';
    }

    // 自定义验证规则
    protected function checkType($value, $rule, $data)
    {
        return in_array($value, explode(',', $rule)) ? true : '支付类型错误';
    }
}
