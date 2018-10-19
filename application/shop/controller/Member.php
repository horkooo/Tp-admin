<?php
/**
 * Created by BoBo.
 * Date: 2018/10/19 9:52
 * Function:会员管理
 */
namespace app\shop\controller;

use controller\BasicAdmin;
use think\Db;

class Member extends BasicAdmin{
    public $table = "SystemMember";

    public function index(){
        $this->title = '会员列表';
        $get = $this->request->get();
        $db = Db::name($this->table)->where(['uid' => ['>',0]]);
        foreach (['username', 'phone', 'mail'] as $key) {
            (isset($get[$key]) && $get[$key] !== '') && $db->whereLike($key, "%{$get[$key]}%");
        }
        if (isset($get['date']) && $get['date'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['date']));
            $db->whereBetween('login_at', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        return parent::_list($db);
    }

}