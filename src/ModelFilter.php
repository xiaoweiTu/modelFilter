<?php
/**
 *
 * User: xiaowei<13177839316@163.com>
 * Date: 2020/4/24
 * Time: 10:43
 */

namespace Xiaowei\ModelFilter;

use Hyperf\Database\Model\Builder;

abstract  class ModelFilter
{
    /**
     * 屏蔽相关字段的搜索
     * @var array
     */
    protected $blacklist = [];

    /**
     * filter的参数
     *
     * @var array
     */
    protected $input;

    /**
     * @var Builder
     */
    protected $query;


    public function __construct($query, array $input = [])
    {
        $this->input = $this->removeEmptyInput($input);
        $this->query = $query;
    }


    /**
     * @return Builder
     */
    public function handle()
    {
        // Run input filters
        $this->filterInput();

        return $this->query;
    }

    /**
     * @param $method
     * @param $args
     * 支持直接 $this->where 的方式进行查询
     * @return mixed|ModelFilter
     */
    public function __call($method, $args)
    {
        $resp = call_user_func_array([$this->query, $method], $args);

        // Only return $this if query builder is returned
        // We don't want to make actions to the builder unreachable
        return $resp instanceof Builder ? $this : $resp;
    }

    /**
     * 过滤参数
     */
    public function filterInput()
    {
        foreach ($this->input as $key => $val) {
            // 获取本文件实例方法
            $method = $this->getFilterMethod($key);
            // 检验是否可用
            if ($this->methodIsCallable($method)) {
                $this->{$method}($val);
            }
        }
    }

    /**
     * @param $methodName
     * 将参数变成小驼峰形式
     * @return string
     */
    public function camelName($methodName)
    {
        $nameArr = explode('_',$methodName);

        foreach($nameArr as $key=>$name ) {
            if ($key > 0) {
                $nameArr[$key] = ucfirst($name);
            }
        }

        return implode('',$nameArr);
    }

    /**
     * @param $key
     * @return string
     */
    public function getFilterMethod($key)
    {
        // 去除.
        $methodName = str_replace('.', '', $key);

        // 变成小驼峰
        return  $this->camelName($methodName);
    }

    /**
     * @param $method
     * 检查是否可以调用,排除黑名单和自身
     * @return bool
     */
    public function methodIsCallable($method)
    {
        return ! $this->methodIsBlacklisted($method) &&
            method_exists($this, $method) &&
            ! method_exists(ModelFilter::class, $method);
    }


    /**
     * @param $method
     * @return bool
     */
    public function methodIsBlacklisted($method)
    {
        return in_array($method, $this->blacklist, true);
    }



    /**
     * 移除空字符
     *
     * @param array $input
     * @return array
     */
    public function removeEmptyInput($input)
    {
        $filterableInput = [];

        foreach ($input as $key => $val) {
            if ($val !== '' && $val !== null) {
                $filterableInput[$key] = $val;
            }
        }

        return $filterableInput;
    }



}
