<?php
require_once ROOT_DIR . '/services/Admin/Admin.php';

class IndexerInformation extends Admin_Admin{
	function launch() {
		global $interface;

		$runningProcesses = $this->loadRunningProcesses();
		if (!empty($_REQUEST['selectedProcesses'])) {

			$stopResults = '';
			$processesToStop = $_REQUEST['selectedProcesses'];
			$stopResults = "Marking " . count($processesToStop) . " processes to be stopped.<br>";
			foreach ($processesToStop as $processId => $value){
				if (array_key_exists($processId, $runningProcesses)) {
					//Add to the list of processes to stop
					require_once ROOT_DIR . '/sys/Greenhouse/ProcessToStop.php';
					$processToStop = new ProcessToStop();
					$processToStop->processId = $processId;
					$processToStop->processName = $runningProcesses[$processId]['name'];
					$processToStop->stopAttempted = 0;
					$processToStop->dateSet = time();
					$processToStop->insert();
				}else{
					$stopResults .= "Process $processId was not found.";
				}
			}

			$user = UserAccount::getActiveUserObj();
			$user->updateMessage = $stopResults;
			$user->update();

			header('Location: /Greenhouse/IndexerInformation');
			die();
		}else{
			$user = UserAccount::getActiveUserObj();
			if (!empty($user->updateMessage)) {
				$interface->assign('stopResults', $user->updateMessage);
				$user->updateMessage = '';
				$user->update();
			}
		}

		require_once ROOT_DIR . '/sys/Greenhouse/ProcessToStop.php';
		$processToStop = new ProcessToStop();
		$processToStop->stopAttempted = 0;
		$processToStop->find();
		$processesToStop = [];
		while ($processToStop->fetch()) {
			if (array_key_exists($processToStop->processId, $runningProcesses)) {
				$processesToStop[$processToStop->processId] = clone $processToStop;
			}else{
				//This process is no longer running
				$processToStop->stopAttempted = 2;
				$processToStop->update();
			}
		}

		$interface->assign('runningProcesses', $runningProcesses);
		$interface->assign('processesToStop', $processesToStop);

		$this->display('indexerInformation.tpl', 'Indexer Information', false);
	}

	function loadRunningProcesses() {
		global $configArray;
		global $serverName;
		$runningProcesses = [];
		if ($configArray['System']['operatingSystem'] == 'windows') {
			/** @noinspection SpellCheckingInspection */
			exec("WMIC PROCESS get Processid,Commandline", $processes);
			$processRegEx = '/.*?java(?:.exe\")?\s+-jar\s(.*?)\.jar.*?\s+(\d+)/ix';
			$processIdIndex = 2;
			$processNameIndex = 1;
			$processStartIndex = -1;
			$solrRegex = "/$serverName\\\\solr7/ix";
		} else {
			exec("ps -ef | grep java", $processes);
			$processRegEx = '/(\d+)\s+.*?([a-zA-Z0-9:]{5}).*?(\d{2}:\d{2}:\d{2})\sjava\s-jar\s(.*?)\.jar\s' . $serverName . '/ix';
			$processIdIndex = 1;
			$processNameIndex = 4;
			$processStartIndex = 2;
			$solrRegex = "/(\d+)\s+.*?([a-zA-Z0-9:]{5}).*?(\d{2}:\d{2}:\d{2})\s.*?$serverName\/solr7/ix";
		}

		$results = "";

		$solrRunning = false;
		foreach ($processes as $processInfo) {
			if (preg_match($processRegEx, $processInfo, $matches)) {
				$processId = $matches[$processIdIndex];
				if ($processStartIndex > 0) {
					$startDayTime = $matches[$processStartIndex];
				}else{
					$startDayTime = translate(['text'=>'Not Available', 'isPublicFacing'=>true]);
				}

				$process = $matches[$processNameIndex];
				if (array_key_exists($process, $runningProcesses)) {
					$results .= "There is more than one process for $process PID: {$runningProcesses[$process]['pid']} and $processId\r\n";
				} else {
					$runningProcesses[$processId] = [
						'name' => $process,
						'pid' => $processId,
						'startTime' => $startDayTime,
					];
				}

				//echo("Process: $process ($processId)\r\n");
			} elseif (preg_match($solrRegex, $processInfo)) {
				$solrRunning = true;
			}
		}

		return $runningProcesses;
	}

	function getActiveAdminSection(): string {
		return 'greenhouse';
	}

	function canView(): bool {
		if (UserAccount::isLoggedIn()) {
			if (UserAccount::getActiveUserObj()->isAspenAdminUser()) {
				return true;
			}
		}
		return false;
	}

	function getBreadcrumbs(): array {
		$breadcrumbs = [];
		$breadcrumbs[] = new Breadcrumb('/Greenhouse/Home', 'Greenhouse Home');
		$breadcrumbs[] = new Breadcrumb('', 'Indexer Information');

		return $breadcrumbs;
	}
}