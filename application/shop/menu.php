<?php
/**
 * Created by BoBo.
 * Date: 2018/10/18 10:58
 * Function:后台菜单配置示例
 */
return [
    'top_menu'=>['title'=>"商城管理",'icon'=>"",'url'=>"#",'sort'=>1,'status'=>1],
    'sub_menu'=>[
        ['title'=>"会员管理",'icon'=>"",'url'=>"#",'sort'=>1,'status'=>1,'children'=>[
            ['title'=>"会员列表",'icon'=>"fa fa-mars-double",'url'=>"/shop/member/index",'sort'=>1,'status'=>1],
            ['title'=>"等级管理",'icon'=>"fa fa-file-picture-o",'url'=>"/shop/member/level",'sort'=>2,'status'=>1]
        ]],
        ['title'=>"商品管理",'icon'=>"",'url'=>"#",'sort'=>2,'status'=>1,'children'=>[
            ['title'=>"添加商品",'icon'=>"fa fa-mars-double",'url'=>"/shop/goods/add",'sort'=>1,'status'=>1],
            ['title'=>"商品列表",'icon'=>"fa fa-file-picture-o",'url'=>"/shop/goods/list",'sort'=>2,'status'=>1]
        ]],
        ['title'=>"订单管理",'icon'=>"",'url'=>"#",'sort'=>3,'status'=>1,'children'=>[
            ['title'=>"订单列表",'icon'=>"fa fa-mars-double",'url'=>"/shop/order/list",'sort'=>1,'status'=>1],
            ['title'=>"申请维权",'icon'=>"fa fa-file-picture-o",'url'=>"/shop/order/tuikuan",'sort'=>2,'status'=>1]
        ]],
    ]

];