<!DOCTYPE html>
<html>
  <head>
    <title>CO框架Demo4-2</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link href="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/css/demo.css" rel="stylesheet" media="screen">
    <script src="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/js/jquery-1.10.2.min.js"></script>
  </head>
  <body>
  	<h2>CO框架示例Demo4-2</h2>
  	<p>
  		此例子用于说明语言包如何手动设置。
  	</p>
  	<pre>
  function demo4_2(){
		//手动设置语言
		$this->config->Set("language", "en");
		//加载语言包
		$this->GetLang('standard');
		$this->Render('demo4/demo4_2');
	}
	</pre>
	<p>
		在视图中使用$this->language['hello_text']即可显示当前语言设置的内容。
	</p>
	<pre>&lt;?php echo $this-&gt;language['hello_text']?&gt;</pre>
	<hr>
	<p>运行结果：</p>
	<pre><?php echo $this->language['hello_text']?></pre>
  </body>
</html>