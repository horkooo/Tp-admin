<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------


use service\DataService;
use service\FileService;
use service\NodeService;
use service\SoapService;
use think\Db;
use Wechat\Loader;

/**
 * 打印输出数据到文件
 * @param mixed $data
 * @param bool $replace
 * @param string|null $pathname
 */
function p($data, $replace = false, $pathname = null)
{
    is_null($pathname) && $pathname = RUNTIME_PATH . date('Ymd') . '.txt';
    $str = (is_string($data) ? $data : (is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true)) . "\n";
    $replace ? file_put_contents($pathname, $str) : file_put_contents($pathname, $str, FILE_APPEND);
}

/**
 * 获取mongoDB连接
 * @param string $col 数据库集合
 * @param bool $force 是否强制连接
 * @return \think\db\Query|\think\mongo\Query
 */
function mongo($col, $force = false)
{
    return Db::connect(config('mongo'), $force)->name($col);
}

/**
 * 获取微信操作对象
 * @param string $type
 * @return \Wechat\WechatMedia|\Wechat\WechatMenu|\Wechat\WechatOauth|\Wechat\WechatPay|\Wechat\WechatReceive|\Wechat\WechatScript|\Wechat\WechatUser|\Wechat\WechatExtends|\Wechat\WechatMessage
 * @throws Exception
 */
function & load_wechat($type = '')
{
    static $wechat = [];
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        $config = [
            'token'          => sysconf('wechat_token'),
            'appid'          => sysconf('wechat_appid'),
            'appsecret'      => sysconf('wechat_appsecret'),
            'encodingaeskey' => sysconf('wechat_encodingaeskey'),
            'mch_id'         => sysconf('wechat_mch_id'),
            'partnerkey'     => sysconf('wechat_partnerkey'),
            'ssl_cer'        => sysconf('wechat_cert_cert'),
            'ssl_key'        => sysconf('wechat_cert_key'),
            'cachepath'      => CACHE_PATH . 'wxpay' . DS,
        ];
        $wechat[$index] = Loader::get($type, $config);
    }
    return $wechat[$index];
}

/**
 * UTF8字符串加密
 * @param string $string
 * @return string
 */
function encode($string)
{
    list($chars, $length) = ['', strlen($string = iconv('utf-8', 'gbk', $string))];
    for ($i = 0; $i < $length; $i++) {
        $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
    }
    return $chars;
}

/**
 * UTF8字符串解密
 * @param string $string
 * @return string
 */
function decode($string)
{
    $chars = '';
    foreach (str_split($string, 2) as $char) {
        $chars .= chr(intval(base_convert($char, 36, 10)));
    }
    return iconv('gbk', 'utf-8', $chars);
}

/**
 * 网络图片本地化
 * @param string $url
 * @return string
 */
function local_image($url)
{
    if (is_array(($result = FileService::download($url)))) {
        return $result['url'];
    }
    return $url;
}

/**
 * 日期格式化
 * @param string $date 标准日期格式
 * @param string $format 输出格式化date
 * @return false|string
 */
function format_datetime($date, $format = 'Y年m月d日 H:i:s')
{
    return empty($date) ? '' : date($format, strtotime($date));
}

/**
 * 设备或配置系统参数
 * @param string $name 参数名称
 * @param bool $value 默认是null为获取值，否则为更新
 * @return string|bool
 */
function sysconf($name, $value = null)
{
    static $config = [];
    if ($value !== null) {
        list($config, $data) = [[], ['name' => $name, 'value' => $value]];
        return DataService::save('SystemConfig', $data, 'name');
    }
    if (empty($config)) {
        $config = Db::name('SystemConfig')->column('name,value');
    }
    return isset($config[$name]) ? $config[$name] : '';
}

/**
 * RBAC节点权限验证
 * @param string $node
 * @return bool
 */
function auth($node)
{
    return NodeService::checkAuthNode($node);
}

/**
 * array_column 函数兼容
 */
if (!function_exists("array_column")) {

    function array_column(array &$rows, $column_key, $index_key = null)
    {
        $data = [];
        foreach ($rows as $row) {
            if (empty($index_key)) {
                $data[] = $row[$column_key];
            } else {
                $data[$row[$index_key]] = $row[$column_key];
            }
        }
        return $data;
    }

}
//----------------------------------
/*
 * 随机字符生成
 */
function randStr($len=6,$format='all') {
    switch($format) {
        case 'all':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; break;
        case 'char':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~'; break;
        case 'number':
            $chars='0123456789'; break;
        case 'charnum':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'verify':
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZ23456789';
            break;
    }
    mt_srand();
    $password="";
    while(strlen($password)<$len)
        $password.=substr($chars,(mt_rand()%strlen($chars)),1);
    return $password;
}

/*
 * 字符加密
 */
function create_pass($str,$salt){
    $data = md5(md5($str).$salt);
    return $data;
}

/*
 * 效验密码
 */
function check_pass($str,$salt,$passstr){
    $data = (create_pass($str,$salt) == $passstr)?true:false;
    return $data;
}

/*
 * 生成邀请码
 */
function CreateCode(){
    $codes = Db::name("SystemMember")->where(['id'=>['>','0']])->column('invite_code');
    $tj_code = randStr(6,'number');
    while(in_array($tj_code,$codes)){
        $tj_code = randStr(6,'number');
    }
    return $tj_code;
}

/*
 * 获取用户特定信息
 */
function getUserField($uid,$field='account'){
    $data = Db::name("SystemMember")->where(['id'=>$uid])->value($field);
    return $data;
}

/*
 * 统一日期时间标准
 */
function getDT(){
    return date("Y-m-d H:i:s");
}

/*
 * 生成唯一订单号
 */
function CreateOrderId(){
    $order_id_main = date('YmdHis') . rand(10000000,99999999);
    //订单号码主体长度
    $order_id_len = strlen($order_id_main);
    $order_id_sum = 0;
    for($i=0; $i<$order_id_len; $i++){
        $order_id_sum += (int)(substr($order_id_main,$i,1));
    }
    //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
    $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);
    return $order_id;
}

/*
 * 获取文章分类名称
 */
function getCateName($id){
    $data = Db::name("SystemArticleCate")->where(['id'=>$id])->value('name');
    return $data;
}

/*
 * 文章分类动态下拉
 */
function getCateList($cate = '0'){
    $data = '<option value="">请选择</option>'.PHP_EOL;
    $list = Db::name("SystemArticleCate")->where(['id'=>['>','0']])->select();
    foreach($list as $key=>$value){
        if($value['id'] == $cate){
            $data .= "<option value='".$value['id']."' selected=''>".$value['name']."</option>".PHP_EOL;
        }else{
            $data .= "<option value='".$value['id']."'>".$value['name']."</option>".PHP_EOL;
        }
    }
    return $data;
}

/*
 * 获取文章图片
 */
function getContentPic($content,$num='1'){
    $pattern="/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
    preg_match_all($pattern,htmlspecialchars_decode($content),$match);
    if(!empty($match[1]) && $num == 1){
        return $match[1][0];
    }else{
        return $match[1];
    }
    return '';
}

/*
 * 获取管理员姓名
 */
function getAdminName($id){
    $data = Db::name('SystemUser')->where('id', $id)->value('username');
    return $data;
}

/*
 * 获取下层无限级ID
 */
function getJuniorIds($parent,$list,$fid='pid'){
    $data = [];
    foreach($list as $item){
        if($item[$fid] == $parent){
            $data[] = $item['id'];
            $data = array_merge($data,getJuniorIds($item['id'],$list,$fid));
        }
    }
    return $data;
}

/*
 * 网站参数配置
 */
function webconf($name, $value = null){
    static $config = [];
    if ($value !== null) {
        list($config, $data) = [[], ['name' => $name, 'value' => $value]];
        return DataService::save('SystemWebconfig', $data, 'name');
    }
    if (empty($config)) {
        $config = Db::name('SystemWebconfig')->column('name,value');
    }
    return isset($config[$name]) ? $config[$name] : '';
}

/*
 * Xml文件增加节点
 * @param $filename string 文件路径
 * @param $data array 追加数组
 * @param $nodename string 节点名称
 * @return boole
 */
function addXmlNode($filename,$data,$nodename){
    if(is_array($data) && !empty($data) && is_file($filename) && is_string($nodename)){
        $contents = file_get_contents($filename);
        $xml = @simplexml_load_string($contents);
        foreach($data as $key=>$value){
            foreach($data[$key] as $k=>$v){
                $item = $xml->addChild($nodename);
                $item->addChild($k,$v);
            }
        }
        $xml->asXML();
        $xmlDocument = new DOMDocument('1.0');
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->formatOutput = true;
        $xmlDocument->loadXML($xml->asXML(),LIBXML_NOERROR);
        $res = file_put_contents($filename,$xmlDocument->saveXML());
        return  $res !== false?true:$res;
    }else{
        throw new \think\Exception('Incorrect parameters');
    }

}

