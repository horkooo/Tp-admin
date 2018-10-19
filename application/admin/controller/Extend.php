<?php
/**
 * Created by BoBo.
 * Date: 2018/10/16 10:14
 * Function:扩展管理
 */
namespace app\admin\controller;

use controller\BasicAdmin;
use service\DataService;
use think\Db;
use think\Build;

class Extend extends BasicAdmin{
    public $table = "SystemModules";

    /*
     * 模块管理
     */
    public function modules(){
        $this->title = "模块列表";
        $db = Db::name($this->table)->where(['id'=>['>','0']]);
        return parent::_list($db);
    }

    public function module_add(){
        if(!$this->request->isPost()){
            return $this->_form($this->table, 'moduleform');
        }
        $pdata = $this->request->post();
        if(isset($pdata['id'])){
            return $this->_form($this->table, 'moduleform');
        }else{
            $pdata['addtime'] = getDT();
            $pdata['status'] = 1;
            $module_path = APP_PATH.$pdata['tag'].DS;
            if(is_dir($module_path)){
                $this->error('模块已存在');
            }else{
                $res = Db::name($this->table)->insertGetId($pdata);
                if($res){
                    Build::module($pdata['tag']);
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }

        }

    }

    public function update_menu(){
        $model_id = $this->request->post('id');
        $module = $this->getModule($model_id);
        $menu_path = APP_PATH.$module['tag'].DS."menu.php";
        if(is_file($menu_path)){
            $menu = include_once $menu_path;
            if(!empty($menu) && is_array($menu)){
                if(!empty($menu['top_menu']['title'])){
                    $is_exist = Db::name("SystemMenu")->where(['title'=>$menu['top_menu']['title']])->find();
                    if(!empty($is_exist)){
                        $this->error("菜单已存在");
                    }else{
                        $create_at = getDT();
                        $main_arr = ['pid'=>0,'target'=>"_self",'create_by'=>0,'create_at'=>$create_at];
                        $menu['top_menu'] = array_merge($menu['top_menu'],$main_arr);
                        $main_id = Db::name("SystemMenu")->insertGetId($menu['top_menu']);
                        foreach($menu['sub_menu'] as $key=>$item){
                            $item_array = ['pid'=>$main_id,'target'=>"_self",'create_by'=>0,'create_at'=>$create_at];
                            $item_children = [];
                            if(isset($item['children'])){
                                $item_children = $item['children'];
                                unset($item['children']);
                            }
                            $item = array_merge($item,$item_array);
                            $menu_id = Db::name("SystemMenu")->insertGetId($item);
                            if(!empty($item_children)){
                                foreach($item_children as $k=>$val){
                                    $val_arr = ['pid'=>$menu_id,'target'=>"_self",'create_by'=>0,'create_at'=>$create_at];
                                    $children = array_merge($val,$val_arr);
                                    Db::name("SystemMenu")->insert($children);
                                }
                            }
                        }
                        $this->success('菜单已更新');
                    }
                }else{
                    $this->error('顶级菜单不存在');
                }
            }
        }else{
            $this->error('配置文件不存在');
        }
    }

    /*
     * 禁用模块
     */
    public function dis_module(){
        if(DataService::update($this->table)){
            $this->success('操作成功');
        }
        $this->error('操作失败，请重试');
    }



    /*
     * 插件管理
     */
    public function plugs(){
        $this->title = "插件列表";
        $this->assign('title',$this->title);
        return view();
    }


    //私有方法
    private function getModule($id){
        $data = Db::name($this->table)->where(['id'=>$id])->find();
        return $data;
    }

}