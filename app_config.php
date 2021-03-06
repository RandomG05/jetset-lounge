<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*General Info*/
$config['program_name']			=	'SPOS';
$config['program_version']		=	'3.42.22';
$config['program_release']		=	'2021';
$config['program_name_short'] 	=	'SPOS';
$config['copyright']			=	$config['program_name_short'].' v'.$config['program_version'].' &copy copyright '.$config['program_release'];
$config['client_name']			=	'WePOS.Client';
$config['program_author']		=	'WePOS.Dev';
$config['website']				=	'https://wepos.id';

/*Common config*/
$config['db_prefix']	= 'apps_';
$config['db_prefix2']	= 'pos_';
$config['db_prefix3']	= 'acc_';

$config['timezone_default']	= 'Asia/Jakarta';
$config['wepos_crt_file']	= 'cacert.pem';
