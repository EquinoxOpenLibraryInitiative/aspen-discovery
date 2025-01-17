<?php
/** @noinspection PhpUnused */
function getUpdates22_12_00(): array {
	$curTime = time();
	return [
		/*'name' => [
			'title' => '',
			'description' => '',
			'sql' => [
				''
			]
		], //sample*/

		//mark
		'custom_form_includeIntroductoryTextInEmail' => [
			'title' => 'Custom Form - includeIntroductoryTextInEmail',
			'description' => 'Allow introductory text to be included in the response email',
			'sql' => [
				'ALTER TABLE web_builder_custom_form ADD COLUMN includeIntroductoryTextInEmail TINYINT(1) default 0',
			],
		],
		//custom_form_includeIntroductoryTextInEmail
		'aspen_release_test_release_date' => [
			'title' => 'Aspen Release - add release date to test',
			'description' => 'Aspen Release - add release date to test',
			'continueOnError' => true,
			'sql' => [
				'ALTER TABLE aspen_release ADD COLUMN releaseDateTest DATE',
				'ALTER TABLE aspen_release CHANGE COLUMN releaseDate releaseDate DATE',
			],
		],
		//aspen_release_test_release_date
		'development_sprints' => [
			'title' => 'Development - Create Sprints',
			'description' => 'Development - Create Sprints',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS development_sprint (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					name VARCHAR(255) NOT NULL UNIQUE,
					startDate DATE,
					endDate DATE,
					active TINYINT(1) DEFAULT 1
				) ENGINE INNODB',
			],
		],
		//development_sprints
		'development_tasks_take_2' => [
			'title' => 'Development - Create Development Tasks',
			'description' => 'Development - Create Development Tasks',
			'sql' => [
				'DROP TABLE IF EXISTS development_task',
				'CREATE TABLE IF NOT EXISTS development_task (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					taskType INT(11) DEFAULT 0,
					name VARCHAR(255) NOT NULL UNIQUE,
					dueDate CHAR(10),
					dueDateComment VARCHAR(255),
					description MEDIUMTEXT,
					releaseId INT(11) DEFAULT 0,
					status INT(11) DEFAULT 0,
					storyPoints float,
					devTestingNotes MEDIUMTEXT,
					qaFeedback MEDIUMTEXT,
					releaseNoteText TEXT,
					newSettingsAdded TEXT,
					suggestedForCommunityDev TINYINT(1) DEFAULT 0
				) ENGINE INNODB',
			],
		],
		//development_tasks_take_2
		'development_tickets_to_tasks' => [
			'title' => 'Development - Link Tickets To Tasks',
			'description' => 'Development - Link Tickets To Tasks',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS development_task_ticket_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					ticketId INT(11), 
					taskId INT(11), 
					UNIQUE INDEX (ticketId, taskId)
				) ENGINE INNODB',
			],
		],
		//development_tickets_to_tasks
		'development_epics' => [
			'title' => 'Development - Create Epics',
			'description' => 'Development - Create Epics',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS development_epic (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					name VARCHAR(255) NOT NULL UNIQUE,
					description MEDIUMTEXT,
					linkToDesign VARCHAR(255),
					linkToRequirements VARCHAR(255),
					internalComments MEDIUMTEXT,
					dueDate CHAR(10),
					dueDateComment VARCHAR(255),
					privateStatus INT(11) DEFAULT 0
				) ENGINE INNODB',
			],
		],
		//development_epics
		'development_sprints_to_tasks' => [
			'title' => 'Development - Link Sprints To Tasks',
			'description' => 'Development - Link Sprints To Tasks',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS development_task_sprint_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					sprintId INT(11), 
					taskId INT(11), 
					weight INT NOT NULL DEFAULT 0, 
					UNIQUE INDEX (sprintId, taskId),
					INDEX (sprintId, weight)
				) ENGINE INNODB',
			],
		],
		//development_sprints_to_tasks
		'development_partners_to_tasks' => [
			'title' => 'Development - Link Partners To Tasks',
			'description' => 'Development - Link Partners To Tasks',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS development_task_partner_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					partnerId INT(11), 
					taskId INT(11), 
					UNIQUE INDEX (partnerId, taskId)
				) ENGINE INNODB',
			],
		],
		//development_partners_to_tasks
		'development_partners_to_epics' => [
			'title' => 'Development - Link Partners To Epics',
			'description' => 'Development - Link Partners To Epics',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS development_epic_partner_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					partnerId INT(11), 
					epicId INT(11), 
					UNIQUE INDEX (partnerId, epicId)
				) ENGINE INNODB',
			],
		],
		//development_partners_to_epics
		'development_epics_to_tasks' => [
			'title' => 'Development - Link Epics To Tasks',
			'description' => 'Development - Link Epics To Tasks',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS development_task_epic_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					epicId INT(11), 
					taskId INT(11), 
					weight INT NOT NULL DEFAULT 0, 
					UNIQUE INDEX (epicId, taskId),
					INDEX (epicId, weight)
				) ENGINE INNODB',
			],
		],
		//development_epics_to_tasks
		'development_tickets_to_components' => [
			'title' => 'Development - Link Tickets to Components',
			'description' => 'Development - Link Tickets to Components',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS component_ticket_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					ticketId INT(11), 
					componentId INT(11), 
					UNIQUE INDEX (ticketId, componentId)
				) ENGINE INNODB',
			],
		],
		//development_tickets_to_components
		'development_components_to_tasks' => [
			'title' => 'Development - Link Components To Tasks',
			'description' => 'Development - Link Components To Tasks',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS component_development_task_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					componentId INT(11), 
					taskId INT(11), 
					weight INT NOT NULL DEFAULT 0, 
					UNIQUE INDEX (componentId, taskId),
					INDEX (componentId, weight)
				) ENGINE INNODB',
			],
		],
		//development_components_to_tasks
		'development_components_to_epics' => [
			'title' => 'Development - Link Components To Epics',
			'description' => 'Development - Link Components To To Epics',
			'sql' => [
				'CREATE TABLE IF NOT EXISTS component_development_epic_link (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					componentId INT(11), 
					epicId INT(11), 
					UNIQUE INDEX (componentId, epicId)
				) ENGINE INNODB',
			],
		],
		//development_components_to_epics
		'library_lists_without_editable_text' => [
			'title' => 'Library - Allow Lists to not have editable text',
			'description' => 'Library - Allow Lists to not have editable text',
			'sql' => [
				'ALTER TABLE library ADD COLUMN enableListDescriptions TINYINT(1) default 1',
				"ALTER TABLE library ADD COLUMN allowableListNames VARCHAR(500) default ''",
			],
		],
		//library_lists_without_editable_text
		'greenhouse_rt_auth_token' => [
			'title' => 'Greenhouse Token - Add RT Auth Token',
			'description' => 'Add an RT Auth Token for better querying of Request Tracker',
			'sql' => [
				'ALTER TABLE greenhouse_settings ADD COLUMN requestTrackerAuthToken VARCHAR(50)',
			],
		],
		//greenhouse_rt_auth_token
		'greenhouse_rt_base_url' => [
			'title' => 'Greenhouse Token - Add RT Base URL',
			'description' => 'Add the Base URL to connect to RT',
			'sql' => [
				'ALTER TABLE greenhouse_settings ADD COLUMN requestTrackerBaseUrl VARCHAR(100)',
			],
		],
		//greenhouse_rt_base_url
		'author_authorities_index' => [
			'title' => 'Author Authorities Index',
			'description' => 'Add a new index for author authorities',
			'sql' => [
				'ALTER TABLE author_authority_alternative ADD INDEX(normalized)',
			],
		],
		//author_authorities_index
		'library_CityStateField' => [
			'title' => 'Library - City State Field',
			'description' => 'Determine how to load city and state ',
			'sql' => [
				'ALTER TABLE library ADD COLUMN cityStateField TINYINT DEFAULT 0',
			],
		],
		//library_CityStateField

		//kirstien
		'add_oauth_logout' => [
			'title' => 'Add custom OAuth gateway logout URL',
			'description' => 'Add custom OAuth gateway logout URL',
			'sql' => [
				'ALTER TABLE sso_setting ADD COLUMN oAuthLogoutUrl VARCHAR(255)',
			],
		],
		//add_oauth_logout
		'add_oauth_to_user' => [
			'title' => 'Add OAuth tokens to user table',
			'description' => 'Add columns to store OAuth access and refresh tokens in the user table',
			'sql' => [
				'ALTER TABLE user ADD COLUMN oAuthAccessToken VARCHAR(255)',
				'ALTER TABLE user ADD COLUMN oAuthRefreshToken VARCHAR(255)',
			],
		],
		//add_oauth_to_user
		'add_oauth_grant_type' => [
			'title' => 'Add custom OAuth grant type',
			'description' => 'Add custom OAuth grant type',
			'sql' => [
				'ALTER TABLE sso_setting ADD COLUMN oAuthGrantType TINYINT(1) DEFAULT 0',
			],
		],
		//add_oauth_grant_type
		'add_oauth_private_keys' => [
			'title' => 'Add custom OAuth private keys',
			'description' => 'Add custom OAuth private keys for authentication by client credentials',
			'sql' => [
				'ALTER TABLE sso_setting ADD COLUMN oAuthPrivateKeys VARCHAR(255)',
			],
		],
		//add_oauth_private_keys

		//kodi
		'user_disableAccountLinking' => [
			'title' => 'User Disable Account Linking',
			'description' => 'Adds switch for the user to disable account linking',
			'sql' => [
				"ALTER TABLE user ADD COLUMN disableAccountLinking TINYINT(1) DEFAULT '0'",
			],
		],
		//user_disableAccountLinking
		'disable_linking_changes' => [
			'title' => 'Remove Old Account Linking Functionality',
			'description' => 'Remove linkingDisabled column',
			'sql' => [
				"ALTER TABLE user_link DROP COLUMN linkingDisabled",
			],
		],
		//disable_linking_changes
		'user_message_addendum' => [
			'title' => 'Add actions to user messaging',
			'description' => 'Adds addendum for certain messages',
			'sql' => [
				'ALTER TABLE user_messages ADD COLUMN addendum VARCHAR(255)',
			],
		],
		//user_message_addendum
		'records_to_exclude_increase_length_to_400' => [
			'title' => 'Increase the length of records to exclude',
			'description' => 'Make records to exclude fields longer',
			'sql' => [
				"ALTER TABLE library_records_owned CHANGE COLUMN locationsToExclude locationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
				"ALTER TABLE location_records_owned CHANGE COLUMN locationsToExclude locationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
				"ALTER TABLE library_records_to_include CHANGE COLUMN locationsToExclude locationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
				"ALTER TABLE location_records_to_include CHANGE COLUMN locationsToExclude locationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
				"ALTER TABLE library_records_owned CHANGE COLUMN subLocationsToExclude subLocationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
				"ALTER TABLE location_records_owned CHANGE COLUMN subLocationsToExclude subLocationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
				"ALTER TABLE library_records_to_include CHANGE COLUMN subLocationsToExclude subLocationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
				"ALTER TABLE location_records_to_include CHANGE COLUMN subLocationsToExclude subLocationsToExclude VARCHAR(400) NOT NULL DEFAULT ''",
			],
		],
		//records_to_exclude_increase_length_to_400

		//other
	];
}