<?php
/** @noinspection PhpUnused */
function getUpdates23_05_00(): array {
	$curTime = time();
	return [
		/*'name' => [
			'title' => '',
			'description' => '',
			'continueOnError' => false,
			'sql' => [
				''
			]
		], //sample*/

		'drop_securityId_cp' => [
			'title' => 'Drop securityId from Certified Payments',
			'description' => 'Drop securityId from Certified Payments Settings table',
			'sql' => [
				'ALTER TABLE deluxe_certified_payments_settings DROP COLUMN securityId',
			],
		],
		//drop_securityId_cp
		'add_tab_coloring_theme' => [
			'title' => 'Add tab coloring to themes',
			'description' => 'Adds column to specify tab colors in themes',
			'continueOnError' => true,
			'sql' => [
				"ALTER TABLE themes ADD COLUMN inactiveTabBackgroundColor CHAR(7) DEFAULT '#ffffff'",
				'ALTER TABLE themes ADD COLUMN inactiveTabBackgroundColorDefault tinyint(1) DEFAULT 1',
				"ALTER TABLE themes ADD COLUMN inactiveTabForegroundColor CHAR(7) DEFAULT '#6B6B6B'",
				'ALTER TABLE themes ADD COLUMN inactiveTabForegroundColorDefault tinyint(1) DEFAULT 1',
				"ALTER TABLE themes ADD COLUMN activeTabBackgroundColor CHAR(7) DEFAULT '#e7e7e7'",
				'ALTER TABLE themes ADD COLUMN activeTabBackgroundColorDefault tinyint(1) DEFAULT 1',
				"ALTER TABLE themes ADD COLUMN activeTabForegroundColor CHAR(7) DEFAULT '#333333'",
				'ALTER TABLE themes ADD COLUMN activeTabForegroundColorDefault tinyint(1) DEFAULT 1',
			]
		],
		//add_tab_coloring_theme
		'add_bypass_patron_login' => [
			'title' => 'Add option to bypass local patron login',
			'description' => 'Adds column to bypass local patron login when using a single sign-on service',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE sso_setting ADD COLUMN bypassAspenPatronLogin tinyint(1) DEFAULT 0',
			]
		],
		//add_bypass_patron_login
		'add_aspen_site_scheduled_update' => [
			'title' => 'Add table to store scheduled updates',
			'description' => 'Create a table to store scheduled system updates',
			'continueOnError' => true,
			'sql' => [
				'CREATE TABLE IF NOT EXISTS aspen_site_scheduled_update (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					dateScheduled INT(11) DEFAULT NULL,
					updateToVersion VARCHAR(32) DEFAULT NULL,
					updateType VARCHAR(10) DEFAULT NULL,
					dateRun INT(11) DEFAULT NULL,
					status VARCHAR(10) DEFAULT NULL,
					notes VARCHAR(255) DEFAULT NULL,
					siteId INT(11) DEFAULT NULL
				) ENGINE INNODB',
			],
		],
		//add_aspen_site_scheduled_update
		'allow_long_scheduled_update_notes' => [
			'title' => 'Allow long scheduled update notes',
			'description' => 'Allow long scheduled update notes',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE aspen_site_scheduled_update CHANGE column notes notes TEXT'
			],
		],
		//allow_long_scheduled_update_notes
		'add_opt_out_batch_updates' => [
			'title' => 'Add option opt out of batch scheduled updates',
			'description' => 'Adds column to opt-out of batch scheduled updates for an Aspen site',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE aspen_sites ADD COLUMN optOutBatchUpdates tinyint(1) DEFAULT 0',
			]
		],
		//add_opt_out_batch_updates
		'update_dates_scheduled_updates' => [
			'title' => 'Change column types for dates in Scheduled Updates',
			'description' => 'Changes column type and extends for date fields in aspen_site_scheduled_update',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE aspen_site_scheduled_update MODIFY COLUMN dateScheduled VARCHAR(16)',
				'ALTER TABLE aspen_site_scheduled_update MODIFY COLUMN dateRun VARCHAR(16)',
				'ALTER TABLE aspen_site_scheduled_update MODIFY COLUMN status VARCHAR(10) DEFAULT "pending"',
			]
		],
		'update_dates_scheduled_updates2' => [
			'title' => 'Set dates for Scheduled Updates back to timestamps',
			'description' => 'Changes column type and extends for date fields in aspen_site_scheduled_update',
			'continueOnError' => true,
			'sql' => [
				'TRUNCATE TABLE aspen_site_scheduled_update',
				'ALTER TABLE aspen_site_scheduled_update MODIFY COLUMN dateScheduled INT(11) DEFAULT 0',
				'ALTER TABLE aspen_site_scheduled_update MODIFY COLUMN dateRun INT(11) DEFAULT 0',
			]
		],
		//update_dates_scheduled_updates
		'add_greenhouse_id_scheduled_update' => [
			'title' => 'Add greenhouseId to aspen_site_scheduled_update',
			'description' => 'Adds column to store the greenhouse id for an off-site scheduled update',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE aspen_site_scheduled_update ADD COLUMN greenhouseId INT(11) DEFAULT NULL',
			]
		],
		//add_greenhouse_id_scheduled_update
		'user_events_registrations' => [
			'title' => 'User Event Registration Data',
			'description' => 'Setup table to store event registration data for patrons',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS user_events_registrations (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					userId INT(11) NOT NULL,
					userBarcode varchar (256) NOT NULL, 
					sourceId varchar(50) NOT NULL,
					waitlist TINYINT(1) DEFAULT 0,
					UNIQUE (userId, sourceId)
				)',
			],
		],
		//user_events_registrations
	];
}