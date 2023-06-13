<?php

namespace App\Http\Queries;

use App\Models\Reply;
use Spatie\QueryBuilder\QueryBuilder;

class ReplyQuery extends QueryBuilder
{
    /**
     * TopicQuery constructor.
     */
    public function __construct()
    {
        parent::__construct(Reply::query());

        // allowedIncludes 方法传入可以被include的参数(参数为模型中定义的关联关系)
        $this->allowedIncludes('user', 'topic', 'topic.user');
    }
}
