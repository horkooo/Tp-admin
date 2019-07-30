<?php
/**
 * Created by bobo.
 * DateTime: 2019/7/24 17:22
 * Function:安卓手机ADB服务
 * Description：该服务没啥特别的用途，仅限于安卓手机机器人自动操作，使用时请打开手机调试模式
 */
class AdbService{

    protected $screenPix = [];
    protected $savePath = "";

    /*
     * 初始化数据
     */
    public function __construct(){
        exec("adb shell wm size",$screen);
        $screen = explode(":",$screen[0]);
        $info = explode("x",$screen[1]);    //1080x2280
        $this->screenPix = [intval($info[0]),intval($info[1])];   //X|Y
        print_r($this->screenPix);
        $this->savePath = __DIR__ . "/images/SH".date("YmdHis").".png";
    }


    /*
     * 单击屏幕指定位置
     */
    public function singleTap($x=0,$y=0){
        $x = empty($x)?$this->screenPix[0]/2:$x;
        $y = empty($y)?$this->screenPix[1]/2:$y;
        exec("adb shell input tap ".$x." ".$y." ");
        return true;

    }

    /*
     * 双击屏幕指定位置
     */
    public function doubleTap($x=0,$y=0){
        $x = empty($x)?$this->screenPix[0]/2:$x;
        $y = empty($y)?$this->screenPix[1]/2:$y;
        exec("adb shell input tap ".$x." ".$y." ");
        exec("adb shell input tap ".$x." ".$y." ");
        return true;
    }

    /*
     * 左滑屏幕
     */
    public function swipeLeft(){
        $x = 100;
        $y = $y1 = intval($this->screenPix[1]/2);
        $x1 = intval($this->screenPix[0]/2)+100;
        $this->swipe($x1,$y,$x,$y1);
        return true;

    }

    /*
     * 右滑屏幕
     */
    public function swipeRight(){
        $y = $y1 = intval($this->screenPix[1]/2);
        $x = 100;
        $x1 = intval($this->screenPix[0]/2)+100;
        $this->swipe($x,$y,$x1,$y1);
        return true;
    }

    /*
     * 上滑屏幕
     */
    public function swipeUp(){
        $x = $x1 = intval($this->screenPix[0]/2);
        $y = intval($this->screenPix[1]/2)+100;
        $y1 = 100;
        $this->swipe($x,$y,$x1,$y1);
        return true;
    }

    /*
     * 下滑屏幕
     */
    public function swipeDown(){
        $x = $x1 = intval($this->screenPix[0]/2);
        $y = 100;
        $y1 = intval($this->screenPix[1]/2)+100;
        $this->swipe($x,$y,$x1,$y1);
        return true;
    }

    /*
     * 屏幕截图
     */
    public function screenShot($filepath){
        $filepath = empty($filepath)?$this->savePath:$filepath;
        $dirname = dirname($filepath);
        if(!is_dir($dirname)){
            mkdir($dirname,0777,true);
        }
        exec("adb exec-out screencap -p > ".$filepath);
        //exec("adb exec-out screencap -p | sed 's/\r\n$//'.$filepath")  //备用方法，部分不兼容机器可尝试使用
        return $filepath;

    }

    /*
     * 输入文本
     */
    public function inputText($text){
        if(empty($text)){
            return false;
        }
        exec("adb shell input text ".$text);
        return true;
    }

    /*
     * 检测手机是否root
     */
    public function hasRoot(){
        exec("adb shell",$data);
        if(strstr($data,"#")){
            return true;
        }elseif(strstr($data,"$")){
            return false;
        }else{
            return "unknown";
        }
    }

    /*
     * 重启手机
     */
    public function reboot(){
        exec("adb shell reboot");
        return true;
    }

    /*
     * 扩展ADB命令
     */
    public function extendCmd($command){
        exec($command,$data,$status);
        return ['status'=>$status,'result'=>$data];
    }



    /*
     * 滑动操作
     */
    private function swipe($x,$y,$x1,$y1){
        return exec("adb shell input swipe ".$x." ".$y." ".$x1." ".$y1." ") !== false;
    }



}