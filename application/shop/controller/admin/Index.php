<?php
namespace app\shop\controller\admin;

class Index
{
    public function index(){
        echo "<br/>";
        $url = url('shop/admin.index/index');
        $url2 = url('shop/admin.member/index');
        echo "本页面地址：".$url;
        echo "<br/>";
        echo "会员列表页面地址：".$url2;
        echo "<br/>";
        echo addon_url('test://index/index');
        exit;
    }
}
