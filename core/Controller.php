<?php
/**
 * 控制器基类
 *
 * @package		comnide
 * @author			B.I.T
 * @copyright		Copyright (c) 2016 - 2017.
 * @license
 * @link
 * @since				Version 1.17
 */

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * 控制器基类。系统中所有控制器对象均需要继承于此类。
 * 作为MVC模式的重要一部分，控制器发挥着连接视图(view)和模型(model)的重要作用。本框架的MVC模式中，视图和模型是不能够直接交互的。数据的交互和操
 * 作均是使用控制器调度。
 * GetModel()方法可以获取指定的模型对象。
 * Render()方法可以调用指定的视图。
 * -----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
class Controller{
	
	//控制器方法
    private $__mod='';
    
    //控制器函数
    private $__act='';
    
    //控制器内置变量，传入视图时使用
    private $__variables = array();
    
    //视图输出类对象
    private $__output;
    
    //视图文件路径
    private $__view_file_path = '';
    
    //模型对象
    protected $_models = array();
    
    //输入类对象
    public $input = null;
    
    //插件类对象
    public $plugins = null;
    
    //配置类对象
    public $config = null;
    
    //会话类对象
    public $session = null;
    
    //是否引用视图展示
    protected $_rendered = false;
    
    //视图主题名称
    private $__themes_name = '';
    
    //语言包
    public $language = array();
    
    /**
     * 构造函数
     * @param	parameter array 实例化参数
     * @return	void
     */
    function __construct($parameter){
    	$this->__mod = $parameter['mod'];
    	$this->__act = $parameter['act'];
    	
    	//初始化input对象
    	$this->input=new CO_Input();
    	
    	//加载配置
    	$this->config=new CO_Config();
    	
    	//加载插件
    	$this->plugins=new CO_Plugins();
    	
    	/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
    	 * 设置session
    	 * 应用(application)目录中的custom_config.php设置
    	* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
    	if($this->config->Get('session_start')=='1'){
    		//开启session
    		$custom_session=$this->config->Get('session_custom');
    		if($custom_session!=''){
    			//采用指定的session定制类
    			$handler = new $custom_session(array(
    					'lifetime'=>$this->config->Get('session_lifetime')
    					));
    			session_set_save_handler($handler, true);
    		}
    		session_start();
    	}
    	$this->session = new CO_Session();
    	
    	//设置主题
    	$this->SetThemes($this->config->Get('themes'));
    	
    	//初始化
    	$this->_Init();
    }   
    
    /**
     * 获取output对象
	 * @return	Output 页面输出对象
	 */
	public function GetOutput() {
		return $this->output;
	}
	
	/**
	 * 设置output对象
	 * @param	output	Output	页面输出对象
	 * @return	boolean
	 */
	public function SetOutput($output) {
		$this->output = $output;
		return true;
	}

	/**
	 * 设置当前主题
	 * @param	themes string 主题id
	 * @return	true
	 */
	public function SetThemes($themes="default"){
		$this->__themes_name=$themes;
		return true;
	}
	
	/**
	 * 获取当前主题
	 * return	string 主题id
	 */
	public function GetThemes(){
		return $this->__themes_name;
	}
	
    /**
     * 析构函数
     */
    function _destruct(){
    	
    }
    
    /**
     * 初始化函数
     */
    protected function _Init(){
    	
    }
    
    /**
     * 输出页面
     * echo出页面代码
     * @return	void
     */
    function Display() {
    	$this->output->Display();
    }

    /**
     * 渲染View页面
     * @param	view_name string 模板名，如"index", "part/header"
     * @param	variables array 变量关联数组
     * @param	return boolean 是否返回为变量（取消页面输出）
     * @return	mixed
     */
    function Render($view_name, array $variables = array(), $return = false ){
    	/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
    	 * 定位视图文件
    	* ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
    	$_exp = explode('.', $view_name);
        $count=count($_exp);
        if($count==1) $_ext='.php';
        else $_ext='.'.$_exp[$count-1];
        //根据视图主题(themes)决定使用那个视图(view)
        $this->__view_file_path = VIEW_THEMES_PATH .'/'. $this->GetThemes() . '/view/' . $view_name . $_ext;
        //如果指定模板目录下找不到该模板文件，则使用默认主题视图
        if(!file_exists($this->__view_file_path)){
        	$this->__view_file_path = VIEW_THEMES_PATH .'/'. VIEW_DEFAULT_THEMES . '/view/' . $view_name . $_ext;
        	$this->__themes_name = VIEW_DEFAULT_THEMES;
        }
        if ( !file_exists( $this->__view_file_path ) ) {
            return false;
        }        
        /* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
         * 设置传入视图的变量
        * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
        $this->Assign($variables);
        //将传入的data数组导入到当前变量中
        extract($this->__variables);
        //引入视图文件
        include($this->__view_file_path);
        /* ------------------------------------------------------------------------------------------------------------------------------------------------------------------
         * 读取视图内容至输出对象
        * ------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
        ob_start();
        $buffer = ob_get_contents ();
        @ob_end_clean ();
        //输出
        if ($return === true) {
        	return $buffer;
        }else{
        	$this->output->AppendOutputStream($buffer);
        	$this->_rendered = true;
        	return true;
        }
    }
    
    /**
     * 预设页面渲染变量
     *
     * @param	variables array
     * @param	clear boolean 是否先清空
     * @return	boolean
     */
    function Assign($variables, $clear = false) {
    	static $count = 1;
    	if ($clear) {
    		$this->__variables = array ();
    	}
    	if (is_array($variables) && !empty($variables)) {
    		$this->__variables = array_merge($this->__variables, $variables);
    	}
    	return true;
    }

    /**
     * 加载语言包
     * @param	mod string 功能模块
     * @return	$this
     */
    function GetLang($mod=""){
    	$lang=array();
    	if($mod=="") return false;
    	
    	@include_once APP_LANG_PATH.'/'.$this->config->Get("language").'/'.$mod.'_lang.php';
    	//var_dump($this->config->Get("language"));die;
    	//过滤html可运行脚本
    	$lang_filter=array();
    	foreach ($lang as $key => $_lang){
    		@$lang_filter[$key]=htmlspecialchars($_lang);
    	}
    	$this->language=array_merge($this->language, $lang_filter);
    	unset($lang_filter);
    	unset($lang);
    	
    	return $this;
    }
    
    /**
     * 加载模型对象  
     */
    function GetModel($model_name, $param=array()){
    	//转化成匹配的模型文件
    	$class_name=str_camelize($model_name).'Model';
    	$class_file=MODEL_PATH."/".$class_name.'.php';
    	if(is_object($this->_models[$model_name])) return $this->_models[$model_name];
    	elseif(file_exists($class_file)){
    		//引入model文件，并生成对象
    		include_once $class_file;
    		$model_object = new $class_name($this, $param);
    		$this->_models[$model_name] = $model_object;
    		unset($model_object);
    		return $this->_models[$model_name];
    	}else return null;
    }
}
?>