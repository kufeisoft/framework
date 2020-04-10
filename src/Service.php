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
// | 自定义服务基类
// +----------------------------------------------------------------------

namespace kufei;

use think\App;
use think\Container;

abstract class Service{
  /**
   * 应用实例
   * @var App
   */
  protected $app;

  /**
   * 构造方法
   * @param App $app
   */
  public function __construct(App $app){
    $this->app = $app;
    $this->initialize();
  }

  /**
   * 模仿THINKPHP初始化
   * @return $this
   */
  protected function initialize(){
    return $this;
  }

  /**
   * 静态实例对象
   * @param array $args
   * @return static
   */
  public static function instance(...$args){
    return Container::getInstance()->make(static::class, $args);
  }
}