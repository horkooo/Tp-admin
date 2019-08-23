Tp-admin
--
* Tp-admin 是一个基于 Thinkphp 5.0.x 开发的后台管理系统，集成后台系统常用功能,其原型是zoujingli的项目ThinkAdmin。
* 项目安装及二次开发请参考 ThinkPHP 官方文档及下面的服务环境说明，数据库 sql 文件存放于项目根目录下。
> 注意：项目测试请另行搭建环境并创建数据库（数据库配置 application/database.php）, 切勿直接使用测试环境数据！


Documentation
--
本项目相关开发文档：http://doc.zkii.net/web/#/4?page_id=21
如有问题可以直接QQ2398156504


Repositorie
--
 Tp-admin 为开源项目，允许您把它用于任何地方，不受任何约束，欢迎 fork 项目。

 演示地址：http://tpadmin.zkii.net

Module
--
* 简易`RBAC`权限管理（用户、权限、节点、菜单控制）
* 自建秒传文件上载组件（本地存储、七牛云存储，阿里云OSS存储）
* 基站数据服务组件（唯一随机序号、表单更新）
* `Http`服务组件（原生`CURL`封装，兼容PHP多版本）
* 微信公众号服务组件（基于[wechat-php-sdk](https://github.com/zoujingli/wechat-php-sdk)，微信网页授权获取用户信息、已关注粉丝管理、自定义菜单管理等等）
* 微信商户支付服务组件（基于[wechat-php-sdk](https://github.com/zoujingli/wechat-php-sdk)，支持JSAPI支付、扫码模式一支付、扫码模式二支付）
* 支付服务，支持微信、支付宝所有支付接口，参见PaymentService.php
* 支持短信接口：聚合、阿里云（非阿里大鱼）、上海助通。参见SmsService.php
* 支持连接手机，操作手机自动完成各项任务。参见AdbService.php 需将手机开启开发者模式，连接电脑，至于云端控制大家可以自行研究。控制器运行在cli模式下，开启exec()函数
* 地图选择器组件已经并入系统内，示例可以增加菜单“/demo/plugs/mapselector”,我就不修改数据库了。-20190823
* 支持模块与插件扩展（需自己开发）

ThinkPHP
--
2018年12月12日，新增TP核心自动定位模板的功能。在引入页面公共头部和底部时，比如index模块下，可以在HTML中写{include file="public/header.html" /}标签，然后把header.html放在index模块下视图文件夹如“view”的下级目录public中。不影响TP文档中的正常流程，目的是为开发者提供更大的方便。

Environment
---
>1. PHP 版本不低于 PHP5.4，推荐使用 PHP7 以达到最优效果；
>2. 需开启 PATHINFO，不再支持 ThinkPHP 的 URL 兼容模式运行（源于如何优雅的展示）。

* Apache

```xml
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```

* Nginx

```
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}
```

Copyright
--
* ThinkAdmin 基于`MIT`协议发布，任何人可以用在任何地方，不受约束
* ThinkAdmin 部分代码来自互联网，若有异议，可以联系作者进行删除
* 核心部分版权请遵循其对应的版权声明，波波添加修改部分则随意使用，波波本人也不在意版权问题。


Note
--
*本项目开发周期不长，基本上属于在别人的基础上二次修改而来。若大家在使用过程中遇到问题，可以随时联系我进行修改。也欢迎大家访问我的博客“菠菜园(www.zkii.net)”。