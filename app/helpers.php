<?php

//将当前请求的路由名称转换为CSS类名称
function route_class()
{
    return str_replace('.','-',Route::currentRouteName());
}

function category_nav_active($category_id)
{
    return active_class((if_route('categories.show') && if_route_param('category',$category_id)));
}
