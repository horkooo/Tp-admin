<?php
/**
 * Created by BoBo.
 * Date: 2018/10/10 16:07
 * Function:支付服务
 */
namespace service;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PaymentService{

    /*
     * 支付服务
     * composer地址：https://packagist.org/packages/yansongda/pay
     * @param $type 支付类型
     * @param $order 订单信息
     * @return Array
     * 支付宝支付：web（电脑支付）、wap（手机网站支付）、app（APP 支付）、pos（刷卡支付）、scan	（扫码支付）、transfer（帐户转账）
     * 微信支付：mp(公众号支付）、miniapp（小程序支付）、wap（H5 支付）、scan（扫码支付）、pos（刷卡支付）、app（APP 支付）
     * transfer	（企业付款）、redpack（普通红包）、groupRedpack	（分裂红包）
     */
    public static function pay($type,$order,$method){
        $method_arr = ['web','wap','app','pos','scan','transfer','mp','miniapp','redpack','groupRedpack'];
        if(in_array($type,['alipay','wechat']) && !empty($order) && in_array($method,$method_arr)){
            $config = self::getConfig($type);
            $res = Pay::$type($config)->$method($order)->send();
            $result = ['errcode'=>"ok",'msg'=>"请求成功",'result'=>$res];
        }else{
            $result = ['errcode'=>"fail",'msg'=>"参数有误"];
        }
        return $result;
    }

    /*
     * 查询订单
     * @param $paytype string 支付类型
     * @param $order array 订单编号
     * @param $ordertype boole 是否退款单
     */
    public static function find($paytype,$order,$ordertype=false){
        $config = self::getConfig($paytype);
        if($ordertype){
            $result = Pay::$paytype($config)->find($order,true);
        }else{
            $result = Pay::$paytype($config)->find($order);
        }
        return $result;
    }

    /*
     * 订单退款
     * @param $paytype string 支付方式
     * @param $order array 订单信息
     */
    public static function refund($paytype,$order){
        $config = self::getConfig($paytype);
        $result = Pay::$paytype($config)->refund($order);
        return $result;
    }

    /*
     * 微信红包
     */
    public static function redpack($order){
        $config = self::getConfig('wxpay');
        $result = Pay::wechat($config)->redpack($order);
        return $result;
    }

    /*
     * 获取支付状态
     * @return array 所有支付状态
     */
    public static function getPayType(){
        $data = ['alipay'=>sysconf('alipay_status'),'wxpay'=>sysconf('wxpay_status')];
        return $data;
    }

    /*
     * 获取单个支付状态
     * @param $paytype string 支付类型(alipay,wxpay)
     * @return boole
     */
    public static function isEnable($paytype){
        $key = $paytype."_status";
        if(sysconf($key) == 1){
            return true;
        }
        return false;
    }

    /*
     * 配置管理
     */
    private static function getConfig($paytype){
        switch($paytype){
            case "alipay":
                $data = ['app_id'=>sysconf('alipay_appid'),'notify_url'=>sysconf('alipay_notify_url'),
                    'return_url'=>sysconf('alipay_return_url'),'ali_public_key'=>sysconf('alipay_public_key'),
                    'private_key'=>sysconf('alipay_private_key'),
                    'log'=>['file'=>"./logs/alipay.log",'level'=>"info",'type'=>"daily",'max_file'=>30],
                    'http' => [
                        'timeout' => 5.0,
                        'connect_timeout' => 5.0,
                    ],
                    'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
                ];
                break;
            case "wxpay":
                $data = [
                    'appid'=>sysconf('wxpay_appid'),  //APP APPID
                    'app_id'=>sysconf('wxpay_app_id'),  //公众号 APPID
                    'miniapp_id'=>sysconf('wxpay_miniapp_id'),  //小程序 APPID
                    'mch_id'=>sysconf('wxpay_mch_id'),  //商户 APPID
                    'key'=>sysconf('wxpay_key'),  //密钥
                    'notify_url'=>sysconf('wxpay_notify_url'),  //退款用 格式：./cert/apiclient_cert.pem
                    'cert_client'=>sysconf('wxpay_cert_client'), //退款用 格式：./cert/apiclient_key.pem
                    'cert_key'=>sysconf('wxpay_cert_key'),
                    'log'=>['file'=>"./logs/wechat.log",'level'=>"info",'type'=>"daily",'max_file'=>30],
                    'http' => [
                        'timeout' => 5.0,
                        'connect_timeout' => 5.0,
                    ],
                    'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
                ];
                break;
        }
        return $data;
    }

}