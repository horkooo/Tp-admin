<?php
/**
 * Created by BoBo.
 * Date: 2018/8/22 16:20
 * Function:短信服务,仅支持聚合数据
 */
namespace service;

use think\Cache;
use Flc\Alidayu\Client;
use Flc\Alidayu\Requests\IRequest;
class SmsService {

    /*
     * 发送短信
     * @param $mobile number 手机号
     * @param $scene string 场景
     * @param $code string 验证码
     * @return json
     */
    public static function send($mobile,$scene,$code){
        $sms_type = sysconf('sms_type');
        $data = self::$sms_type($mobile,$scene,$code);
        return $data;
    }

    /*
     * 聚合短信
     */
    private static function juhe($mobile,$scene,$code){
        $base_url = "http://v.juhe.cn/sms/send";
        $tpl_scene = self::sms_tpl();
        $tmp_string = '#code#='.$code;
        $tpl_value = urlencode($tmp_string);
        $smsConf = array(
            'key'   => sysconf('sms_juhe_appkey'),
            'mobile'    => $mobile,
            'tpl_id'    => $tpl_scene[$scene],
            'tpl_value' => $tpl_value,
        );
        $result = HttpService::post($base_url,$smsConf);
        $result = json_decode($result,true);
        if($result['error_code'] == 0){
            $data = ['errcode'=>"0",'msg'=>"短信发送成功"];
        }else{
            $data = ['errcode'=>"1",'msg'=>$result['reason']];
        }
        return $data;
    }

    /*
     * 阿里大鱼短信
     */
    private static function alidayu($mobile,$scene,$code){
        $config = ['app_key'=> sysconf('sms_aliyun_appid'),'app_secret'=> sysconf('sms_aliyun_appsecret')];
        $param = ['code'=>$code];
        $tpl_scene = self::sms_tpl();
        $tpl_id = $tpl_scene[$scene];
        $sign = sysconf('sms_aliyun_signname');
        Client::configure($config);
        $resp = Client::request('alibaba.aliqin.fc.sms.num.send', function (IRequest $req) use($mobile,$param,$sign,$tpl_id){
            $req->setRecNum($mobile)
                ->setSmsParam($param)
                ->setSmsFreeSignName($sign)
                ->setSmsTemplateCode($tpl_id);
        });
        return json_decode(json_encode($resp),true);
    }

    /*
     * 阿里云
     * 阿里大鱼和阿里云为两个接口
     */
    public static function aliyun($mobile,$scene,$code){
        $target = "dysmsapi.aliyuncs.com";
        date_default_timezone_set("GMT");
        $dateTimeFormat = 'Y-m-d\TH:i:s\Z'; // ISO8601规范
        $accessKeyId = sysconf('sms_aliyun_appid');
        $accessKeySecret = sysconf('sms_aliyun_appsecret');
        $signName = sysconf('sms_aliyun_signname');
        $tpl_scene = self::sms_tpl();
        $ParamString = json_encode(['code'=>$code,'product'=>"ytx"]);
        $data = array(
            // 公共参数
            'SignName'=> $signName,
            'Format' => 'XML',
            'Version' => '2017-05-25',
            'AccessKeyId' => $accessKeyId,
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce'=> uniqid(),
            'Timestamp' => date($dateTimeFormat),
            // 接口参数
            'Action' => 'SendSms',
            'TemplateCode' => $tpl_scene[$scene],
            'PhoneNumbers' => $mobile,
            'TemplateParam' => $ParamString,
            'RegionId' => "cn-hangzhou"
        );
        $data['Signature'] = self::getSignature($data, $accessKeySecret);
        // 发送请求
        $result = simplexml_load_string(HttpService::get($target,$data));
        $result = json_decode(json_encode($result),true);
        $ok_msg = ['errcode'=>"0",'msg'=>"短信发送成功"];
        $fail_msg = ['errcode'=>"1",'msg'=>$result['Message']];
        $res = ($result['Code'] == "OK")?$ok_msg:$fail_msg;
        return $res;
    }

    /*
     * 上海助通
     */
    private static function zhutong($mobile,$scene,$code){
        $base_url = "http://api.zthysms.com/sendSms.do";
        $username = sysconf('sms_zhutong_account');
        $upass = sysconf('sms_zhutong_passwd');
        $tkey = date("YmdHis");
        $password = md5(md5($upass).$tkey);
        $contentlist = self::sms_tpl();  //$code隐藏在模板内
        $content = $contentlist[$scene];
        $param = ['username'=>$username,'tkey'=>$tkey,'password'=>$password,'mobile'=>$mobile,'content'=>$content];
        $result = HttpService::post($base_url,$param);
        $result = explode(",",$result);
        if($result[0] == 1){
            $data = ['errcode'=>"0",'msg'=>"短信发送成功"];
        }else{
            $data = ['errcode'=>"1",'msg'=>$result[1]];
        }
        return $data;
    }

    /*
     * 获取短信模板
     */
    private static function sms_tpl(){
        $data = ['reg'=> sysconf('sms_tpl_reg'),'find'=> sysconf('sms_tpl_find'),'notice'=>sysconf('sms_tpl_notice')];
        return $data;
    }

//--下面两个方法是为阿里云短信签名用，勿删
    private static function getSignature($parameters, $accessKeySecret){
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . self::percentEncode($key)
                . '=' . self::percentEncode($value);
        }
        // 生成用于计算签名的字符串 stringToSign
        $stringToSign = 'GET&%2F&' . self::percentencode(substr($canonicalizedQueryString, 1));
        // 计算签名，注意accessKeySecret后面要加上字符'&'
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
        return $signature;
    }

    private static function percentEncode($str){
        // 使用urlencode编码后，将"+","*","%7E"做替换即满足ECS API规定的编码规范
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }



}