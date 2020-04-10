<?php
// +----------------------------------------------------------------------
// | Kufeisoft framework
// +----------------------------------------------------------------------
// | 版权所有 2020 西安酷飞软件有限公司
// +----------------------------------------------------------------------
// | 官方网站: https://framework.kufeisoft.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | 模块注册服务
// +----------------------------------------------------------------------
namespace kufei;

use think\middleware\SessionInit;
use think\Request;
use think\Service;

class InitService extends Service
{
  /**
   * 注册初始化服务
   */
  public function register(){
    // 加载中文语言
    $this->app->lang->load(__DIR__ . '/lang/zh-cn.php', 'zh-cn');
    $this->app->lang->load(__DIR__ . '/lang/en-us.php', 'en-us');
    // 输入变量默认过滤
    $this->app->request->filter(['trim', 'safe_replace']);
    // 判断访问模式，兼容 CLI 访问控制器
    if ($this->app->request->isCli()) {
      if (empty($_SERVER['REQUEST_URI']) && isset($_SERVER['argv'][1])) {
        $this->app->request->setPathinfo($_SERVER['argv'][1]);
      }
    } else {
      // 注册会话初始化中间键
      if ($this->app->request->request('nosession', 0) == 0) {
        $this->app->middleware->add(SessionInit::class);
      }
      // 注册访问处理中间键
      $this->app->middleware->add(function (Request $request, \Closure $next) {
        $header = [];
        if (($origin = $request->header('origin', '*')) !== '*') {
          $header['Access-Control-Allow-Origin'] = $origin;
          $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE';
          $header['Access-Control-Allow-Headers'] = 'Authorization,Content-Type,If-Match,If-Modified-Since,If-None-Match,If-Unmodified-Since,X-Requested-With';
          $header['Access-Control-Expose-Headers'] = 'User-Form-Token,User-Token,Token';
        }
      }, 'route');
    }
    // 动态加入应用函数
    $cmdRule = "{$this->app->getAppPath()}*/cmd.php";
    foreach (glob($cmdRule) as $file) includeFile($file);
  }

  /**
   * 启动服务
   */
  public function boot(){
    // 注册系统任务指令
  }
}