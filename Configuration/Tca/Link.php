<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}


$TCA['tx_amazingshortlinks_domain_model_link'] = array(
	'ctrl' => $TCA['tx_amazingshortlinks_domain_model_link']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,createdon,lastupdated,starttime,endtime,domainrecord,shortpath'
	),
	'feInterface' => $TCA['tx_amazingshortlinks_domain_model_link']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => 0
			)
		),
		'cruser_id' => array(
			'label'   => 'cruser_id',
			'config'  => array(
				'type'    => 'passthrough'
			)
		),
		'pid' => array(
			'label'   => 'pid',
			'config'  => array(
				'type'    => 'passthrough'
			)
		),
		'createdon' => array(
			'label'   => 'Created on',
			'config'  => array(
				'type'     => 'input',
				'size'     => 8,
				'max'      => 20,
				'eval'     => 'date',
				'default'  => 0,
			)
		),
		'lastupdated' => array(
			'label'   => 'lastupdated',
			'config'  => array(
				'type'     => 'passthrough',
			)
		),
		'starttime' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:cms/locallang_ttc.xml:starttime_formlabel',
			'config'  => array(
				'type'     => 'input',
				'size'     => 8,
				'max'      => 20,
				'eval'     => 'date',
				'default'  => 0,
			)
		),
		'endtime' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:cms/locallang_ttc.xml:endtime_formlabel',
			'config'  => array(
				'type'     => 'input',
				'size'     => 8,
				'max'      => 20,
				'eval'     => 'date',
				'default'  => 0,
			)
		),
		'domainrecord' => array(
			'label' => 'LLL:EXT:amazingshortlinks/Resources/Private/Language/db.xlf:tx_amazingshortlinks_domain_model_link.domainrecord',
			'config' => array(
				'type' => 'select',
				'items' => array(),
				'foreign_table' => 'sys_domain',
				'foreign_table_where' => ' AND ORDER BY tx_news_domain_model_category.domainName',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
		'shortpath' => array(
			'label' => 'LLL:EXT:amazingshortlinks/Resources/Private/Language/db.xlf:tx_amazingshortlinks_domain_model_link.shortpath',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'required',
			)
		),
		'destination' => array(
			'label' => 'LLL:EXT:amazingshortlinks/Resources/Private/Language/db.xlf:tx_amazingshortlinks_domain_model_link.destination',
			'config' => array(
				'type' => 'input',
				'size' => 80,
				'eval' => 'required',
			)
		),
		'totalhits' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:amazingshortlinks/Resources/Private/Language/db.xlf:tx_amazingshortlinks_domain_model_link.totalhits',
			'config'  => array(
				'type'     => 'input',
				'size'     => 8,
				'default'  => 0,
			)
		),
		'lasthit' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:amazingshortlinks/Resources/Private/Language/db.xlf:tx_amazingshortlinks_domain_model_link.lasthit',
			'config'  => array(
				'type'     => 'input',
				'size'     => 8,
				'max'      => 20,
				'eval'     => 'datetime',
				'default'  => 0,
			)
		)
	),
	'types' => array(
		'0' => array(
			'showitem' => '
				--palette--;;paletteLink, destination, totalhits, lasthit,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access, hidden, --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;paletteAccess'
		),
	),
	'palettes' => array(
		'paletteLink' => array(
			'showitem' => 'domainrecord, shortpath',
			'canNotCollapse' => TRUE,
		),
		'paletteAccess' => array(
			'showitem' => 'starttime;LLL:EXT:cms/locallang_ttc.xml:starttime_formlabel,
					endtime;LLL:EXT:cms/locallang_ttc.xml:endtime_formlabel',
			'canNotCollapse' => TRUE,
		),
	)
);