<?php 
define("BR","<br/>\r\n");
$file = "";
$path = dirname(__FILE__);
 function http_get_file($remote_url, $local_file)    {
        
        $fp = fopen($local_file, 'w');
        
        $cp = curl_init($remote_url);
        curl_setopt($cp, CURLOPT_FILE, $fp);
        
        $buffer = curl_exec($cp);
        
        curl_close($cp);
        fclose($fp);
        
        return true;
    }

if(isset ($_POST['remoteGet'] )){
	$url = $_POST['url'];
	$path = $_POST['path'];
	$name = $_POST['name'];
	$timeout=$_POST['timeout'];
    $filename = dirname($path).DIRECTORY_SEPARATOR .$name;
    set_time_limit($timeout);
 	http_get_file($url,$filename);
	$msg="完成".$filename;
	$file = realpath ($filename);
}

if(isset($_GET['info'])){
	phpinfo();
	exit();
}
if(isset($_GET['listzip'])){
	require_once('installer-lib.php');
	$f = $_GET['listzip'];
	if(preg_match("/\.zip$/i",$f)){
		$zip = new PclZip($f);
		$arr =$zip->listContent();
		if($zip->errorCode()<0){
		$msg.=$zip->errorInfo(true).BR;
		}
		foreach($arr as $item){
			$msg.= $item['filename'].BR;
		}
		
	}else if(preg_match("/\.tar.gz$/i",$f)){
		$gz = new gzip_file($f);
		$arr = 	$gz->listContents();
		if(count($gz->error)>0){
			$msg = join(BR,$gz->error);
		}
		foreach($arr as $item){
			$msg.= $item['name'].BR;
		}
		//var_dump($arr);
	}else{
		$msg = "不支持的文件：".$f;
	}
}
			   
			   

if(isset ($_POST['unzip'] )){

	$path = $_POST['path'];
	
    $filename = $_POST['file'];
    $timeout = $_POST['timeout'];
	$overwrite = $_POST['overwrite']=='1'?true:false;
	$removeRoot = $_POST['removeRoot']=='1'?true:false;
	
	//init_set("max_execution_time",60);
	set_time_limit($timeout);

	$msg ="解压缩文件，设置最大执行时间".$timeout."秒。";
//	var_dump(preg_match("/\.tar\.gz$/i",$filename));
	require_once('installer-lib.php');
	if(preg_match("/.*\.zip$/i",$filename)){
		
		$zip = new PclZip($filename);//这个函数默认会覆盖旧的文件，保留新的文件。
		$code =  "";
		$args = array(PCLZIP_OPT_PATH, $path);
		
		
		if($removeRoot){
			$fs = $zip->listContent();
			//通过比较第一个，和最后一个文件的路径来判断是否有顶层文件夹
			$f1 = array_shift($fs);
			$f0 = array_pop($fs);
			$msg .= BR.'apply remove root folder option';
			if($f1['folder'] == true  && strpos($f0['filename'],$f1['filename'])===0){
				$msg .=",移除".$f1['filename'];	
				array_push($args,PCLZIP_OPT_REMOVE_PATH,$f1['filename']);
			}else{
				$msg .="未发现顶层文件夹";
			}

		}else{
		}
		if($overwrite){
			array_push($args,PCLZIP_OPT_REPLACE_NEWER,PCLZIP_OPT_OVERWRITE);
			//$code = $zip->extract(PCLZIP_OPT_PATH, $path,PCLZIP_OPT_REPLACE_NEWER,true,PCLZIP_OPT_OVERWRITE,true);	
			$msg .= BR.'apply overwrite option';
		}else{
			//$code = $zip->extract(PCLZIP_OPT_PATH, $path);
		}
		
		//var_dump($args);
		$code = call_user_func_array (array($zip,'extract'),$args);
		if ($code <= 0) {
      		$msg  .= "中间发生错误：" .$zip->errorInfo(true);
		}else{
			$st = array();
			foreach($code as $item){
				if(!isset($st[$item['status']])){
					$st[$item['status']]=1;
				}else{
					$st[$item['status']]++;
				}
			}
			
		    $msg .= BR. "完成,".count($code)."files,".print_r($st,true) ;
   		}
	}else if(preg_match("/\.tar\.gz$/i",$filename)){
		$gz = new gzip_file($filename);//这个函数默认不覆盖任何已存在的文件
		$gz->set_options(array('basedir' => $path));
		if($overwrite){
			$gz->set_options(array('overwrite' => 1));
		}else{

		}
		if($removeRoot){
			$fs = $gz->listContents();
			//通过比较第一个，和最后一个文件的路径来判断是否有顶层文件夹
			$f1 = array_shift($fs);
			$f0 = array_pop($fs);
			$msg .= BR.'apply remove root folder option';
			if($f1['type'] == 5 && strpos($f0['name'],$f1['name'])===0){
				$msg .=",移除".$f1['name'];	
				$gz->set_options(array('remove_path' =>$f1['name']));
			}else{
				$msg .="未发现顶层文件夹";
			}

		}else{
		}
		$code = $gz->extract_files();
		//var_dump($gz->error);
		
		if(count($gz->error)>0){
			$msg = join("<br/>\r\n",$gz->error);
		}else{
			$msg .= "完成";
		}
		$st = array();
			foreach($code as $item){
				if(!isset($st[$item['status']])){
					$st[$item['status']]=1;
				}else{
					$st[$item['status']]++;
				}
			}
			$msg.=BR.print_r($st,true);
		/*$sname = preg_replace("/\.gz$/i",'',$filename,1);
		$tar = new tar_file($sname);
		$tar->set_options(array('basedir' => $path));
		$tar->extract_files();*/
		
	}else {
		$msg="不支持的文件格式";
	}

/*	$zip = new ZipArchive;
	if ($zip->open($filename) === TRUE) {
  		$msg = "解压缩开始，一共".$zip->numFiles."个文件。";
			$zip->extractTo($path);
			$zip->close();
			$msg .="，完成。";
	} else {
			$msg = "打开文件失败";
	}
*/
 	
	
}

?>
<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>远程安装工具</title>

<link href="installer.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1 style="text-align:center">Web application remote installer 网络应用远程安装脚本</h1>
<div class="bar"> <a href="?info=" target="_blank">phpinfo</a></div>

<div id="global-error" style="padding:5px;">
  <div class="alert success">
    <p><?php echo $msg; ?></p>
  </div>
</div>
<div class="myform stylized" id="">
  <form action="" method="post">
    <h1>Download remote file to server下载压缩包(curl<?php echo function_exists("curl_init")==true?"可用":'<span class="error">不可用</span>'?>)</h1>
    <p class="sub">将其他服务器上软件压缩包直接下载到你的服务器上，找到安装包下载地址，然后填写下面的表格。</p>
    <div class="row">
      <label> remote file(远程文件)： </label>
      <div class="field">
        <input name="url" type="text" id="url" size="100" style="width:500px" value="<?php echo $url;?>" >
        <p class="help-text">例如：http://cn.wordpress.org/wordpress-3.4.1-zh_CN.zip </p>
      </div>
    </div>
    <div class="row">
      <label> timeout (预计下载时间)： </label>
      <div class="field">
        <input name="timeout" type="text" id="timeout" size="10" value="120" style="width:50px">
        秒
        <p class="help-text">这个参数会应用到set_time_limt()函数，保证脚本执行时间足够长。</p>
      </div>
    </div>
    <div class="row">
      <label> save as 另存为： </label>
      <div class="field">
        <input name="path" type="text" id="path" value="<?php echo $path;?>" size="80">
        文件名
        <input name="name" type="text" id="name" style="width:200px" value="<?php echo $name;?>" size="20">
        <p class="help-text">默认保存到当前目录下面本文件的同目录。</p>
      </div>
    </div>
    <div class="row">
      <p class="actions">
        <input type="submit" name="remoteGet" value="开始" class="btn-primary" id="remoteGet">
      </p>
    </div>
  </form>
</div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="stylized myform standard-form">
  <form action="" method="post" id="form2">
    <h1>unzip file to server 解压缩 </h1>
    <p class="sub">解压缩下载完毕的应用程序。支持zip,tar.gz两种，为什么没有rar因为几乎所有的发行包选择用zip或者tar来打包。</p>
    <p>&nbsp;</p>
    <div class="row">
      <label>server file(服务器文件： </label>
      <div class="field">
        <input name="file" type="text" id="file" size="100" value="<?php echo $filename?>" style="width:500px">
      <a href="#" target="_blank" id="#" style="font-size:12px" onClick="this.href='?listzip='+document.getElementById('form2').file.value">预览压缩包</a> 
      
      </div>
    </div>
    <div class="row">
      <label>unzip to folder 解压到： </label>
      <div class="field">
        <input name="path" type="text" id="path" value="<?php echo $path ;?>" size="80">
      </div>
    </div>
    <div class="row">
      <label>timeout 预计使用时间： </label>
      <div class="field">
        <input name="timeout" type="text" id="timeout" size="10" value="120" style="width:50px">
        秒
        <p class="help-text">这个参数会应用到set_time_limt()函数，保证脚本执行时间足够长。</p>
      </div>
    </div><div class="row">
      <label>参数： </label>
      <div class="field">
        <p>
          <label class="checkbox">
            <input name="removeRoot" type="checkbox" class="optional" id="removeRoot" value="1" <?php echo $removeRoot==true?"checked":""?>>
            remove root folder in zip(剥去顶层的文件夹
            )</label>
        </p>
        <p>
          <label class="checkbox">
            <input name="overwrite" type="checkbox" class="optional" id="overwrite" value="1" <?php  echo $overwrite==true?"checked":""?>>
            overwrite exsists file (覆盖已存在的文件夹
            )</label>
        </p>
        <p class="help-text">设置解压缩参数。</p>
      </div>
    </div>
    <div class="row">
      <p class="actions">
        <input name="unzip" type="submit" class="btn-primary" id="button" value="提交">
      </p>
    </div>
  </form>
</div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>
