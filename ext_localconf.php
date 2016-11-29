<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Nitsan.' . $_EXTKEY,
	'Timeline',
	array(
		'SocialTimeline' => 'list',
		
	),
	// non-cacheable actions
	array(
		'SocialTimeline' => 'list',
		
	)
);
