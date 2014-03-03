<?php


if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
header("content-type:text/html;charset=utf-8");
function parse_template($tplfile, $templateid, $tpldir) {
	//global $language, $subtemplates, $timestamp;

	$nest = 6;
	$basefile = $file = basename($tplfile, '.htm');
	$file == 'header' && CURSCRIPT && $file = 'header_'.CURSCRIPT;
	$objfile = ROOT_PATH.'./data/'.COMPILEDIR.'/'.STYLEID.'_'.$templateid.'_'.$file.'.tpl.php';

	if(!@$fp = fopen($tplfile, 'r')) {
		dexit("Current template file './$tpldir/$file.htm' not found or have no access!");
	}

	$template = @fread($fp, filesize($tplfile));
	fclose($fp);

	$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
	$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

	$headerexists = preg_match("/{(sub)?template\s+header\}/", $template) || $basefile == 'header_ajax';
	$subtemplates = array();
	for($i = 1; $i <= 3; $i++) {
		if(strexists($template, '{subtemplate')) {
			$template = preg_replace("/[\n\r\t]*\{subtemplate\s+([a-z0-9_:]+)\}[\n\r\t]*/ies", "stripvtemplate('\\1', 1)", $template);
		}
	}
	$template = preg_replace("/[\n\r\t]*\{csstemplate\}[\n\r\t]*/ies", "loadcsstemplate('\\1')", $template);
	$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
	$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
	$template = preg_replace("/\{lang\s+(.+?)\}/ies", "languagevar('\\1')", $template);
	$template = preg_replace("/\{faq\s+(.+?)\}/ies", "faqvar('\\1')", $template);
	$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);

	$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
	$template = preg_replace("/$var_regexp/es", "addquote('<?=\\1?>')", $template);
	$template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "addquote('<?=\\1?>')", $template);

	$headeradd ='';
	if(!empty($subtemplates)) {
		$headeradd .= "\n0\n";
		foreach ($subtemplates as $fname) {
			$headeradd .= "|| checktplrefresh('$tplfile', '$fname', $timestamp, '$templateid', '$tpldir')\n";
		}
		$headeradd .= ';';
	}

	$template = "<? if(!defined('IN_SYSTEM')) exit('Access Denied'); {$headeradd}?>\n$template";

	$template = preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_:]+)\}[\n\r\t]*/ies", "stripvtemplate('\\1', 0)", $template);
	$template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/ies", "stripvtemplate('\\1', 0)", $template);
	$template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<? \\1 ?>','')", $template);
	$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "stripvtags('<? echo \\1; ?>','')", $template);
	$template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "stripvtags('\\1<? } elseif(\\2) { ?>\\3','')", $template);
	$template = preg_replace("/([\n\r\t]*)\{else\}([\n\r\t]*)/is", "\\1<? } else { ?>\\2", $template);

	for($i = 0; $i < $nest; $i++) {
		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('<? if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<? } } ?>')", $template);
		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/ies", "stripvtags('<? if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<? } } ?>')", $template);
		$template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/ies", "stripvtags('\\1<? if(\\2) { ?>\\3','\\4\\5<? } ?>\\6')", $template);
	}

	$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
	$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

	if(!@$fp = fopen($objfile, 'w')) {
		dexit("Directory './data/compile/' not found or have no access!");
	}

	$template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "transamp('\\0')", $template);//链接的正则表达式
	$template = preg_replace("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/ise", "stripscriptamp('\\1', '\\2')", $template);

	$template = preg_replace("/[\n\r\t]*\{block\s+([a-zA-Z0-9_]+)\}(.+?)\{\/block\}/ies", "stripblock('\\1', '\\2')", $template);
	//$template = str_replace('<div id="phpup_version"></div>', '<div id="phpup_version"><A HREF="http://www.loopnow.com/" style="color:#ff0000;">Www.loopnow.Com</A> Powered by '.VERSION.'&copy; 2010-2011.</div>', $template);
	flock($fp, 2);
	fwrite($fp, $template);
	fclose($fp);
}

function stripvtemplate($tpl, $sub) {
	$vars = explode(':', $tpl);
	$templateid = 0;
	$tpldir = '';
	if(count($vars) == 2) {
		list($templateid, $tpl) = $vars;
		$tpldir = './plugins/'.$templateid.'/templates';
	}
	if($sub) {
		return loadsubtemplate($tpl, $templateid, $tpldir);
	} else {
		return stripvtags("<? include template('$tpl', '$templateid', '$tpldir'); ?>", '');
	}
}

function loadsubtemplate($file, $templateid = 0, $tpldir = '') {
	global $subtemplates;
	$tpldir = $tpldir ? $tpldir : TPLDIR;
	$templateid = $templateid ? $templateid : TEMPLATEID;

	$tplfile = ROOT_PATH.'./'.$tpldir.'/'.$file.'.htm';
	if($templateid != 1 && !file_exists($tplfile)) {
		$tplfile = ROOT_PATH.'./views/default/'.$file.'.htm';
	}
	$content = @implode('', file($tplfile));
	$subtemplates[] = $tplfile;
	return $content;
}

function loadcsstemplate() {
	global $csscurscripts;
	$scriptcss = '<link rel="stylesheet" type="text/css" href="data/compile/style_{STYLEID}_common.css?{VERHASH}" />';
	$content = $csscurscripts = '';
	$content = @implode('', file(ROOT_PATH.'./data/compile/style_'.STYLEID.'_script.css'));
	$content = preg_replace("/([\n\r\t]*)\[CURSCRIPT\s+=\s+(.+?)\]([\n\r]*)(.*?)([\n\r]*)\[\/CURSCRIPT\]([\n\r\t]*)/ies", "cssvtags('\\2','\\4')", $content);
	if($csscurscripts) {
		$csscurscripts = preg_replace(array('/\s*([,;:\{\}])\s*/', '/[\t\n\r]/', '/\/\*.+?\*\//'), array('\\1', '',''), $csscurscripts);
		if(@$fp = fopen(ROOT_PATH.'./data/compile/scriptstyle_'.STYLEID.'_'.CURSCRIPT.'.css', 'w')) {
			fwrite($fp, $csscurscripts);
			fclose($fp);
		} else {
			exit('Can not write to cache files, please check directory ./data/compile/ .');
		}
		$scriptcss .='<link rel="stylesheet" type="text/css" href="data/compile/scriptstyle_{STYLEID}_{CURSCRIPT}.css?{VERHASH}" />';
	}
	$content = str_replace('[SCRIPTCSS]', $scriptcss, $content);
	return $content;
}

function cssvtags($curscript, $content) {
	global $csscurscripts;
	$csscurscripts .= in_array(CURSCRIPT, explode(',', $curscript)) ? $content : '';
}

function transamp($str) {
	$str = str_replace('&', '&amp;', $str);
	$str = str_replace('&amp;amp;', '&amp;', $str);
	$str = str_replace('\"', '"', $str);
	return $str;
}

function addquote($var) {
	return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}

function languagevar($var) {
	global $templatelang;
	if(isset($GLOBALS['language'][$var])) {
		return $GLOBALS['language'][$var];
	} else {
		$vars = explode(':', $var);
		if(count($vars) != 2) {
			return "$var";
		}
		if(in_array($vars[0], $GLOBALS['templatelangs']) && empty($templatelang[$vars[0]])) {
			@include_once ROOT_PATH.'./forumdata/plugins/'.$vars[0].'.lang.php';
		}
		if(!isset($templatelang[$vars[0]][$vars[1]])) {
			return "!$var!";
		} else {
			return $templatelang[$vars[0]][$vars[1]];
		}
	}
}

function faqvar($var) {
	global $_DCACHE;
	include_once ROOT_PATH.'./data/compile/cache_faqs.php';

	if(isset($_DCACHE['faqs'][$var])) {
		return '<a href="index.php?action=faq&id='.$_DCACHE['faqs'][$var]['fpid'].'&messageid='.$_DCACHE['faqs'][$var]['id'].'" target="_blank">'.$_DCACHE['faqs'][$var]['keyword'].'</a>';
	} else {
		return "!$var!";
	}
}

function stripvtags($expr, $statement) {
	$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
	$statement = str_replace("\\\"", "\"", $statement);
	return $expr.$statement;
}

function stripscriptamp($s, $extra) {
	$extra = str_replace('\\"', '"', $extra);
	$s = str_replace('&amp;', '&', $s);
	return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
}

?>