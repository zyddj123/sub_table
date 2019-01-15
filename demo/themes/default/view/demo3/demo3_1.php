<!DOCTYPE html>
<html>
  <head>
    <title>CO框架Demo3-1</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link href="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/css/demo.css" rel="stylesheet" media="screen">
    <script src="<?php echo APP_HTTP_ROOT.$this->GetThemes();?>/js/jquery-1.10.2.min.js"></script>
  </head>
  <body>
  	<h2>CO框架示例Demo3-1</h2>
  	<p>
  		此例子用于说明如何配置应用数据库。
  	</p>
  	<p>
  		每个应用目录都有独立的数据库配置文件。存放于应用根目录下的config目录，配置文件名为db_config.php。数据库配置内容如下：
  	</p>
  	<pre>
  	$db_config['default'] = array(
  		'type' =&gt; 'mysql',
		'host' =&gt; 'localhost',
		'port' =&gt; '3306',
		'user' =&gt; '',
		'password' =&gt; '',
		'charset' =&gt; 'utf-8',
		'db_name' =&gt; ''
	);
	</pre>
	<p>
		数据库配置使用数组key-value方式，应用可以拥有多个数据库配置。key表示数据库配置id，value内容表示具体的配置。
	</p>
	<p>“type”指数据库类型，现阶段支持mysql。</p>
	<p>“host”指数据库地址。</p>
	<p>“port”指数据库端口，默认3306。</p>
	<p>“user”指数据库连接用户名。</p>
	<p>“password”指数据库连接密码。</p>
	<p>“charset”指数据库字符集。</p>
	<p>“db_name”指需要连接数据库的实力。</p>
  </body>
</html>