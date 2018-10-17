<?php
/**
 * Created by BoBo.
 * Date: 2018/8/22 15:42
 * Function:会员基础控制器
 */
namespace controller;

use think\Controller;
use think\Db;
use think\Session;

class BasicMember extends Controller{

    public $UserInfo;
    public $table = "SystemMember";
    public function _initialize(){
        $user = Session::get('member');
        if(empty($user)){
            $this->redirect('@wap/login');
        }else{
            $this->UserInfo = $user;
        }
        
    }

}