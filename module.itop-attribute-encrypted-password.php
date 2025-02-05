<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'itop-attribute-encrypted-password/1.0.0',
	[
		// Identification
		//
		'label' => 'Encrypted password iTop attribute',
		'category' => 'business',

		// Setup
		//
		'dependencies' => [
			'itop-structure/3.1.0',
		],
		'mandatory' => false,
		'visible' => false,

		// Components
		//
		'datamodel' => [
			'vendor/autoload.php',
			'src/Model/CMDBChangeOpSetAttributeEncryptedPassword.php',
			'src/Attribute/AttributeEncryptedPassword.php',
			'model.itop-attribute-encrypted-password.php', // Contains the PHP code generated by the "compilation" of datamodel.itop-attribute-encrypted-password.xml
		],
		'webservice' => [],
		'data.struct' => [
			// add your 'structure' definition XML files here,
		],
		'data.sample' => [
			// add your sample data XML files here,
		],

		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any

		// Default settings
		//
		'settings' => [
			// Module specific settings go here, if any
		],
	]
);


