Tp-admin
--
* Tp-admin 是一个基于 Thinkphp 5.0.x 开发的后台管理系统，集成后台系统常用功能,其原型是zoujingli的项目ThinkAdmin。
* 项目安装及二次开发请参考 ThinkPHP 官方文档及下面的服务环境说明，数据库 sql 文件存放于项目根目录下。
> 注意：项目测试请另行搭建环境并创建数据库（数据库配置 application/database.php）, 切勿直接使用测试环境数据！


Documentation
--
本项目相关开发文档：http://doc.zkii.net/web/#/7?page_id=54  访问密码：654321
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
* 测试公众号名称：思过崖思过 （大家可以关注它来进行简单的测试）
* 支持短信接口：聚合、阿里云（非阿里大鱼）、上海助通
* 支持微信、支付宝支付
* 支持模块与插件扩展（需自己开发）


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
server {
	listen 80;
	server_name wealth.demo.cuci.cc;
	root /home/wwwroot/ThinkAdmin;
	index index.php index.html index.htm;
	
	add_header X-Powered-Host $hostname;
	fastcgi_hide_header X-Powered-By;
	
	if (!-e $request_filename) {
		rewrite  ^/(.+?\.php)/?(.*)$  /$1/$2  last;
		rewrite  ^/(.*)$  /index.php/$1  last;
	}
	
	location ~ \.php($|/){
		fastcgi_index   index.php;
		fastcgi_pass    127.0.0.1:9000;
		include         fastcgi_params;
		set $real_script_name $fastcgi_script_name;
		if ($real_script_name ~ "^(.+?\.php)(/.+)$") {
			set $real_script_name $1;
		}
		fastcgi_split_path_info ^(.+?\.php)(/.*)$;
		fastcgi_param   PATH_INFO               $fastcgi_path_info;
		fastcgi_param   SCRIPT_NAME             $real_script_name;
		fastcgi_param   SCRIPT_FILENAME         $document_root$real_script_name;
		fastcgi_param   PHP_VALUE               open_basedir=$document_root:/tmp/:/proc/;
		access_log      /home/wwwlog/domain_access.log    access;
		error_log       /home/wwwlog/domain_error.log     error;
	}
	
	location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
		access_log  off;
		error_log   off;
		expires     30d;
	}
	
	location ~ .*\.(js|css)?$ {
		access_log   off;
		error_log    off;
		expires      12h;
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
*虽然本项目开发周期只有短短几天，但是关于此项目后期不再提供任何更新。除非有人反馈存在重大BUG。所以本版本也算是最终版了。开发中有任何问题均可QQ留言。Bye！