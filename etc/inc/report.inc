<?php
/*
	report.inc

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2025 XigmaNAS® <info@xigmanas.com>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS®, either expressed or implied.
*/
require_once 'config.inc';
require_once 'util.inc';
require_once 'disks.inc';
require_once 'email.inc';
require_once 'rc.inc';

class StatusReportArticle {
	var $_title = '';
	var $_text = '';

	function __construct($title,$text) {
		$this->_title = $title;
		$this->_text = $text;
	}
	function GetTitle() {
		return $this->_title;
	}
	function GetText() {
		return $this->_text;
	}
}
class StatusReportArticleCmd extends StatusReportArticle {
	function __construct($title,$command) {
		parent::__construct($title,'');
		if(!empty($command)):
			mwexec2("{$command} 2>&1",$execOutput,$execStatus);
			for($i = 0;isset($execOutput[$i]);$i++):
				if($i > 0):
					$this->_text .= "\n";
				endif;
//				Print command output.
				$this->_text .= $execOutput[$i];
			endfor;
			$this->_text .= "\n";
		endif;
	}
}
class StatusReportRenderer {
	function Generate($articles) {
		$report = '';
		$this->_RenderReport($report,$articles);
		return $report;
	}
	function _RenderReport(&$report,$articles) {
		for($i = 0;is_object($articles[$i]);$i++):
			$this->_RenderArticle($report,$articles[$i]);
		endfor;
	}
	function _RenderArticle(&$report,$article) {
//		To be implemented by derived class.
	}
}
class StatusReportRendererText extends StatusReportRenderer {
	function _RenderArticle(&$report,$article) {
//		Render article title.
		$title = $article->GetTitle();
		if(!empty($title)):
			$nWidth = strlen($title) + 1;
			$report .= sprintf("%s:\n%'-{$nWidth}s\n",$title,''); // Print title and underline.
		endif;
//		Render article text.
		$report .= $article->GetText();
		$report .= "\n";
	}
}
class StatusReportRendererHTML extends StatusReportRenderer {
	function _RenderReport(&$report,$articles) {
		$dateOutput = [];
		$dateStatus = null;
		mwexec2('/bin/date',$dateOutput,$dateStatus);
		$currentDate = $dateOutput[0];
		$report = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Status Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">
<!--
body, td, th {
	font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
a {
	text-decoration: none;
}
.title {
	font-size: 18px;
	color: #777777;
	font-weight: bold;
}
.listtopic {
	border-right: 1px solid #999999;
	font-size: 11px;
	background-image: url("images/listtopic_bg.png");
	background-color: #95A7C0;
	padding-right: 16px;
	padding-left: 6px;
	color: #FFFFFF;
	font-weight: bold;
	padding-top: 5px;
	padding-bottom: 5px;
}
.listlr {
	background-color: #FFFFFF;
	border-right: 1px solid #999999;
	border-bottom: 1px solid #999999;
	border-left: 1px solid #999999;
	font-size: 11px;
	padding-top: 4px;
	padding-bottom: 4px;
	padding-right: 6px;
	padding-left: 6px;
	text-align: left;
}
pre {
	margin: 0px;
	font-family: 'Courier New', Courier, monospace;
	font-weight: normal;
	font-size: 9pt;
}
table {
	width: 800px;
}
-->
</style>
</head>
<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
<span class="title">Status Report</span><br>
<strong>{$currentDate}</strong><br>

EOD;

//		Render table of contents.
		$this->_RenderTableOfContents($report,$articles);
//		Render articles.
		for($i = 0;isset($articles[$i]) && is_object($articles[$i]);$i++):
			$this->_RenderArticle($report,$articles[$i]);
		endfor;
		$report .= <<<EOD
</body>
</html>

EOD;
	}
	function _RenderTableOfContents(&$report,$articles) {
		$report .= "<br>";
		$report .= "This status page includes the following information:\n";
		$report .= "<ul>\n";
		for($i = 0;isset($articles[$i]) && is_object($articles[$i]);$i++):
			$report .= "<li><strong><a href=\"#" . $articles[$i]->GetTitle() . "\">" . $articles[$i]->GetTitle() . "</a></strong>\n";
		endfor;
		$report .= "</ul>\n";
	}
	function _RenderArticle(&$report,$article) {
		$report .= "<a name=\"" . $article->GetTitle() . "\">\n";
		$report .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$report .= "<tr><td class=\"listtopic\">" . $article->GetTitle() . "</td></tr>\n";
		$report .= "<tr><td class=\"listlr\"><pre>";
			$report .= htmlspecialchars($article->GetText(),ENT_NOQUOTES);
			$report .= "</pre></tr>\n";
		$report .= "</table>\n";
		$report .= "<br>\n";
	}
}
class StatusReport {
/**
 *	Contains commands using to generate report.
 *	@var array
 */
	var $articles = [];
/**
 *	Sets the Content-type of the message.
 *	@var bollean
 */
	var $isHTML = false;
/**
 *	Sets message type to HTML.
 *	@param bool $bool
 *	@return void
 */
	function IsHTML($bool) {
		$this->isHTML = $bool;
	}
/**
 *  Add article.
 *  @return void
 */
	function AddArticle($articles) {
		$this->articles[] = $articles;
	}
/**
 *	Generate report.
 *	@return string
 */
	function Generate() {
		$report = '';
		if($this->isHTML === true):
			$renderer = new StatusReportRendererHTML;
			$report = $renderer->Generate($this->articles);
		else:
			$renderer = new StatusReportRendererText;
			$report = $renderer->Generate($this->articles);
		endif;
		return $report;
	}
}
function report_send_mail() {
	global $config,$g;

	$smartd_drivedb_arg = get_smartmontools_drivedb_arg();
//	Consider modified clog path
	$clogdir = rc_getenv_ex('clog_logdir',$g['varlog_path']);
//	Get number of lines to be displayed
	$numentries = $config['syslogd']['nentries'] ?? 50;
//	Use default value if setting is not available.
	if(!$numentries):
		$numentries = 50;
	endif;
//	Reverse log output?
	$reverse = isset($config['syslogd']['reverse']) ? '-r' : '';
//	Generate report
	$statusreport = new StatusReport();
	if(isset($config['statusreport']['report']) && is_array($config['statusreport']['report'])):
		if(in_array('systeminfo',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd('Version','cat /etc/prd.version'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Revision','cat /etc/prd.revision'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Platform','cat /etc/platform'));
			$statusreport->AddArticle(new StatusReportArticleCmd('System bios','/bin/kenv -q'));
			$statusreport->AddArticle(new StatusReportArticleCmd('System uptime','uptime'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Interfaces','/sbin/ifconfig -a'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Routing tables','netstat -nr'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Firewall','ipfw -at list'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Processes','ps xauww'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Memory','top -b 0 | grep Mem'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Swap usage','/usr/sbin/swapinfo'));
			$statusreport->AddArticle(new StatusReportArticleCmd('ZFS pool list','/sbin/zpool list'));
			$statusreport->AddArticle(new StatusReportArticleCmd('ZFS pool status','/sbin/zpool status'));
			$statusreport->AddArticle(new StatusReportArticleCmd('SCSI disk','/sbin/camcontrol devlist'));
			$disklist = get_physical_disks_list();
			foreach($disklist as $disknamek => $disknamev):
				$statusreport->AddArticle(new StatusReportArticleCmd("S.M.A.R.T. [/dev/{$disknamek}]",'/usr/local/sbin/smartctl ' . $smartd_drivedb_arg . "-a /dev/{$disknamek}"));
			endforeach;
			$statusreport->AddArticle(new StatusReportArticleCmd('Geom Concat','/sbin/gconcat list'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Geom Stripe','/sbin/gstripe list'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Geom Mirror','/sbin/gmirror list'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Geom Vinum','/sbin/gvinum list'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Mount point','/sbin/mount'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Free disk space','/bin/df -h'));
			$statusreport->AddArticle(new StatusReportArticleCmd('Encrypted disks','/sbin/geli list'));
		endif;
		if(in_array('dmesg',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd('dmesg','/sbin/dmesg'));
		endif;
		if(in_array('systemlog',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} System log entries","/usr/sbin/clog {$clogdir}/system.log 2>&1 | tail {$reverse} -n {$numentries}"));
		endif;
		if(in_array('ftplog',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} FTP log entries","/usr/sbin/clog {$clogdir}/ftp.log 2>&1 | tail {$reverse} -n {$numentries}"));
		endif;
		if(in_array('rsynclog',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} RSYNCD log entries","/usr/sbin/clog {$clogdir}/rsyncd.log 2>&1 | tail {$reverse} -n {$numentries}"));
		endif;
		if(in_array('sshdlog',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} SSHD log entries","/usr/sbin/clog {$clogdir}/sshd.log 2>&1 | tail {$reverse} -n {$numentries}"));
		endif;
		if(in_array('smartdlog',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} SMARTD log entries","/usr/sbin/clog {$clogdir}/smartd.log 2>&1 | tail {$reverse} -n {$numentries}"));
		endif;
		if(in_array('daemonlog',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} Daemon log entries","/usr/sbin/clog {$clogdir}/daemon.log 2>&1 | tail {$reverse} -n {$numentries}"));
		endif;
		if(in_array('script',$config['statusreport']['report'])):
			$statusreport->AddArticle(new StatusReportArticleCmd('Custom script entries',$config['statusreport']['report_scriptname']));
		endif;
	endif;
//	Send email and log result.
	$result = email_send($config['statusreport']['to'],$config['statusreport']['subject'],$statusreport->Generate(),$error);
	if($result != 0):
		write_log(sprintf('Failed to send status report to: %s.',$config['statusreport']['to']));
		return 1;
	endif;
	write_log(sprintf('Status report successfully sent to: %s.',$config['statusreport']['to']));
	return 0;
}
/**
 *	Generate a HTML formated status report.
 *	@return The HTML report as string.
 */
function report_generate_html() {
	global $g;

	$smartd_drivedb_arg = get_smartmontools_drivedb_arg();
//	Generate HTML report
	$statusreport = new StatusReport();
	$statusreport->IsHTML(true);
//	Consider modified clog path
	$clogdir = rc_getenv_ex('clog_logdir',$g['varlog_path']);
	$statusreport->AddArticle(new StatusReportArticleCmd('Version','cat /etc/prd.version'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Revision','cat /etc/prd.revision'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Platform','cat /etc/platform'));
	$statusreport->AddArticle(new StatusReportArticleCmd('System bios','/bin/kenv -q'));
	$statusreport->AddArticle(new StatusReportArticleCmd('System uptime','uptime'));
	$statusreport->AddArticle(new StatusReportArticleCmd('dmesg','/sbin/dmesg'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Interfaces','/sbin/ifconfig -a'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Routing tables','netstat -nr'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Firewall','ipfw -at list'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Processes','ps xauww'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Network performances','netstat -m'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Memory','top -b 0 | grep Mem'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Swap usage','/usr/sbin/swapinfo'));
	$statusreport->AddArticle(new StatusReportArticleCmd('ZFS pool list','/sbin/zpool list'));
	$statusreport->AddArticle(new StatusReportArticleCmd('ZFS pool status','/sbin/zpool status'));
	$statusreport->AddArticle(new StatusReportArticleCmd('SCSI disk','/sbin/camcontrol devlist'));
	$disklist = get_physical_disks_list();
	foreach($disklist as $disknamek => $disknamev):
		$statusreport->AddArticle(new StatusReportArticleCmd("S.M.A.R.T. [/dev/{$disknamek}]",'/usr/local/sbin/smartctl ' . $smartd_drivedb_arg . "-a /dev/{$disknamek}"));
	endforeach;
	$statusreport->AddArticle(new StatusReportArticleCmd('Geom Concat','/sbin/gconcat list'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Geom Stripe','/sbin/gstripe list'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Geom Mirror','/sbin/gmirror list'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Geom Vinum','/sbin/gvinum list'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Mount point','/sbin/mount'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Free disk space','/bin/df -h'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Encrypted disks','/sbin/geli list'));
	$statusreport->AddArticle(new StatusReportArticleCmd('rc.conf','cat /etc/rc.conf'));
	$statusreport->AddArticle(new StatusReportArticleCmd('resolv.conf','cat /var/etc/resolv.conf'));
	$statusreport->AddArticle(new StatusReportArticleCmd('hosts','cat /etc/hosts'));
	$statusreport->AddArticle(new StatusReportArticleCmd('hosts.allow','cat /etc/hosts.allow'));
	$statusreport->AddArticle(new StatusReportArticleCmd('crontab','cat /var/etc/crontab'));
	$statusreport->AddArticle(new StatusReportArticleCmd('dhclient.conf','cat /etc/dhclient.conf'));
	$statusreport->AddArticle(new StatusReportArticleCmd('hast.conf','cat /var/etc/hast.conf'));
	$statusreport->AddArticle(new StatusReportArticleCmd('smb4.conf','cat /var/etc/smb4.conf'));
	$statusreport->AddArticle(new StatusReportArticleCmd('sshd.conf','cat /var/etc/ssh/sshd_config'));
	$statusreport->AddArticle(new StatusReportArticleCmd('mdnsresponder.conf','cat /var/etc/mdnsresponder.conf'));
	$statusreport->AddArticle(new StatusReportArticleCmd('Last 250 system log entries',"/usr/sbin/clog {$clogdir}/system.log 2>&1 | tail -n 250"));
	$statusreport->AddArticle(new StatusReportArticleCmd('/conf','ls /conf'));
	$statusreport->AddArticle(new StatusReportArticleCmd('/var/etc','ls /var/etc'));
	$statusreport->AddArticle(new StatusReportArticleCmd('/var/run','ls /var/run'));
	$statusreport->AddArticle(new StatusReportArticleCmd('config.xml',"/usr/local/bin/xml ed -P -u \"//*/password\" -v \"xxxxx\" -u \"//system/email/from\" -v \"xxxxx\" -u \"//statusreport/to\" -v \"xxxxx\" -u \"//*/media_uctlsecret\" -v \"xxxxx\" -u \"//*/authsecret\" -v \"xxxxx\" -u \"//*/authmsecret\" -v \"xxxxx\" /conf/config.xml"));
	return $statusreport->Generate();
}
