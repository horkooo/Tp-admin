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
        //自定义常量
        define('IS_LOGIN') or define('IS_LOGIN',false);
        $user = Session::get('member');
        if(empty($user)){
            //$this->redirect('@wap/login'); 可设置跳转
        }else{
            define('IS_LOGIN',true);
            /*
             * 这部分代码作用不允许同一账号同时登陆，
            $mem_token = Db::name($this->table)->where(['id'=>$user['id']])->value('token');
            if(empty($user['token']) || ($user['token'] !== $mem_token)){
                $this->redirect('@wap/login');
            }
            */
            $this->UserInfo = $user;
        }
        
    }

}