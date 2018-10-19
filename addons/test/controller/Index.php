<?php
/**
 * Created by BoBo.
 * Date: 2018/10/19 14:44
 * Function:插件开发示例控制器
 */
namespace addons\test\controller;

use think\addons\Controller;

class Index extends Controller{

    public function index(){
        return view('index');
    }

}