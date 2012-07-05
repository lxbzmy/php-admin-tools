<?php 


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
  $filename = $path.'/'.$name;
  set_time_limit($timeout);
 	http_get_file($url,$filename);
	$msg="完成".$filename;
}

if(isset($_GET['info'])){
	phpinfo();
	exit();
}
			   
			   

if(isset ($_POST['unzip'] )){

	$path = $_POST['path'];
	
  $filename = $_POST['file'];
  $timeout = $_POST['timeout'];
	//init_set("max_execution_time",60);
	set_time_limit($timeout);

	$msg ="解压缩文件，设置最大执行时间".$timeout."秒。";
//	var_dump(preg_match("/\.tar\.gz$/i",$filename));
	require_once('installer-lib.php');
	if(preg_match("/.*\.zip$/i",$filename)){
		
		$zip = new PclZip($filename);
		$code = $zip->extract(PCLZIP_OPT_PATH, $path);
		if ($code  == 0) {
		$msg  .= "中间发生错误：" .$archive->errorInfo(true);
		}else{
		$msg .= "<br/>\r\n完成:一共 ".count($code)."个文件";
   		}
	}else if(preg_match("/\.tar\.gz$/i",$filename)){
		$gz = new gzip_file($filename);
		$gz->set_options(array('basedir' => $path));
		$gz->extract_files();
		//var_dump($gz->error);
		if(count($gz->error)>0){
			$msg = join('<br/>\r\n',$gz->error);
		}else{
			$msg .= "完成";
		}
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
<style type="text/css">
body {
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
	font-size:14px;
}
p, h1, form, button {
	border:0;
	margin:0;
	padding:0;
}
.spacer {
	clear:both;
	height:1px;
}
/* ----------- My Form ----------- */
.myform {
	margin:0 auto;
	padding:14px;
}
/* ----------- stylized ----------- */
.stylized {
	border:solid 2px #b7ddf2;
	background:#ebf4fb;
}
.stylized h1 {
	font-size:18px;
	font-weight:bold;
	margin-bottom:8px;
}
.stylized p.sub {
	font-size:12px;
	color:#666666;
	margin-bottom:20px;
	border-bottom:solid 1px #b7ddf2;
	padding-bottom:10px;
}
.stylized .small {
	color:#666666;
	display:block;
	font-size:11px;
	font-weight:normal;
	text-align:right;
	width:200px;
}
/*.stylized label {
	display:block;
	font-weight:bold;
	text-align:right;
	width:200px;
	float:left;
}

.stylized input {
	float:left;
	font-size:12px;
	padding:4px 2px;
	border:solid 1px #aacfe4;
	width:200px;
	margin:2px 0 20px 10px;
}*/
.stylized button {
	clear:both;
	margin-left:150px;
	width:125px;
	height:31px;
	background:#666666 url(img/button.png) no-repeat;
	text-align:center;
	line-height:31px;
	color:#FFFFFF;
	font-size:11px;
	font-weight:bold;
}
.row label, .multi-field h3 {
	float: left;
	width: 192px;
	text-align: right;
	font-weight: bold;
	padding-right: 6px;
	clear: left;
}
.accessible label, .accessible-label {
	position:absolute;
	top:-999em;
	left:-999em;
*clear:none !important
}
.default-text, .help-text, div.optional {
	color:#666
}
input, textarea {
	line-height:1.2 !important;
	font-size:100%
}
textarea {
	padding:4px
}
.help-text {
	margin:4px 0;
	padding:0;
	font-size:13px
}
div.optional {
	font-weight:normal
}
.required {
	font-size:110%;
	margin-top:20px
}
.row label em, .required em, .descriptions label.description-label em, .applicant-routing em, .posted-by h3 em {
	color:#C00;
	font-size:120%;
	font-weight:bold;
	padding-right:3px
}
.row {
	margin:0 0 18px 0;
	overflow:hidden;
	width:100%;
	font-size:130%;
	clear: both;
}
.row label, .multi-field h3 {
	float:left;
	width:150px;
	text-align:right;
	font-weight:bold;
	padding-right:6px;
	clear:left;
	font-size: 14px;
}
.multi-field h3 {
	margin:3px 0 0
}
.row.text label {
	margin:5px 0 0
}
.row .field {
	float:left;
	clear:right;
	margin-bottom:3px
}
.row .field .field {
	font-size:100%
}
.row .field select {
	width:320px;
	font-size:100%
}
.en .row .field select {
	width:370px
}
.row .field input {
	width:500px
}
.row .field .field-text {
	color:#666;
	margin-top:4px
}
.field-text-hover {
	background-color:#FFF5CC !important;
	cursor:pointer;
	cursor:hand
}
.row .location-field {
	margin-top:8px
}
.row .location-field a {
	font-size:85%
}
.job-data textarea {
	resize:none
}
#page-title {
	display:none
}
fieldset.job-details legend {
	display:none
}
#post-job-form p.error {
	font-weight:bold;
	color:#900
}
.actions {
	clear: both;
	padding-top: 10px;
	border-top: solid 1px #b7ddf2;
	margin-top: 20px;
}
.multi-select .add {
font-size: 11px;
padding-left: 5px;
}
a:visited, #footer a:visited {
color: 
#069;
}
a:hover {
text-decoration: underline;
}
a {
text-decoration: none;
color: 
#069;
outline: none;
}
.btn-primary, .btn-new-primary, .btn-secondary, .btn-tertiary, .btn-ternary, .btn-quaternary, .btn-action {
	border-width:1px;
	border-style:solid;
	-moz-border-radius:3px;
	-webkit-border-radius:3px;
	border-radius:3px;
	cursor:pointer;
	font-size:16px;
	line-height:1.7;
	margin:0;
	overflow:visible;
	padding:5px 10px 5px;
*padding:2px 10px 1px;
	text-decoration:none !important;
	vertical-align:top;
	width:auto
}
.btn-primary.mini, .btn-new-primary.mini, .btn-secondary.mini, .btn-new-secondary.mini, .btn-tertiary.mini, .btn-ternary.mini, .btn-quaternary.mini, .btn-action.mini {
	font-size:11px
}
a.btn-primary, a.btn-new-primary, a.btn-secondary, a.btn-new-secondary, a.btn-tertiary, a.btn-ternary, a.btn-quaternary, a.btn-action {
	display:inline-block !important;
*padding:3px 10px
}
a.btn-primary:hover, a.btn-new-primary:hover, a.btn-secondary:hover, a.btn-new-secondary:hover, a.btn-tertiary:hover, a.btn-ternary:hover, a.btn-quaternary:hover, a.btn-action:hover {
	text-decoration:none !important
}
input[disabled], .disabled {
	cursor:default
}
.toggle-btn {
	display:inline-block !important;
	margin-top:10px;
	width:15px;
	height:3px
}
.btn-primary, .btn-menu-open.btn-primary, .btn-split.btn-primary:hover {
	background:#0571A6;
	background:-moz-linear-gradient(top, #73aec9 0, #73aec9 1px, #338ab0 1px, #0571a6 100%);
	background:-o-linear-gradient(top, #73aec9 0, #73aec9 1px, #338ab0 1px, #0571a6 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #73aec9), color-stop(5%, #73aec9), color-stop(5%, #338ab0), color-stop(100%, #0571a6));
	background:linear-gradient(top, #73aec9 0%, #73aec9 1px, #338ab0 1px, #0571a6 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#338AB0', endColorstr='#0571A6', GradientType=0 );
	border-color:#045a8b;
	color:#fff !important
}
.btn-primary:hover, .btn-split-toggle-hover.btn-primary:hover {
	background:#04608E;
	background:-moz-linear-gradient(top, #69a0b6 0, #69a0b6 1px, #277696 1px, #04608e 100%);
	background:-o-linear-gradient(top, #69a0b6 0, #69a0b6 1px, #277696 1px, #04608e 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #69a0b6), color-stop(5%, #69a0b6), color-stop(5%, #277696), color-stop(100%, #04608e));
	background:linear-gradient(top, #69a0b6 0%, #69a0b6 1px, #277696 1px, #04608e 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#277696', endColorstr='#04608E', GradientType=0 );
	border-color:#004a73
}
.btn-split-toggle-hover .btn-primary {
	border-right-color:#004a73
}
.btn-primary:active, .btn-split-toggle-hover.btn-primary:active {
	background:#267696;
	background:-moz-linear-gradient(top, #025f8d 0, #267696 100%);
	background:-o-linear-gradient(top, #025f8d 0, #267696 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #025f8d), color-stop(100%, #267696));
	background:linear-gradient(top, #025f8d 0%, #267696 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#025F8D', endColorstr='#267696', GradientType=0 );
	border-color:#004a73
}
.btn-primary[disabled], .btn-primary.disabled {
	background:#82B8D3;
	background:-moz-linear-gradient(top, #b9d6e4 0, #b9d6e4 1px, #99c4d7 1px, #82b8d2 100%);
	background:-o-linear-gradient(top, #b9d6e4 0, #b9d6e4 1px, #99c4d7 1px, #82b8d2 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #b9d6e4), color-stop(5%, #b9d6e4), color-stop(5%, #99c4d7), color-stop(100%, #82b8d2));
	background:linear-gradient(top, #b9d6e4 0%, #b9d6e4 1px, #99c4d7 1px, #82b8d2 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#99C4D7', endColorstr='#82B8D2', GradientType=0 );
	border-color:#81acc5;
	color:#ddebf2 !important
}
.btn-primary .toggle-btn {
	background-position:4px -1925px
}
.btn-ternary, .btn-menu-open.btn-ternary, .btn-split.btn-ternary:hover {
	background:#CDE5F0;
	background:-moz-linear-gradient(top, #eef7fa 0, #eef7fa 1px, #dcedf5 1px, #cde5f0 100%);
	background:-o-linear-gradient(top, #eef7fa 0, #eef7fa 1px, #dcedf5 1px, #cde5f0 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #eef7fa), color-stop(5%, #eef7fa), color-stop(5%, #dcedf5), color-stop(100%, #cde5f0));
	background:linear-gradient(top, #eef7fa 0%, #eef7fa 1px, #dcedf5 1px, #cde5f0 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#DCEDF5', endColorstr='#CDE5F0', GradientType=0 );
	border-color:#a3cfe4;
	color:#069 !important
}
.btn-ternary:hover, .btn-split-toggle-hover.btn-ternary:hover {
	background:#AED6E9;
	background:-moz-linear-gradient(top, #e2f2f9 0, #e2f2f9 1px, #c3e5f2 1px, #aed6e9 100%);
	background:-o-linear-gradient(top, #e2f2f9 0, #e2f2f9 1px, #c3e5f2 1px, #aed6e9 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #e2f2f9), color-stop(5%, #e2f2f9), color-stop(5%, #c3e5f2), color-stop(100%, #aed6e9));
	background:linear-gradient(top, #e2f2f9 0%, #e2f2f9 1px, #c3e5f2 1px, #aed6e9 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#C3E5F2', endColorstr='#AED6E9', GradientType=0 );
	border-color:#64b0d4
}
.btn-split-toggle-hover .btn-ternary {
	border-right-color:#64b0d4
}
.btn-ternary:active, .btn-split-toggle-hover.btn-ternary:active {
	background:#DCEDF5;
	background:-moz-linear-gradient(top, #b4d8e9 0, #dcedf5 100%);
	background:-o-linear-gradient(top, #b4d8e9 0, #dcedf5 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #b4d8e9), color-stop(100%, #dcedf5));
	background:linear-gradient(top, #b4d8e9 0%, #dcedf5 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#B4D8E9', endColorstr='#DCEDF5', GradientType=0 );
	border-color:#64b0d4
}
.btn-ternary[disabled], .btn-ternary.disabled {
	background:#E6F2F7;
	background:-moz-linear-gradient(top, #f6fbfc 0, #f6fbfc 1px, #edf6fa 1px, #e6f2f7 100%);
	background:-o-linear-gradient(top, #f6fbfc 0, #f6fbfc 1px, #edf6fa 1px, #e6f2f7 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #f6fbfc), color-stop(5%, #f6fbfc), color-stop(5%, #edf6fa), color-stop(100%, #e6f2f7));
	background:linear-gradient(top, #f6fbfc 0%, #f6fbfc 1px, #edf6fa 1px, #e6f2f7 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#EDF6FA', endColorstr='#E6F2F7', GradientType=0 );
	border-color:#d1e7f1;
	color:#81b4c3 !important
}
.btn-ternary .toggle-btn {
	background-position:4px -1768px
}
.btn-quaternary, .btn-menu-open.btn-quaternary, .btn-split.btn-quaternary:hover {
	background:#ECECEC;
	background:-moz-linear-gradient(top, #fff 0, #ececec 100%);
	background:-o-linear-gradient(top, #fff 0, #ececec 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #fff), color-stop(100%, #ececec));
	background:linear-gradient(top, #ffffff 0%, #ececec 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#ECECEC', GradientType=0 );
	border-color:#ccc;
	color:#585858 !important
}
.btn-quaternary:hover, .btn-split-toggle-hover.btn-quaternary:hover {
	background:#D7D7D7;
	background:-moz-linear-gradient(top, #f0f0f0 0, #d7d7d7 100%);
	background:-o-linear-gradient(top, #f0f0f0 0, #d7d7d7 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #f0f0f0), color-stop(100%, #d7d7d7));
	background:linear-gradient(top, #f0f0f0 0%, #d7d7d7 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#F0F0F0', endColorstr='#D7D7D7', GradientType=0 );
	border-color:#b3b3b3
}
.btn-quaternary-toggle-hover .btn-ternary {
	border-right-color:#b3b3b3
}
.btn-quaternary:active, .btn-split-toggle-hover.btn-quaternary:active {
	background:#F7F7F7;
	background:-moz-linear-gradient(top, #dfdfdf 0, #f7f7f7 100%);
	background:-o-linear-gradient(top, #dfdfdf 0, #f7f7f7 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #dfdfdf), color-stop(100%, #f7f7f7));
	background:linear-gradient(top, #dfdfdf 0%, #f7f7f7 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#DFDFDF', endColorstr='#F7F7F7', GradientType=0 );
	border-color:#b3b3b3
}
.btn-quaternary[disabled], .btn-quaternary.disabled {
	background:#F5F5F5;
	background:-moz-linear-gradient(top, #fff 0, #f5f5f5 100%);
	background:-o-linear-gradient(top, #fff 0, #f5f5f5 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #fff), color-stop(100%, #f5f5f5));
	background:linear-gradient(top, #ffffff 0%, #f5f5f5 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#F5F5F5', GradientType=0 );
	color:#BDBDBD !important
}
.btn-quaternary .toggle-btn {
	background-position:4px -1609px
}
.btn-secondary, .btn-menu-open.btn-secondary:hover {
	background:#CECECE;
	background:-moz-linear-gradient(top, #f2f2f2 0, #f2f2f2 1px, #e4e4e4 1px, #cecece 100%);
	background:-o-linear-gradient(top, #f2f2f2 0, #f2f2f2 1px, #e4e4e4 1px, #cecece 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #f2f2f2), color-stop(5%, #f2f2f2), color-stop(5%, #e4e4e4), color-stop(100%, #cecece));
	background:linear-gradient(top, #f2f2f2 0%, #f2f2f2 1px, #e4e4e4 1px, #cecece 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#E4E4E4', endColorstr='#CECECE', GradientType=0 );
	border-color:#999;
	color:#666 !important
}
.btn-secondary:hover {
	background:#B7B7B7;
	background:-moz-linear-gradient(top, #e4e4e4 0, #e4e4e4 1px, #c8c8c8 1px, #b7b7b7 100%);
	background:-o-linear-gradient(top, #e4e4e4 0, #e4e4e4 1px, #c8c8c8 1px, #b7b7b7 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #e4e4e4), color-stop(5%, #e4e4e4), color-stop(5%, #c8c8c8), color-stop(100%, #b7b7b7));
	background:linear-gradient(top, #e4e4e4 0%, #e4e4e4 1px, #c8c8c8 1px, #b7b7b7 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#C8C8C8', endColorstr='#B7B7B7', GradientType=0 )
}
.btn-secondary:active, .btn-split-toggle-hover.btn-secondary:active {
	background:#C9C9C9;
	background:-moz-linear-gradient(top, #b6b6b6 0, #c9c9c9 100%);
	background:-o-linear-gradient(top, #b6b6b6 0, #c9c9c9 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #b6b6b6), color-stop(100%, #c9c9c9));
	background:linear-gradient(top, #b6b6b6 0%, #c9c9c9 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#B6B6B6', endColorstr='#C9C9C9', GradientType=0 )
}
.btn-secondary[disabled], .btn-secondary.disabled {
	background:#E6E6E6;
	background:-moz-linear-gradient(top, #f8f8f8 0, #f8f8f8 1px, #f1f1f1 1px, #e6e6e6 100%);
	background:-o-linear-gradient(top, #f8f8f8 0, #f8f8f8 1px, #f1f1f1 1px, #e6e6e6 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #f8f8f8), color-stop(5%, #f8f8f8), color-stop(5%, #f1f1f1), color-stop(100%, #e6e6e6));
	background:linear-gradient(top, #f8f8f8 0%, #f8f8f8 1px, #f1f1f1 1px, #e6e6e6 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#F1F1F1', endColorstr='#E6E6E6', GradientType=0 );
	border-color:#ccc;
	color:#b0b0b0 !important
}
.btn-action {
	background:#FFCF0B;
	background:-moz-linear-gradient(top, #fff5bd 0, #fff5bd 1px, #ffe976 1px, #ffcf0b 100%);
	background:-o-linear-gradient(top, #fff5bd 0, #fff5bd 1px, #ffe976 1px, #ffcf0b 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #fff5bd), color-stop(5%, #fff5bd), color-stop(5%, #ffe976), color-stop(100%, #ffcf0b));
	background:linear-gradient(top, #fff5bd 0%, #fff5bd 1px, #ffe976 1px, #ffcf0b 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFE976', endColorstr='#FFCF0B', GradientType=0 );
	border-color:#e8b463;
	color:#333 !important;
	font-weight:bold
}
.btn-action:hover {
	background:#F6C408;
	background:-moz-linear-gradient(top, #faeeae 0, #faeeae 1px, #f5db59 1px, #f6c408 100%);
	background:-o-linear-gradient(top, #faeeae 0, #faeeae 1px, #f5db59 1px, #f6c408 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #faeeae), color-stop(5%, #faeeae), color-stop(5%, #f5db59), color-stop(100%, #f6c408));
	background:linear-gradient(top, #faeeae 0%, #faeeae 1px, #f5db59 1px, #f6c408 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#F5DB59', endColorstr='#F6C408', GradientType=0 )
}
.btn-action:active {
	background:#F5DD61;
	background:-moz-linear-gradient(top, #f6c202 0, #f5dd61 100%);
	background:-o-linear-gradient(top, #f6c202 0, #f5dd61 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #f6c202), color-stop(100%, #f5dd61));
	background:linear-gradient(top, #f6c202 0%, #f5dd61 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#F6C202', endColorstr='#F5DD61', GradientType=0 )
}
.btn-action[disabled], .btn-action.disabled {
	background:#FFE785;
	background:-moz-linear-gradient(top, #fffade 0, #fffade 1px, #fff4ba 1px, #ffe785 100%);
	background:-o-linear-gradient(top, #fffade 0, #fffade 1px, #fff4ba 1px, #ffe785 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #fffade), color-stop(5%, #fffade), color-stop(5%, #fff4ba), color-stop(100%, #ffe785));
	background:linear-gradient(top, #fffade 0%, #fffade 1px, #fff4ba 1px, #ffe785 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFF4BA', endColorstr='#FFE785', GradientType=0 );
	border-color:#f3d9b1;
	color:#a4996d !important
}
.btn-tertiary {
	background:#69A219;
	background:-moz-linear-gradient(top, #afd47b 0, #afd47b 1px, #8bc03f 1px, #69a219 100%);
	background:-o-linear-gradient(top, #afd47b 0, #afd47b 1px, #8bc03f 1px, #69a219 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #afd47b), color-stop(5%, #afd47b), color-stop(5%, #8bc03f), color-stop(100%, #69a219));
	background:linear-gradient(top, #afd47b 0%, #afd47b 1px, #8bc03f 1px, #69a219 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#AFD47B', endColorstr='#69A219', GradientType=0 );
	border-color:#693;
	color:#fff !important;
	font-weight:bold;
	white-space:nowrap
}
a.action, span.action {
	display:block;
	float:left;
	margin:0 10px 0 0;
	padding:0;
	text-decoration:none !important;
	color:#000 !important;
	cursor:pointer;
	text-align:center
}
a.action span, span.action span {
	display:block;
	min-height:16px;
	padding:3px 20px !important;
	white-space:normal !important
}
a.action.secondary, span.action.secondary {
	border-color:#ccc
}
div.optional1 {
	color:#666
}
span.error {
	font-weight:bold;
	color:#900
}
div.alert {
	color:#333;
	background-color:#fff;
	margin:0 0 15px;
	padding:6px 7px 6px 35px;
*padding-top:14px;
	min-height:28px;
	position:relative
}
div.alert img {
	display:none
}
div.alert p, div.alert h3 {
	font-size:13px;
	margin:7px 21px 5px 7px
}
div.alert ul {
	font-size:13px;
	margin:0 21px 5px 7px
}
div.alert h3 {
	font-weight:bold
}
div.alert .dismiss {
	display:block;
	padding:7px 0 2px;
	position:absolute;
	text-indent:-12345px;
	top:3px;
	right:14px;
	width:18px;
	cursor:pointer;
	height:12px
}
div.alert a:visited {
	color:#039
}
div.alert.attention {
	background:#fff5cc
}
div.alert.warning {
	background:#fff5cc
}
div.alert.error {
	background:#fee
}
div.alert.success {
	background:#e6f8dd
}
div.alert.notice {
	background:#ddf0f8
}

.btn-link {
	background:transparent;
	border:0;
	color:#069;
	cursor:pointer;
	display:inline-block;
	margin:0;
*overflow:visible;
	padding:0
}
.btn-link:hover {
	text-decoration:underline
}






.bar {
	border-width:1px;
	border-style:solid;
	-moz-border-radius:3px;
	-webkit-border-radius:3px;
	border-radius:3px;
	font-size:16px;
	line-height:1.7;
	margin:0;
	overflow:visible;
	padding:5px 10px 5px;
*padding:2px 10px 1px;
	text-decoration:none !important;
	vertical-align:top;
	width:auto
	background:#CECECE;
	background:-moz-linear-gradient(top, #f2f2f2 0, #f2f2f2 1px, #e4e4e4 1px, #cecece 100%);
	background:-o-linear-gradient(top, #f2f2f2 0, #f2f2f2 1px, #e4e4e4 1px, #cecece 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0, #f2f2f2), color-stop(5%, #f2f2f2), color-stop(5%, #e4e4e4), color-stop(100%, #cecece));
	background:linear-gradient(top, #f2f2f2 0%, #f2f2f2 1px, #e4e4e4 1px, #cecece 100%);
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#E4E4E4', endColorstr='#CECECE', GradientType=0 );
	border-color:#999;
	color:#666 !important;
	height: 35px;
	line-height: 35px;
}

.bar a:hover{
	border:1px splid #069;
}
</style>
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
    <h1>下载压缩包(curl<?php echo function_exists("curl_init")==true?"可用":'<span class="error">不可用</span>'?>)</h1>
    <p class="sub">将其他服务器上软件压缩包直接下载到你的服务器上，找到安装包下载地址，然后填写下面的表格。</p>
    <div class="row">
      <label> 远程文件地址： </label>
      <div class="field">
        <input name="url" type="text" id="url" size="100" style="width:500px" >
        <p class="help-text">例如：http://cn.wordpress.org/wordpress-3.4.1-zh_CN.zip </p>
      </div>
    </div>
    <div class="row">
      <label>预计下载时间： </label>
      <div class="field">
        <input name="timeout" type="text" id="timeout" size="10" value="120" style="width:50px">
        秒
        <p class="help-text">这个参数会应用到set_time_limt()函数，保证脚本执行时间足够长。</p>
      </div>
    </div>
    <div class="row">
      <label> 另存为： </label>
      <div class="field">
        <input name="path" type="text" id="path" value="<?php echo dirname(___FILE__);?>" size="80">
        文件名
        <input name="name" type="text" id="name" size="20" style="width:200px">
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
<div class="stylized myform">
  <form action="" method="post">
    <h1>解压缩 </h1>
    <p class="sub">解压缩下载完毕的应用程序。支持zip,tar.gz两种，为什么没有rar因为几乎所有的发行包选择用zip或者tar来打包。</p>
    <p>&nbsp;</p>
    <div class="row">
      <label>服务器文件地址： </label>
      <div class="field">
        <input name="file" type="text" id="file" size="100" value="<?php echo $file?>" style="width:500px">
      <a href="#" id="#" style="font-size:12px">预览压缩包</a> </div>
    </div>
    <div class="row">
      <label>解压缩到： </label>
      <div class="field">
        <input name="path" type="text" id="path" value="<?php echo dirname(___FILE__);?>" size="80">
      </div>
    </div>
    <div class="row">
      <label>预计使用时间： </label>
      <div class="field">
        <input name="timeout" type="text" id="timeout" size="10" value="120" style="width:50px">
        秒
        <p class="help-text">这个参数会应用到set_time_limt()函数，保证脚本执行时间足够长。</p>
      </div>
    </div>
    <div class="row">
      <p class="actions">
        <input name="unzip" type="submit" class="btn-primary" id="button" value="提交">
      </p>
    </div>
  </form>
</div>
</body>
</html>
