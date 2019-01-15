<!DOCTYPE html>
<html>
  <head>
    <title>CO框架Demo2-2</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link href="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/css/demo.css" rel="stylesheet" media="screen">
    <script src="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/js/jquery-1.10.2.min.js"></script>
  </head>
  <body>
  	<h2>CO框架示例Demo2-2</h2>
  	<p>
  		此例子用于说明如何引入视图，并且向视图内传递参数。
  	</p>
  	<p>
  		视图View只能由控制器Controller调用。使用成员函数$this->Render($view_name, $param)。
  	</p>
  	<p>
  		参数$view_name指需要调用的视图名称，如果视图包含多层目录，即可用带目录层次作为输入。
  	</p>
	<p>
		参数$param指需要传入视图的参数数组。此参数是可选的。参数形如array(‘param_name’=>$param_val)传入视图后，在视图中可使用$param_name变量即可引用。
	</p>
	<hr>
	<p>
		控制器调用视图，并且传递参数，代码如下：
	</p>
	<pre>$this-&gt;Render(
		'demo2/demo2_2',
		array(
			'value_1'=&gt;$this->__val,
			'value_2'=&gt;'world'
			)
		);</pre>
	<p>
		视图接收控制器传入的参数并显示，代码如下：
	</p>
	<pre>&lt;p&gt;&lt;?php echo $value_1?&gt; &lt;?php echo $value_2?&gt;&lt;/p&gt;</pre>
	<p>运行结果：</p>
  	<pre><?php echo $value_1?> <?php echo $value_2?></pre>
  </body>
</html>