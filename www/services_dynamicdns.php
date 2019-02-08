<?php
/*
	services_dynamicdns.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018-2019 XigmaNAS <info@xigmanas.com>.
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
	of XigmaNAS, either expressed or implied.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';
require_once 'properties_services_dynamicdns.php';
require_once 'co_request_method.php';

function dynamicdns_sphere() {
	global $config;

	$sphere = new co_sphere_row('services_dynamicdns','php');
	$sphere->set_enadis(true);
	$sphere->grid = &array_make_branch($config,'dynamicdns');
	return $sphere;
}
//	init properties and sphere
$cop = new dynamicdns_properties();
$sphere = &dynamicdns_sphere();
$a_referer = [
	$cop->get_enable(),
	$cop->get_auxparam()
];
$input_errors = [];
//	determine request method
$rmo = new co_request_method();
$rmo->add('GET','edit',PAGE_MODE_EDIT);
$rmo->add('GET','view',PAGE_MODE_VIEW);
$rmo->add('POST','edit',PAGE_MODE_EDIT);
if($sphere->is_enadis_enabled()):
	$rmo->add('POST','enable',PAGE_MODE_VIEW);
	$rmo->add('POST','disable',PAGE_MODE_VIEW);
endif;
$rmo->add('POST','reload',PAGE_MODE_VIEW);
$rmo->add('POST','restart',PAGE_MODE_VIEW);
$rmo->add('POST','save',PAGE_MODE_POST);
$rmo->add('POST','view',PAGE_MODE_VIEW);
$rmo->add('SESSION',$sphere->get_basename(),PAGE_MODE_VIEW);
$rmo->set_default('GET','view',PAGE_MODE_VIEW);
list($page_method,$page_action,$page_mode) = $rmo->validate();
//	catch error code
switch($page_action):
	case $sphere->get_basename():
		$retval = filter_var($_SESSION[$sphere->get_basename()],FILTER_VALIDATE_INT,['options' => ['default' => 0]]);
		unset($_SESSION['submit']);
		unset($_SESSION[$sphere->get_basename()]);
		$savemsg = get_std_save_message($retval);
		if($retval !== 0):
			$page_action = 'edit';
			$page_mode = PAGE_MODE_EDIT;
		else:
			$page_action = 'view';
			$page_mode = PAGE_MODE_VIEW;
		endif;
		break;
endswitch;
//	validate
switch($page_action):
	case 'edit':
	case 'view':
	case 'disable':
	case 'enable':
	case 'reload':
	case 'restart':
		$source = $sphere->grid;
		foreach($a_referer as $referer):
			$name = $referer->get_name();
			switch($name):
				case 'auxparam':
					if(array_key_exists($name,$source)):
						if(is_array($source[$name])):
							$source[$name] = implode(PHP_EOL,$source[$name]);
						endif;
					endif;
					break;
			endswitch;
			$sphere->row[$name] = $referer->validate_array_element($source);
			if(is_null($sphere->row[$name])):
				if(array_key_exists($name,$source) && is_scalar($source[$name])): 
					switch($page_action):
						case 'enable':
							$input_errors[] = $referer->get_message_error();
							break;
					endswitch;
					$sphere->row[$name] = $source[$name];
				else:
					$sphere->row[$name] = $referer->get_defaultvalue();
				endif;
			endif;
		endforeach;
		break;
	case 'save':
		$source = $_POST;
		foreach($a_referer as $referer):
			$name = $referer->get_name();
			$sphere->row[$name] = $referer->validate_input();
			if(is_null($sphere->row[$name])):
				$input_errors[] = $referer->get_message_error();
				if(array_key_exists($name,$source) && is_scalar($source[$name])): 
					$sphere->row[$name] = $source[$name];
				else:
					$sphere->row[$name] = $referer->get_defaultvalue();
				endif;
			endif;
		endforeach;
		break;
endswitch;
//	reclassify
switch($page_action):
	case 'enable':
		$name = $cop->get_enable()->get_name();
		if($sphere->row[$name]):
			$page_action = 'view';
			$page_mode = PAGE_MODE_VIEW;
		else:
			$sphere->row[$name] = true;
			$page_action = 'save';
			$page_mode = PAGE_MODE_POST;
		endif;
		break;
	case 'disable':
		$name = $cop->get_enable()->get_name();
		if($sphere->row[$name]):
			$sphere->row[$name] = false;
			$page_action = 'save';
			$page_mode = PAGE_MODE_POST;
		else:
			$page_action = 'view';
			$page_mode = PAGE_MODE_VIEW;
		endif;
		break;
endswitch;
//	save configuration
switch($page_action):
	case 'reload':
		$retval = 0;
		config_lock();
		$retval |= rc_reload_service_if_running_and_enabled('inadyn');
		config_unlock();
		$_SESSION['submit'] = $sphere->get_basename();
		$_SESSION[$sphere->get_basename()] = $retval;
		header($sphere->get_location());
		exit;
		break;
	case 'restart':
		$retval = 0;
		config_lock();
		$retval |= rc_restart_service_if_running_and_enabled('inadyn');
		config_unlock();
		$_SESSION['submit'] = $sphere->get_basename();
		$_SESSION[$sphere->get_basename()] = $retval;
		header($sphere->get_location());
		exit;
		break;
	case 'save':
		if(empty($input_errors)):
			foreach($a_referer as $referer):
				$name = $referer->get_name();
				switch($name):
					case 'auxparam':
						$auxparam_grid = [];
						foreach(explode(PHP_EOL,$sphere->row[$name]) as $auxparam_row):
							$auxparam_grid[] = trim($auxparam_row,"\t\n\r");
						endforeach;
						$sphere->row[$name] = $auxparam_grid;
						break;
				endswitch;
				$sphere->grid[$name] = $sphere->row[$name];
			endforeach;
			write_config();
			$retval = 0;
			config_lock();
			$retval |= rc_update_reload_service('inadyn');
			config_unlock();
			$_SESSION['submit'] = $sphere->get_basename();
			$_SESSION[$sphere->get_basename()] = $retval;
			header($sphere->get_location());
			exit;
		else:
			$page_mode = PAGE_MODE_EDIT;
		endif;
		break;
endswitch;
//	determine final page mode and calculate readonly flag
list($page_mode,$is_readonly) = calc_skipviewmode($page_mode);
$is_enabled = $sphere->row[$cop->get_enable()->get_name()];
//	create document
$pgtitle = [gettext('Services'),gettext('Dynamic DNS'),gettext('Settings')];
$document = new_page($pgtitle,$sphere->get_scriptname());
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add tab navigation
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('services_dynamicdns.php',gettext('Global Settings'),gettext('Reload page'),true)->
			ins_tabnav_record('services_dynamicdns.php',gettext('Service'),gettext('Service'));
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	ins_input_errors($input_errors)->
	ins_info_box($savemsg);
//	add content
$n_auxparam_rows = min(64,max(5,1 + substr_count($sphere->row[$cop->get_auxparam()->get_name()],PHP_EOL)));
$content->
	add_table_data_settings()->
		ins_colgroup_data_settings()->
		push()->
		addTHEAD()->
			c2_titleline_with_checkbox($cop->get_enable(),$is_enabled,false,$is_readonly,gettext('Dynamic DNS'))->
		pop()->
		addTBODY()->
			c2_input_text($cop->get_debug(),$sphere->row[$cop->get_debug()->get_name()],false,$is_readonly)->
			c2_input_text($cop->get_maxproc(),$sphere->row[$cop->get_maxproc()->get_name()],false,$is_readonly)->
			c2_input_text($cop->get_timeout(),$sphere->row[$cop->get_timeout()->get_name()],false,$is_readonly)->
			c2_input_text($cop->get_isns_period(),$sphere->row[$cop->get_isns_period()->get_name()],false,$is_readonly)->
			c2_input_text($cop->get_isns_timeout(),$sphere->row[$cop->get_isns_timeout()->get_name()],false,$is_readonly)->
			c2_textarea($cop->get_auxparam(),$sphere->row[$cop->get_auxparam()->get_name()],false,$is_readonly,60,$n_auxparam_rows);
//	add buttons
switch($page_mode):
	case PAGE_MODE_VIEW:
		$document->
			add_area_buttons()->
				ins_button_edit()->
				ins_button_enadis(!$is_enabled)->
				ins_button_restart($is_enabled)->
				ins_button_reload($is_enabled);
		break;
	case PAGE_MODE_EDIT:
		$document->
			add_area_buttons()->
				ins_button_save()->
				ins_button_cancel();
		break;
endswitch;
//	additional javascript code
/*
$js_code = [];
$js_code[PAGE_MODE_VIEW] = '';
$js_code[PAGE_MODE_EDIT] = '';
 */
//	additional javascript code
/*
$js_on_load = [];
$js_on_load[PAGE_MODE_EDIT] = '';
$js_on_load[PAGE_MODE_VIEW] = '';
 */
//	additional javascript code
/*
$js_document_ready = [];
$js_document_ready[PAGE_MODE_EDIT] = '';
$js_document_ready[PAGE_MODE_VIEW] = '';
 */
//	add additional javascript code
/*
$body->addJavaScript($js_code[$page_mode]);
$body->add_js_on_load($js_on_load[$page_mode]);
$body->add_js_document_ready($js_document_ready[$page_mode]);
 */
//	showtime
$document->render();

array_make_branch($config,'dynamicdns');
$pconfig['enable'] = isset($config['dynamicdns']['enable']);
$pconfig['provider'] = !empty($config['dynamicdns']['provider']) ? $config['dynamicdns']['provider'] : "";
$pconfig['domainname'] = !empty($config['dynamicdns']['domainname']) ? $config['dynamicdns']['domainname'] : "";
$pconfig['username'] = !empty($config['dynamicdns']['username']) ? $config['dynamicdns']['username'] : "";
$pconfig['password'] = !empty($config['dynamicdns']['password']) ? $config['dynamicdns']['password'] : "";
$pconfig['updateperiod'] = !empty($config['dynamicdns']['updateperiod']) ? $config['dynamicdns']['updateperiod'] : "";
$pconfig['forcedupdateperiod'] = !empty($config['dynamicdns']['forcedupdateperiod']) ? $config['dynamicdns']['forcedupdateperiod'] : "";
$pconfig['wildcard'] = isset($config['dynamicdns']['wildcard']);
if(isset($config['dynamicdns']['auxparam']) && is_array($config['dynamicdns']['auxparam'])): 
	$pconfig['auxparam'] = implode("\n", $config['dynamicdns']['auxparam']);
endif;
if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	//	input validation
	if(isset($_POST['enable']) && $_POST['enable']):
		$reqdfields = ['provider','domainname','username','password'];
		$reqdfieldsn = [gtext('Provider'),gtext('Domain Name'),gtext('Username'),gtext('Password')];
		do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
		$reqdfields = array_merge($reqdfields, ['updateperiod','forcedupdateperiod']);
		$reqdfieldsn = array_merge($reqdfieldsn, [gtext('Update period'),gtext('Forced Update Period')]);
		$reqdfieldst = ['string','string','string','string','numeric','numeric'];
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);
	endif;
	if(empty($input_errors)):
		$config['dynamicdns']['enable'] = isset($_POST['enable']) ? true : false;
		$config['dynamicdns']['provider'] = $_POST['provider'];
		$config['dynamicdns']['domainname'] = $_POST['domainname'];
		$config['dynamicdns']['username'] = $_POST['username'];
		$config['dynamicdns']['password'] = $_POST['password'];
		$config['dynamicdns']['updateperiod'] = $_POST['updateperiod'];
		$config['dynamicdns']['forcedupdateperiod'] = $_POST['forcedupdateperiod'];
		$config['dynamicdns']['wildcard'] = isset($_POST['wildcard']) ? true : false;
		//	Write additional parameters.
		unset($config['dynamicdns']['auxparam']);
		foreach(explode(PHP_EOL,$_POST['auxparam']) as $auxparam):
			$auxparam = trim($auxparam, "\t\n\r");
			if(!empty($auxparam)):
				$config['dynamicdns']['auxparam'][] = $auxparam;
			endif;
		endforeach;
		write_config();
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			config_lock();
			$retval |= rc_update_service("inadyn");
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
	endif;
endif;
//	Get list of available interfaces.
$a_interface = get_interface_list();
$l_provider = [
	'dyndns.org' => 'dyndns.org',
	'freedns.afraid.org' => 'freedns.afraid.org',
	'zoneedit.com' => 'zoneedit.com',
	'no-ip.com' => 'no-ip.com',
	'3322.org' => '3322.org',
	'easydns.com' => 'easydns.com',
	'dnsdynamic.org' => 'dnsdynamic.org',
	'dhis.org' => 'dhis.org',
	'dnsexit.com' => 'dnsexit.com',
	'ipv6tb.he.net' => 'ipv6tb.he.net',
	'tzo.com' => 'tzo.com',
	'dynsip.org' => 'dynsip.org',
	'changeip.com' => 'changeip.com',
	'dy.fi' => 'dy.fi',
	'two-dns.de' => 'two-dns.de',
	'custom' => gettext('Custom')
];
$pgtitle = [gtext('Services'),gtext('Dynamic DNS')];
include 'fbegin.inc';
?>
<script type="text/javascript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.provider.disabled = endis;
	document.iform.domainname.disabled = endis;
	document.iform.username.disabled = endis;
	document.iform.password.disabled = endis;
	document.iform.updateperiod.disabled = endis;
	document.iform.forcedupdateperiod.disabled = endis;
	document.iform.wildcard.disabled = endis;
	document.iform.auxparam.disabled = endis;
}

function provider_change() {
	switch(document.iform.provider.value) {
		case "dyndns.org":
		case "3322.org":
		case "easydns.com":
		case "custom":
			showElementById('wildcard_tr','show');
			break;

		default:
			showElementById('wildcard_tr','hide');
			break;
	}
}
//-->
</script>
<form action="services_dynamicdns.php" method="post" name="iform" id="iform" onsubmit="spinner()">
	<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td class="tabcont">
<?php
		if(!empty($input_errors)):
			print_input_errors($input_errors);
		endif;
		if(!empty($savemsg)):
			print_info_box($savemsg);
		endif;
?>
		<table width="100%" border="0" cellpadding="6" cellspacing="0">
<?php
			html_titleline_checkbox2("enable", gettext("Dynamic DNS"), !empty($pconfig['enable']) ? true : false, gettext("Enable"), "enable_change(false)");
			html_combobox2('provider',gettext('Provider'),$pconfig['provider'],$l_provider,'',true,false,'provider_change()');
			html_inputbox2("domainname", gettext("Domain Name"), $pconfig['domainname'], gettext("A host name alias. This option can appear multiple times, for each domain that has the same IP. Use a space to separate multiple alias names."), true, 40);
			html_inputbox2("username", gettext("Username"), $pconfig['username'], "", true, 20);
			html_passwordbox2("password", gettext("Password"), $pconfig['password'], "", true, 20);
			html_inputbox2("updateperiod", gettext("Update Period"), $pconfig['updateperiod'], gettext("How often the IP is checked. The period is in seconds (max. is 10 days)."), false, 20);
			html_inputbox2("forcedupdateperiod", gettext("Forced Update Period"), $pconfig['forcedupdateperiod'], gettext("How often the IP is updated even if it is not changed. The period is in seconds (max. is 10 days)."), false, 20);
			html_checkbox2("wildcard", gettext("Wildcard"), !empty($pconfig['wildcard']) ? true : false, gettext("Enable domain wildcarding."), "", false);
			html_textarea2("auxparam", gettext("Additional Parameters"), !empty($pconfig['auxparam']) ? $pconfig['auxparam'] : "", sprintf(gettext("These parameters will be added to global settings in %s."), "inadyn.conf"), false, 65, 3, false, false);
?>
		</table>
		<div id="submit">
			<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save & Restart");?>" onclick="enable_change(true)" />
		</div>
	</td></tr></table>
<?php
	include 'formend.inc';
?>
</form>
<script type="text/javascript">
<!--
enable_change(false);
provider_change();
//-->
</script>
<?php
include 'fend.inc';
