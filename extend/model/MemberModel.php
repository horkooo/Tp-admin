<?php
/**
 * Created by BoBo.
 * Date: 2018/10/17 16:33
 * Function:会员数据模型
 */
namespace think\model;
use think\Model;
use think\Db;
use think\Session;
use think\Cache;

class MemberModel extends Model{

    public $table = "SystemMember";

    /*
     * 用户登录
     * @param $member array 会员信息
     * @return Object
     */
    public function setLogin($member){
        //自定义常量
        if(is_array($member) && !empty($member)){
            Session::set('member');
            return json_encode(['code'=>"0",'msg'=>"设置成功"]);
        }
        return json_encode(['code'=>"1",'msg'=>"参数有误"]);
    }

    /*
     * 获取会员指定字段
     * @param $uid int 用户ID
     * @param $field string 字段
     * @return Array
     */
    public function getUserField($uid,$field='openid'){
        $data = Db::name($this->table)->where(['uid'=>$uid])->field($field)->find();
        return $data;
    }

    /*
     * 获取会员信息
     */
    public function getMemberInfo($where){
        $data = Db::name($this->table)->where($where)->find();
        return $data;
    }






}
