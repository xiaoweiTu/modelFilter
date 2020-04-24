# modelFilter

习惯了laravel的[tucker-eric/eloquentfilter](https://github.com/Tucker-Eric/EloquentFilter)查询条件写法，
转到hyperf发现没有相关的包,所以这个包出现了。

支持hyperf1.1

# 如何使用

```
 安装最新版
 
 composer require xiaowei/model_filter --prefer-dist

 创建目录
 app/ModelFilters
 
 在模型中使用trait
 
 use Xiaowei\ModelFilter\Filterable;
 class Tag extends Model
 {
    use Filterable;
 }
 
 创建filter类
 
 在app/ModelFilters中创建模型名+filter的文件
 
use Xiaowei\ModelFilter\ModelFilter;
class TagFilter extends ModelFilter
{
    public function id($value)
    {
        $this->where('id',$value);
    }

    public function name($value)
    {
        $this->where('name','like',$value.'%');
    }

    public function order($value)
    {
        $this->where('order','>=',$value);
    }
}

```

## 注意

1. 目前只支持Hyperf\Database\Model\Builder中的查询方法
2. 参数需要为下划线分割的名称 如 product_id 那么ModelFilter中对应的方法就为productId


