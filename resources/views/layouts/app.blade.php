<!DOCTYPE html>
{{--获取config/app.php中的locale选项--}}
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{--继承此模板的页面 LaraBBS为title默认值--}}
  <title>@yield('title', 'LaraBBS') - {{ setting('site_name', 'Laravel 进阶教程') }}</title>
  <meta name="description" content="@yield('description', setting('seo_description', 'LaraBBS 爱好者社区。'))" />
  <meta name="keywords" content="@yield('keyword', setting('seo_keyword', 'LaraBBS,社区,论坛,开发者论坛'))" />
  <!-- Styles -->
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">

  @yield('styles')

</head>

<body>
{{--自定义的辅助方法--}}
<div id="app" class="{{ route_class() }}-page">
  {{--加载顶部导航区块的子模块--}}
  @include('layouts._header')

  <div class="container">

    @include('shared._messages')
   {{--占位符声明 允许继承此模板的页面注入内容--}}
    @yield('content')

  </div>
<!-- 加载页面尾部导航区块的子模板 -->
  @include('layouts._footer')
</div>

@if (app()->islocal())
  @include(('sudosu::user-selector'))
@endif

<!-- Scripts -->
<script src="{{ mix('js/app.js') }}"></script>

@yield('scripts')

</body>

</html>
