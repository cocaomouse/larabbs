<?php

namespace App\Http\Queries;

use App\Models\Topic;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TopicQuery extends QueryBuilder
{
    /**
     * TopicQuery constructor.
     */
    public function __construct()
    {
        parent::__construct(Topic::query());

        // allowedIncludes 方法传入可以被include的参数(参数为模型中定义的关联关系)
        // allowedFilters 方法传入可以被搜索的条件,'title'代表对title字段传入的值进行模糊搜索
        // allowedFilter::exact() 代表对括号中字段传入的值进行精确搜索
        // allowedFilter::scope() 代表使用括号中传入的查询作用域进行搜索(查询作用域已在模型中定义)
        $this->allowedIncludes('user', 'category', 'topReplies', 'topReplies.user')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ]);
    }
}
