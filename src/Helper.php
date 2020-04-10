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
// | 助手组件
// +----------------------------------------------------------------------

namespace kufei;

use think\App;
use think\Container;

abstract class Helper
{
  /** 当前应用 */
  public $app;

  /** 当前控制器 */
  public $controller;

  /**
   * 构造方法
   *
   * @param App $app 
   * @param Controller $controller
   */
  public function __construct(App $app, Controller $controller){
    $this->app = $app;
    $this->controller = $controller;
  }

  /**
   * 实例对象的反射
   *
   * @param array ...$args
   * @return Helper
   */
  public static function instance(...$args): Helper{
    return Container::getInstance()->invokeClass(static::class, $args);
  }
}