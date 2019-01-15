<!DOCTYPE html>
<html>
  <head>
    <title>CO框架Demo2-1</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link href="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/css/demo.css" rel="stylesheet" media="screen">
    <script src="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/js/jquery-1.10.2.min.js"></script>
  </head>
  <body>
  	<h2>CO框架示例Demo2-1</h2>
  	<p>
  		此例子用于说明视图引用HTML资源的常量使用。
  	</p>
  	<p>
  		<b>APP_HTTP_ROOT</b>。可以访问到当前主题themes的根目录，常用于在视图中拼写html资源的引入根目录。
  	</p>
  	<pre>&lt;link href="&lt;?php echo <a style="color:red">APP_HTTP_ROOT</a>.$this-&gt;__themes_name;?&gt;/css/demo.css" rel="stylesheet" media="screen"&gt;</pre>
  	<hr>
  	<p>
  		<b>APP_URL_ROOT</b>。可以访问当前应用的根地址，常用于在视图或其他逻辑代码中拼写url跳转的应用根地址。
  	</p>
  	<pre>window.location.href = '&lt;?php echo <a style="color:red">APP_URL_ROOT</a>?&gt;/demo2/demo2_2';</pre>
  	<p>
  		此例子用于说明视图载入方式。在控制器Controller中使用$this->Render($view_name, $param)函数
  	</p>
  	<p>
  		其中：$view_name指需要载入的视图名称。$param指需要传入的参数。
  	</p>
  	<hr>
  	<p>
  		下面的按钮就是通过常量APP_URL_ROOT实现url跳转
  	</p>
  	<button id="btn_demo">点击按钮跳转至demo2_2</button>
  </body>
  <script>
	$(function(){
		$('#btn_demo').click(function(){
			window.location.href = '<?php echo APP_URL_ROOT?>/demo2/demo2_2';
		});
	});
  </script>
</html>