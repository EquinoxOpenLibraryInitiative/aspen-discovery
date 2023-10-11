<?php
require_once ROOT_DIR . '/sys/SelfRegistrationForms/SelfRegistrationFormValues.php';

class SelfRegistrationForm extends DataObject {
	public $__table = 'self_registration_form';
	public $__displayNameColumn = 'symphonyName';
	public $id;
	public $name;

	private $_fields;
	private $_libraries;
//	private $citystate;
	private $SMSpromts;
	private $selfRegProfile;

	static function getObjectStructure($context = ''): array {
		$libraryList = Library::getLibraryList(!UserAccount::userHasPermission('Administer All Libraries'));

		$fieldValuesStructure = SelfRegistrationFormValues::getObjectStructure($context);
		unset($fieldValuesStructure['weight']);
		unset($fieldValuesStructure['selfRegistrationFormId']);

		return [
			'id' => [
				'property' => 'id',
				'type' => 'label',
				'label' => 'Id',
				'description' => 'The unique id within the database',
			],
			'name' => [
				'property' => 'name',
				'type' => 'text',
				'label' => 'Display Name',
				'description' => 'The name of the settings',
				'size' => '40',
				'maxLength' => 255,
			],
			'fields' => [
				'property' => 'fields',
				'type' => 'oneToMany',
				'label' => 'Fields',
				'description' => 'The fields for self registration',
				'keyThis' => 'libraryId',
				'keyOther' => 'libraryId',
				'subObjectType' => 'SelfRegistrationFormValues',
				'structure' => $fieldValuesStructure,
				'sortable' => true,
				'storeDb' => true,
				'allowEdit' => true,
				'canEdit' => false,
				'canAddNew' => true,
				'canDelete' => true,
			],
			'promptForSMSNoticesInSelfReg' => [
				'property' => 'promptForSMSNoticesInSelfReg',
				'type' => 'checkbox',
				'label' => 'Prompt For SMS Notices',
				'description' => 'Whether or not SMS Notification information should be requested.',
			],
//			'cityStateField' => [
//				'property' => 'cityStateField',
//				'type' => 'enum',
//				'values' => [
//					0 => 'CITY / STATE field',
//					1 => 'CITY and STATE fields',
//				],
//				'label' => 'City / State Field',
//				'description' => 'The field from which to load and update city and state.',
//				'hideInLists' => true,
//				'default' => 0,
//			],
			'selfRegistrationUserProfile' => [
				'property' => 'selfRegistrationUserProfile',
				'type' => 'text',
				'label' => 'Self Registration Profile',
				'description' => 'The Profile to use during self registration.',
				'hideInLists' => true,
				'default' => 'SELFREG',
			],
			'libraries' => [
				'property' => 'libraries',
				'type' => 'multiSelect',
				'listStyle' => 'checkboxSimple',
				'label' => 'Libraries',
				'description' => 'Define libraries that use this self registration form',
				'values' => $libraryList,
			],
		];
	}
	public function update($context = '') {
		$ret = parent::update();
		if ($ret !== FALSE) {
			$this->saveFields();
			$this->saveLibraries();
		}
		return $ret;
	}

	public function insert($context = '') {
		$ret = parent::insert();
		if ($ret !== FALSE) {
			$this->saveFields();
			$this->saveLibraries();
		}
		return $ret;
	}

	public function __get($name) {
		if ($name == 'fields') {
			return $this->getFields();
		} if ($name == "libraries") {
			return $this->getLibraries();
		}
//		if ($name == "cityStateField") {
//			return $this->getCityStateField();
//		}
		if ($name == "promptForSMSNoticesInSelfReg") {
			return $this->getSMSpromptSetting();
		} if ($name == "selfRegistrationUserProfile") {
			return $this->getSelfRegistrationUserProfile();
		} else{
			return parent::__get($name);
		}
	}

	public function __set($name, $value) {
		if ($name == 'fields') {
			$this->_fields = $value;
		} if ($name == "libraries") {
			$this->_libraries = $value;
		}
//		if ($name == "cityStateField") {
//			$this->citystate = $value;
//		}
		if ($name == "promptForSMSNoticesInSelfReg") {
			$this->SMSpromts = $value;
		} if ($name == "selfRegistrationUserProfile") {
			$this->selfRegProfile = $value;
		} else {
			parent::__set($name, $value);
		}
	}

	/** @return SelfRegistrationFormValues[] */
	public function getFields(): ?array {
		if (!isset($this->_fields) && $this->id) {
			$this->_fields = [];
			$field = new SelfRegistrationFormValues();
			$field->selfRegistrationFormId = $this->id;
			$field->orderBy('weight');
			$field->find();
			while ($field->fetch()) {
				$this->_fields[$field->id] = clone($field);
			}
		}
		return $this->_fields;
	}

	public function clearFields() {
		$this->clearOneToManyOptions('SelfRegistrationFormValues', 'selfRegistrationFormId');
		/** @noinspection PhpUndefinedFieldInspection */
		$this->fields = [];
	}

	public function saveFields() {
		if (isset ($this->_fields) && is_array($this->_fields)) {
			$this->saveOneToManyOptions($this->_fields, 'selfRegistrationFormId');
			unset($this->fields);
		}
	}

//	public function getCityStateField() {
//		if (!isset($this->citystate) && $this->id) {
//			$this->citystate = 0;
//			$library = new Library();
//			$library->selfRegistrationFormId = $this->id;
//			if ($library->find(true)){
//				$library->fetch();
//				$this->citystate = $library->cityStateField;
//			}
//		}
//		return $this->citystate;
//	}

	public function getSMSpromptSetting() {
		if (!isset($this->SMSpromts) && $this->id) {
			$this->SMSpromts = 0;
			$library = new Library();
			$library->selfRegistrationFormId = $this->id;
			if ($library->find(true)){
				$library->fetch();
				$this->SMSpromts = $library->promptForSMSNoticesInSelfReg;
			}
		}
		return $this->SMSpromts;
	}

	public function getSelfRegistrationUserProfile() {
		if (!isset($this->selfRegProfile) && $this->id) {
			$this->selfRegProfile = '';
			$library = new Library();
			$library->selfRegistrationFormId = $this->id;
			if ($library->find(true)){
				$library->fetch();
				$this->selfRegProfile = $library->selfRegistrationUserProfile;
			}
		}
		return $this->selfRegProfile;
	}

	public function getLibraries() {
		if (!isset($this->_libraries) && $this->id) {
			$this->_libraries = [];
			$library = new Library();
			$library->selfRegistrationFormId = $this->id;
			$library->find();
			while ($library->fetch()) {
				$this->_libraries[$library->libraryId] = $library->libraryId;
			}
		}
		return $this->_libraries;
	}

	public function saveLibraries() {
		if (isset($this->_libraries) && is_array($this->_libraries)) {
			$libraryList = Library::getLibraryList(!UserAccount::userHasPermission('Administer All Libraries'));

			foreach ($libraryList as $libraryId => $displayName) {
				$library = new Library();
				$library->libraryId = $libraryId;
				$library->find(true);
				if (in_array($libraryId, $this->_libraries)) {
					//only update libraries in _libraries - unselected libraries will not have any fields other than selfRegistrationFormId updated
					if (($library->selfRegistrationFormId != $this->id) /*|| ($library->cityStateField != $this->citystate) */|| ($library->promptForSMSNoticesInSelfReg != $this->SMSpromts) || ($library->selfRegistrationUserProfile != $this->selfRegProfile)) {
						$library->selfRegistrationFormId = $this->id;
//						$library->cityStateField = $this->citystate;
						$library->promptForSMSNoticesInSelfReg = $this->SMSpromts;
						$library->selfRegistrationUserProfile = $this->selfRegProfile;
						$library->update();
					}
				} else {
					if ($library->selfRegistrationFormId == $this->id) {
						$library->selfRegistrationFormId = -1;
						$library->update();
					}
				}
			}
			unset($this->_libraries);
		}
	}
}