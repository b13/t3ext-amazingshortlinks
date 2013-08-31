<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// hook into the "determine page ID" process in the beginning
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']['tx_amazingshortlinks'] = 'B13\\Amazingshortlinks\\Hook\\FrontendControllerHook->checkForShortlink';
