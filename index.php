<!DOCTYPE html>
<HTML lang="en">
<HEAD>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.5, initial-scale=1.0, user-scalable=0">

<style>
	body{margin:0; text-align:center;}
	section{display:inline-block; opacity:0; padding-left:20px; width:auto; max-width:100%; text-align:left;}
	section em{font-style:normal; font:12px tahoma,sans-serif; color:#999;}
	section div{margin-left:13px; position:relative;}
	section div > a{font:15px tahoma,sans-serif; text-decoration:none; position:relative; display:inline-block; padding:5px 2px; min-width:50px; padding-right:20px;}
	section div > a:hover{opacity:.5;}
	section div[dir] > a{font-weight:bold;}
	section div[dir] > span{position:relative; display:block; padding-left:10px;}
	section div[dir] > span > a,
	section div[dir] > span > a:visited{display:block; position:absolute; top:-23px; left:-20px; width:20px; height:20px; color:#000; text-decoration:none; cursor:default;}
	section div[dir] > span > a:before{content:"+"; font-family:serif; font-weight:bold; padding:0 5px; display:block;}
	section div[dir] > span.expand > a:before{content:"_"; font-weight:bold; padding:0 5px; margin:-8px 0 0 1px;}
	nav{z-index:2; position:fixed; top:0px; left:0px; width:100%; box-shadow: 0 0 12px rgba(0,0,0,.5); text-align:center; background:#f7f7f7;}
	nav > a{position:relative; display:inline-block; font:18px tahoma,sans-serif; font-weight:bold; margin:10px 10px 13px; color:#000; text-decoration:none !important; background:transparent !important;}
	nav > a:before{content:"[ "; visibility:hidden;}
	nav > a:after{content:" ]"; visibility:hidden;}
	nav > a:hover:before,
	nav > a:hover:after{visibility:visible; color:#999;}
	label{display:block; text-align:center; font:15px tahoma,sans-serif; color:#999; padding-top:70px;}
	label > span{display:block; font-size:50px;}
</style>

<script type="text/javascript">
	function setCookie(cookieName,cookieValue,ms) {
		var expire;
		if (ms) {
			expire = new Date();
			expire.setTime(expire.getTime() + ms);
			expire = expire.toUTCString();
			cookieValue = escape(cookieValue);
		} else {
			expire = "Thu, 01 Jan 1970 00:00:00 GMT; Max-Age=0;";
			cookieValue = "";
		}
		document.cookie = cookieName +"="+ cookieValue +
			";domain="+ window.location.hostname +
			";path=/"+
			";expires="+ expire;
	}
	function getCookie(cookieName){
		var theCookie = "" + document.cookie;
		var ind = theCookie.indexOf(cookieName);
		if(ind==-1||cookieName=="") return "";
		var ind1=theCookie.indexOf(';',ind);
		if(ind1==-1) ind1=theCookie.length;
		return unescape(theCookie.substring(ind+cookieName.length+1,ind1));
	}
	function getOffsetTop(elm) {
		if (!elm) return 0;
		var top = 0;
		do {
			top += elm.offsetTop || 0;
			elm = elm.offsetParent;
		} while(elm);
		return top.toFixed(2);
	};
	function getStage() {
		var scr = pageYOffset || (document.documentElement.clientHeight ? document.documentElement.scrollTop : document.body.scrollTop);
		setCookie('_scr_', scr, 3000);
	}
	function setStage() {
		// scrollTop
		var scr = getCookie('_scr_') || 0;
		if (scr) {
			var top = getOffsetTop(document.querySelector('div[deepest]')) - document.querySelector('label').offsetHeight;
			if (scr > top) scr = top;
		}
		document.body.scrollTop = document.documentElement.scrollTop = scr;
		document.querySelector('section').style.opacity = '1';
		// tree events
		pdir = document.querySelectorAll('div[dir] > span > a');
		for (var i=pdir.length; i--;){
			if(pdir[i].parentNode.className == 'expand') {
				pdir[i].onclick = function(e){
					e.preventDefault();
					if(this.parentNode.className == 'expand'){
						this.parentNode.className = '';
						this.nextSibling.style.display = 'none';
					}else{
						this.parentNode.className = 'expand';
						this.nextSibling.style.display = '';
					}
				}
			}else{
				pdir[i].onclick = function(){
					document.querySelector('section').style.opacity = '.3';
				}
			}
		}
	}
</script>

<?php
	$uri = explode('/', $_SERVER['SCRIPT_NAME']);
	array_pop($uri);
	$uri = join('/', $uri).'/';
	$root = $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$expand = isset($_GET['path']) ? explode("/", $_GET['path']) : [];

	function scanner($path){
		global $expand;
		global $uri;
		$expandPath = $path ? $path.'/' : '';
		$path = $path ? './'.$path : './';
		$files = scandir($path);
		$dirs = '';
		$bins = '';
		sort($files);
		foreach($files as $file){
			$realPath = $expandPath.$file;
			//echo $realPath.'<br>';
			if ($file != '.' && $file != '..' && $file != 'php-source') {
				if(is_dir($realPath)){
					if(!empty($expand) && $expand[0] == $file){
						array_shift($expand);
						$deepest = empty($expand) ? ' deepest' : '';
						$dirs.='<div dir'.$deepest.'><a href="'.$realPath.'/">'.$file.'</a><span class="expand"><a href=""></a><span>'.scanner($realPath).'</span></span></div>';
					}else{
						$dirs.='<div dir><a href="'.$uri.$realPath.'/">'.$file.'</a><span><a href="?path='.$realPath.'"></a></span></div>';
					}
				}
				elseif(!is_dir($realPath)) $bins.='<div><a href="'.$uri.$realPath.'">'.$file.'</a></div>';
			}
		}
		$output = $dirs.$bins;
		return empty($output) ? '<em>< empty ></em>' : $output;
	}
?>

</HEAD>
<BODY onload="setStage()" onunload="getStage()">

<nav>
	<a href="<?php echo '//'.$root ?>">home</a>
	<a href="?phpinfo">phpinfo</a>
</nav>
<label></label>

<?php
	echo '<section>'.scanner(NULL).'<br><br></section>';
	if(isset($_GET['phpinfo'])) phpinfo();
?>

<!--
   Copyright 2017 addmind.org

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
-->
</BODY>
</HTML>