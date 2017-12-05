<?php
ob_clean();

//////////////////////////////////
// Powered By Evi11Crew Team    //
// WAF version 0.0.2b           //
// code by Vyacheslav Sinitsyn  //
// email: ec.evi11ook@gmail.com //
//////////////////////////////////


//
// create .htaccess in site home directory and add string
// php_value auto_prepend_file "{FULL_PATCH}/EvillCrewWAF.php"
//

//print_r($_SERVER);
//logdata();
$MSGERRORWAF = "
<html><head><title>Error</title>
<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />
</head>
<body><H2>Внимание</H2><br>
Ваш запрос к web приложению не может быть выполнен в связи с нарушением политик безопасности. <br>
Свяжитесь с службой поддержки для получения дополнительной информации.<br>
Event id: {EVENTID}<br>
<br><br>
<H2>ATTENITON</H2><br>
Your request to the web application can't be executed due to violation of security policies. <br>
Contact support for more information.<br>
Event id: {EVENTID}<br>
</body></html>
";

$checkDataArr = array($_REQUEST,$_COOKIE,$_SERVER['REQUEST_URI'],$_SERVER['QUERY_STRING'],$_SERVER['HTTP_USER_AGENT'],$_SERVER['HTTP_X_FORWARDED_FOR'],$_SERVER['HTTP_REFERER'],"FILES"=>$_FILES);

function logdata($data=''){
	file_put_contents('log/waf.log', "----------\n".print_r($data,True)."\n\n".print_r($_SERVER,True)."\nREQ:\n".print_r($_REQUEST,True)."\nCOOK:\n".print_r($_COOCKIE,True), FILE_APPEND);
}

function checkInData($data,$FILE=False){
	global $MSGERRORWAF;
	$patt = "~\/\.\.\/\.\.\/\.\.\/|{0-9a-zA-Z}[80]|eval[^\(]*\(|assert[^\(]*\(|include[^\(]*\(|base64_decode[^\(]*\(|stripslashes[^\(]*\(|strip_tags[^\(]*\(|fopen[^\(]*\(|chmod[^\(]*\(|chown[^\(]*\(|chgrp[^\(]*\(|unlink[^\(]*\(|unset[^\(]*\(|fgetc[^\(]*\(|fgets[^\(]*\(|file_get_contents[^\(]*\(|file_put_contents[^\(]*\(|fwrite[^\(]*\(|move_uploaded_file[^\(]*\(|is_uploaded_file[^\(]*\(|rmdir[^\(]*\(|fromCharCode[^\(]*\(|tmpfile[^\(]*\(|tempnam[^\(]*\(|phpinfo[^\(]*\(|basename[^\(]*\(|curl_init[^\(]*\(|socket_create[^\(]*\(|popen[^\(]*\(|exec[^\(]*\(|system[^\(]*\(|passthru[^\(]*\(|proc_open[^\(]*\(|gzuncompress[^\(]*\(|shell_exec[^\(]*\(|\bselect\b|\border\b|\bunion\b|delete from|insert into~i";
	if($FILE){
		if(is_array($data)){
			foreach ($data as $key => $value) {
				if ($value['error'] < 1){
					try{
						if (file_exists($value['tmp_name'])){
							if (preg_match($patt,mb_strtolower(file_get_contents($value['tmp_name'])))){
								echo str_replace("{EVENTID}",'00001',$MSGERRORWAF);
								logdata($value['tmp_name']);
								exit();
							}
						}
					} catch (Exceptio $e) { echo ""; }
				}
			}
		}
	}
	if(!$FILE){
			if (is_array($data)){
				foreach ($data as $key => $value) {
					checkInData($value);
					if($key == "FILES"){
						checkInData($value,True);
					}
				}
			}else{
				try{
					if ( preg_match($patt, mb_strtolower($data)) || preg_match($patt, mb_strtolower(stripslashes($data)) ) ) {
						echo str_replace("{EVENTID}",'00002',$MSGERRORWAF);
						logdata($data);
						exit();
					}
				} catch (Exception $e) { echo "";}
				try{
					if ( preg_match($patt, mb_strtolower(base64_decode($data)) ) || preg_match($patt, mb_strtolower(stripslashes(base64_decode($data)) ) ) ){
						echo str_replace("{EVENTID}",'00003',$MSGERRORWAF);
						logdata($data);
						exit();
					}
				} catch (Exception $e) {echo "";}
			}


		}
	}
ob_end_clean();
?>
