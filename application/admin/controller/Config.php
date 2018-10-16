<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller;

use controller\BasicAdmin;
use service\ExtendService;
use service\LogService;

/**
 * 后台参数配置控制器
 * Class Config
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:05
 */
class Config extends BasicAdmin
{

    /**
     * 当前默认数据模型
     * @var string
     */
    public $table = 'SystemConfig';

    /**
     * 当前页面标题
     * @var string
     */
    public $title = '网站参数配置';

    /**
     * 显示系统常规配置
     */
    public function index()
    {
        if (!$this->request->isPost()) {
            return view('', ['title' => $this->title]);
        }
        foreach ($this->request->post() as $key => $vo) {
            sysconf($key, $vo);
        }
        LogService::write('系统管理', '系统参数配置成功');
        $this->success('系统参数配置成功！', '');
    }

    /**
     * 文件存储配置
     */
    public function file()
    {
        $alert = [
            'type'    => 'danger',
            'title'   => '操作安全警告（默认使用本地服务存储）',
            'content' => '请根据实际情况配置存储引擎，合理做好站点下载分流。建议尽量使用云存储服务，同时保证文件访问协议与网站访问协议一致！'
        ];
        $this->title = '文件存储配置';
        $this->assign('alert', $alert);
        return $this->index();
    }

    /*
     * 短信服务配置
     */
    public function sms(){
        $alert = [
            'type'    => 'danger',
            'title'   => '操作提示（默认使用聚合短信）',
            'content' => '请根据申请的短信接口类型进行配置，如需另行开发请联系QQ:2398156504'
        ];
        $this->title = '短信服务配置';
        $this->assign('alert', $alert);
        return $this->index();
    }

    /*
     * 短信模板配置
     */
    public function sms_tpl(){
        $this->title = "短信模板设置";
        return $this->index();
    }

    /*
     * 支付配置
     */
    public function payment(){
        $this->title = "支付服务配置";
        return $this->index();
    }

}
