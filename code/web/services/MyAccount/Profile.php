<?php

require_once ROOT_DIR . '/services/MyAccount/MyAccount.php';

class MyAccount_Profile extends MyAccount
{
	function launch()
	{
		global $configArray;
		global $interface;
		$user = UserAccount::getLoggedInUser();

		$ils = $configArray['Catalog']['ils'];
		$smsEnabled = $configArray['Catalog']['smsEnabled'];
		$interface->assign('showSMSNoticesInProfile', $ils == 'Sierra' && $smsEnabled == true);

		if ($user) {
			// Determine which user we are showing/updating settings for
			$linkedUsers = $user->getLinkedUsers();

			$patronId    = isset($_REQUEST['patronId']) ? $_REQUEST['patronId'] : $user->id;
			/** @var User $patron */
			$patron      = $user->getUserReferredTo($patronId);

			// Linked Accounts Selection Form set-up
			if (count($linkedUsers) > 0) {
				array_unshift($linkedUsers, $user); // Adds primary account to list for display in account selector
				$interface->assign('linkedUsers', $linkedUsers);
				$interface->assign('selectedUser', $patronId);
			}

			$patronUpdateForm = $patron->getPatronUpdateForm();
			if ($patronUpdateForm != null){
				$interface->assign('patronUpdateForm', $patronUpdateForm);
			}

			/** @var Library $librarySingleton */
			global $librarySingleton;
			// Get Library Settings from the home library of the current user-account being displayed
			$patronHomeLibrary = $librarySingleton->getPatronHomeLibrary($patron);
			if ($patronHomeLibrary == null){
				$canUpdateContactInfo = false;
				$canUpdateAddress = false;
				$canUpdatePhoneNumber = false;
				$showWorkPhoneInProfile = false;
				$showNoticeTypeInProfile = false;
				$showPickupLocationInProfile = false;
				$treatPrintNoticesAsPhoneNotices = false;
				$allowPinReset = false;
				$showAlternateLibraryOptionsInProfile = false;
				$allowAccountLinking = true;
				$passwordLabel = 'Library Card Number';
			}else{
				$canUpdateContactInfo = ($patronHomeLibrary->allowProfileUpdates == 1);
				$canUpdateAddress = ($patronHomeLibrary->allowPatronAddressUpdates == 1);
				$canUpdatePhoneNumber = ($patronHomeLibrary->allowPatronPhoneNumberUpdates == 1);
				$showWorkPhoneInProfile = ($patronHomeLibrary->showWorkPhoneInProfile == 1);
				$showNoticeTypeInProfile = ($patronHomeLibrary->showNoticeTypeInProfile == 1);
				$treatPrintNoticesAsPhoneNotices = ($patronHomeLibrary->treatPrintNoticesAsPhoneNotices == 1);
				$showPickupLocationInProfile = ($patronHomeLibrary->showPickupLocationInProfile == 1);
				$allowPinReset = ($patronHomeLibrary->allowPinReset == 1);
				$showAlternateLibraryOptionsInProfile = ($patronHomeLibrary->showAlternateLibraryOptionsInProfile == 1);
				$allowAccountLinking = ($patronHomeLibrary->allowLinkedAccounts == 1);
				if (($user->_finesVal > $patronHomeLibrary->maxFinesToAllowAccountUpdates) && ($patronHomeLibrary->maxFinesToAllowAccountUpdates > 0)){
					$canUpdateContactInfo = false;
					$canUpdateAddress = false;
				}
				$passwordLabel = str_replace('Your', '', $patronHomeLibrary->loginFormPasswordLabel ? $patronHomeLibrary->loginFormPasswordLabel : 'Library Card Number');
			}

			$interface->assign('canUpdateContactInfo', $canUpdateContactInfo);
			$interface->assign('canUpdateAddress', $canUpdateAddress);
			$interface->assign('canUpdatePhoneNumber', $canUpdatePhoneNumber);
			$interface->assign('showWorkPhoneInProfile', $showWorkPhoneInProfile);
			$interface->assign('showPickupLocationInProfile', $showPickupLocationInProfile);
			$interface->assign('showNoticeTypeInProfile', $showNoticeTypeInProfile);
			$interface->assign('treatPrintNoticesAsPhoneNotices', $treatPrintNoticesAsPhoneNotices);
			$interface->assign('allowPinReset', $allowPinReset);
			$interface->assign('showAlternateLibraryOptions', $showAlternateLibraryOptionsInProfile);
			$interface->assign('allowAccountLinking', $allowAccountLinking);
			$interface->assign('passwordLabel', $passwordLabel);

			// Determine Pickup Locations
			$pickupLocations = $patron->getValidPickupBranches($patron->getAccountProfile()->recordSource);
			$interface->assign('pickupLocations', $pickupLocations);

			// Save/Update Actions
			global $offlineMode;
			if (isset($_POST['updateScope']) && !$offlineMode) {
				$updateScope = $_REQUEST['updateScope'];
				if ($updateScope == 'contact') {
					$result = $patron->updatePatronInfo($canUpdateContactInfo);
					$user->updateMessage = implode('<br/>', $result['messages']);
					$user->updateMessageIsError = !$result['success'];
					$user->update();

				}  elseif ($updateScope == 'userPreference') {
					$patron->updateUserPreferences();
				}  elseif ($updateScope == 'staffSettings') {
					$patron->updateStaffSettings();
				} elseif ($updateScope == 'overdrive') {
					$patron->updateOverDriveOptions();
				} elseif ($updateScope == 'hoopla') {
					$patron->updateHooplaOptions();
				} elseif ($updateScope == 'pin') {
					$result = $patron->updatePin();
					$user->updateMessage = $result['message'];
					$user->updateMessageIsError = !$result['success'];
					$user->update();

				}

				session_write_close();
				$actionUrl = '/MyAccount/Profile' . ( $patronId == $user->id ? '' : '?patronId='.$patronId ); // redirect after form submit completion
				header("Location: " . $actionUrl);
				exit();
			} elseif (!$offlineMode) {
				$interface->assign('edit', true);
			} else {
				$interface->assign('edit', false);
			}

			global $translator;
			global $library;
			$notice         = $translator->translate('overdrive_account_preferences_notice');
			require_once ROOT_DIR . '/sys/OverDrive/OverDriveScope.php';
			$overDriveScope = new OverDriveScope();
			$overDriveScope->id = $library->overDriveScopeId;
			if ($overDriveScope->find(true)){
				require_once ROOT_DIR . '/sys/OverDrive/OverDriveSetting.php';
				$overDriveSettings = new OverDriveSetting();
				$overDriveSettings->id = $overDriveScope->settingId;
				if ($overDriveSettings->find(true)) {
					$overDriveUrl = $overDriveSettings->url;
					$replacementUrl = empty($overDriveUrl) ? '#' : $overDriveUrl;
					$notice = str_replace('{OVERDRIVEURL}', $replacementUrl, $notice); // Insert the Overdrive URL into the notice
				}
			}
			$interface->assign('overdrivePreferencesNotice', $notice);

			if (!empty($user->updateMessage)) {
				if ($user->updateMessageIsError){
					$interface->assign('profileUpdateErrors', $user->updateMessage);
				}else{
					$interface->assign('profileUpdateMessage', $user->updateMessage);
				}
				$user->updateMessage = '';
				$user->update();
			}

			if ($showAlternateLibraryOptionsInProfile) {
				//Get the list of locations for display in the user interface.

				$locationList = array();
				$locationList['0'] = "No Alternate Location Selected";
				foreach ($pickupLocations as $pickupLocation){
					if (!is_string($pickupLocation)){
						$locationList[$pickupLocation->locationId] = $pickupLocation->displayName;
					}
				}
				$interface->assign('locationList', $locationList);
			}

			$interface->assign('profile', $patron);
			$interface->assign('barcodePin', $patron->getAccountProfile()->loginConfiguration == 'barcode_pin');
				// Switch for displaying the barcode in the account profile

		}

		// switch for hack for Millennium driver profile updating when updating is allowed but address updating is not allowed.
		$millenniumNoAddress = $canUpdateContactInfo && !$canUpdateAddress && in_array($ils, array('Millennium', 'Sierra'));
		$interface->assign('millenniumNoAddress', $millenniumNoAddress);

		$catalog = CatalogFactory::getCatalogConnectionInstance();
		$pinValidationRules = $catalog->getPasswordPinValidationRules();
		$interface->assign('pinValidationRules', $pinValidationRules);

		// CarlX Specific Options
		if ($ils == 'CarlX' && !$offlineMode) {
			// Get Phone Types
			$phoneTypes = array();
			/** @var CarlX $driver */
			$driver        = $catalog->driver;
			$rawPhoneTypes = $driver->getPhoneTypeList();
			foreach ($rawPhoneTypes as $rawPhoneTypeSubArray){
				foreach ($rawPhoneTypeSubArray as $phoneType => $phoneTypeLabel) {
					$phoneTypes["$phoneType"] = $phoneTypeLabel;
				}
			}
			$interface->assign('phoneTypes', $phoneTypes);
		}

		$this->display('profile.tpl', 'Account Settings');
	}

	function getBreadcrumbs()
	{
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/MyAccount/Home', 'My Account');
		$breadcrumbs[] = new Breadcrumb('', 'Account Settings');
		return $breadcrumbs;
	}

}