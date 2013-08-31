<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// extend the sys_domain records
t3lib_div::loadTCA('sys_domain');
$additionalColumns = array(
	'tx_amazingshortlinks_enable' => array(
		'label' => 'LLL:EXT:amazingshortlinks/Resources/Private/Language/Database.xlf:sys_domain.tx_amazingshortlinks_enable',
		'exclude' => 1,
		'config' => array (
			'type' => 'check',
			'items' => array(
				array('', '')
			)
		)
	)
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_domain', $additionalColumns, TRUE);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_domain', 'tx_amazingshortlinks_enable', '', '');


// add short links DB table
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_amazingshortlinks_domain_model_link');

$TCA['tx_amazingshortlinks_domain_model_link'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:amazingshortlinks/Resources/Private/Language/Database.xlf:tx_amazingshortlinks_domain_model_link',
		'label'     => 'shortlink',
		'label_alt' => 'domainrecord',
		'label_alt_force' => 1,
		'rootLevel' => -1,	// allow on root level and anywhere else
		'tstamp'    => 'lastupdated',
		'crdate'    => 'createdon',
		'cruser_id' => 'createdby',
		'dividers2tabs' => TRUE,
		'default_sortby' => 'ORDER BY domainrecord ASC, shortlink ASC',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled'  => 'hidden',
			'starttime' => 'starttime',
			'endtime'   => 'endtime'
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Tca/Link.php',
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/link.gif',
		'searchFields' => 'shortlink,domainrecord,destination',
	),
);






// Register backend module, but not in frontend or within upgrade wizards
if (TYPO3_MODE === 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {

	// Module "Web"->"Short Links"
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'B13.' . $_EXTKEY,
		'web',
		'shortlinkmanagement',
		'',
		array(
			'Management' => 'index,add,update,remove',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:amazingshortlinks/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/ModuleShortlinkmanagement.xlf',
		)
	);
}
?>