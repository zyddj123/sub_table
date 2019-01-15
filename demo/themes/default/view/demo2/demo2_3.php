<!DOCTYPE html>
<html>
  <head>
    <title>CO框架Demo2-3</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link href="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/css/demo.css" rel="stylesheet" media="screen">
    <script src="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/js/jquery-1.10.2.min.js"></script>
  </head>
  <body>
  	<h2>CO框架示例Demo2-3</h2>
  	<p>
  		此例子用于说明视图多主题特性。
  	</p>
  	<p>
  		我们预先编写第二个主题，名称叫“red”。视图主题red于默认主题default逻辑独立，并且也包含一套完整的视图结构目录。
  	</p>
  	<p>
  		此例在保证控制器调用视图名称和传递参数均不变化的情况下，通过使用随机数实现视图主题在“red”和“default”之间切换的方式，观察前台页面的效果。
  	</p>
	<p>
		控制器代码如下：
	</p>
	<hr>
	<p>
		运行结果：
	</p>
	<pre><p>这是主题"default"</p><p>当前时间：<?php echo $time;?></p><font>hello word</font>
	</pre>
	<button onclick="window.location.reload();">刷新看看</button>
  </body>
</html>