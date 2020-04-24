<?php
/**
 *
 * User: xiaowei<13177839316@163.com>
 * Date: 2020/4/24
 * Time: 10:41
 */

namespace Hyperf\ModelFilter;


use Hyperf\Database\Model\Builder;

trait Filterable
{

    /**
     * @param Builder $query
     * @param array   $params
     * @param null    $filter
     * 注册钩子函数
     * @return mixed
     */
    public function scopeFilter(Builder $query,array $params = [], $filter = null)
    {
        if ($filter === null) {
            $filter = $this->getModelFilterClass();
        }

        $modelFilter = new $filter($query, $params);

        return $modelFilter->handle();
    }

    /**
     * @param null $filter
     * 找到对应的filter文件 暂时写死在 App\ModelFilters下,后续可配置
     * @return string|null
     */
    public function provideFilter($filter = null)
    {
        if ($filter === null) {
            $filter = 'App\\ModelFilters\\'.class_basename($this).'Filter';
        }

        return $filter;
    }

    /**
     * @return string|null 获取filter文件，可由当前模型重写modelFilter方法提供
     */
    public function getModelFilterClass()
    {
        return method_exists($this, 'modelFilter') ? $this->modelFilter() : $this->provideFilter();
    }

}
