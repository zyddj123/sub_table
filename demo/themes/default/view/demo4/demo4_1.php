<!DOCTYPE html>
<html>
  <head>
    <title>CO框架Demo4-1</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link href="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/css/demo.css" rel="stylesheet" media="screen">
    <script src="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/js/jquery-1.10.2.min.js"></script>
  </head>
  <body>
  	<h2>CO框架示例Demo4-1</h2>
  	<p>
  		此例子用于说明语言包如何使用。
  	</p>
  	<p>
  		在控制器内使用成员函数GetLang($lang_cfg)加载当前语言设置下的语言包。常用方式在控制器的_init()函数中加载。
  	</p>
  	<pre>
  	protected function _init(){
		//加载语言包
		$this-&gt;GetLang('standard');
		return true;
	}
	</pre>
	<p>
		系统默认使用简体中文（zh-cn）语言设置。在视图中使用$this->language['hello_text']即可显示当前语言设置的内容。
	</p>
	<pre>&lt;?php echo $this-&gt;language['hello_text']?&gt;</pre>
	<hr>
	<p>运行结果：</p>
	<pre><?php echo $this->language['hello_text']?></pre>
  </body>
</html>