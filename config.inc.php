<?php

$REX['ADDON']['page']['themes'] = 'SiteClone';
$REX['ADDON']['version']['themes'] = '1.0.0';
$REX['ADDON']['author']['themes'] = 'RexDude';
$REX['ADDON']['supportpage']['themes'] = 'forum.redaxo.de';

// add lang file
if ($REX['REDAXO']) {
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/website_manager/plugins/themes/lang/');
}

// includes
require($REX['INCLUDE_PATH'] . '/addons/website_manager/plugins/siteclone/classes/class.rex_siteclone_utils.inc.php');

// default settings (user settings are saved in data dir!)
$REX['ADDON']['siteclone']['settings'] = array(
	'clone_structure' => true,
	'clone_slices' => false,
	'clone_media' => false,
	'clone_strings' => false,
	'empty_strings' => false,
	'copy_file' => '',
	'source_webiste_id' => 1
);

// overwrite default settings with user settings
rex_siteclone_utils::includeSettingsFile();


rex_register_extension('WEBSITE_AFTER_CREATED', 'rex_siteclone_utils::websiteCreated');
