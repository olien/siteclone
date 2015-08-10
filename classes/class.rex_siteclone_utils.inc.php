<?php
class rex_siteclone_utils {
	public static function websiteCreated($params) {
		global $REX;

		$log = $params['subject']['log_object'];
		$sourceWebsiteId = $REX['ADDON']['siteclone']['settings']['source_webiste_id'];
		$sourceTablePrefix = $REX['WEBSITE_MANAGER']->getWebsite($sourceWebsiteId)->getTablePrefix();
		$newWebsiteId = $params['subject']['website_id'];
		$newTablePrefix = $params['subject']['table_prefix'];

		$sql = new rex_sql();
		//$sql->debugsql = true;

		// copy structure table data
		if ($REX['ADDON']['siteclone']['settings']['clone_structure']) {
			rex_website_manager_utils::logQuery($log, $sql, 'INSERT INTO `' . $newTablePrefix . 'article` SELECT * FROM ' . $sourceTablePrefix . 'article');
		}

		// copy slices table data
		if ($REX['ADDON']['siteclone']['settings']['clone_slices']) {
			rex_website_manager_utils::logQuery($log, $sql, 'INSERT INTO `' . $newTablePrefix . 'article_slice` SELECT * FROM ' . $sourceTablePrefix . 'article_slice');
		}

		// copy media table data
		if ($REX['ADDON']['siteclone']['settings']['clone_media']) {
			rex_website_manager_utils::logQuery($log, $sql, 'INSERT INTO `' . $newTablePrefix . 'file_category` SELECT * FROM ' . $sourceTablePrefix . 'file_category');
			rex_website_manager_utils::logQuery($log, $sql, 'INSERT INTO `' . $newTablePrefix . 'file` SELECT * FROM ' . $sourceTablePrefix . 'file');

			$src = $REX['FRONTEND_PATH'] . '/' . rex_website::constructMediaDir($sourceWebsiteId);
			$files = glob($src . '/*.*');

			foreach ($files as $file) {
				$targetFile = str_replace(rex_website::constructMediaDir($sourceWebsiteId), rex_website::constructMediaDir($newWebsiteId), $file);

				$logMsg = '[COPY FILE] Source: ' . $file . ' | Target: ' . $targetFile;

				if (copy($file, $targetFile)) {
					$log->logInfo($logMsg);
				} else {
					$log->logError($logMsg);
				}
			}
		}

		// copy string table data
		if ($REX['ADDON']['siteclone']['settings']['clone_strings']) {
			rex_website_manager_utils::logQuery($log, $sql, 'INSERT INTO `' . $newTablePrefix . 'string_table` SELECT * FROM ' . $sourceTablePrefix . 'string_table ORDER BY priority');

			// empty string table values
			if ($REX['ADDON']['siteclone']['settings']['empty_strings']) {
				$stringTableFields = $sql->getArray('SELECT * FROM `' . $newTablePrefix . 'string_table` LIMIT 1');
				$values = 'SET value_0 = ""';

				foreach ($stringTableFields[0] as $key => $value) {
					if (preg_match( '/value./', $key) ) {
						if ($key != 'value_0') {
							$values .= ',' . $key . ' = ""';
						}
					}
				}

				rex_website_manager_utils::logQuery($log, $sql, 'UPDATE `' . $newTablePrefix . 'string_table` ' . $values);
			}
		}

		// copy file
		if ($REX['ADDON']['siteclone']['settings']['copy_file'] != '') {
			$sourceFile = str_replace('{id}', $sourceWebsiteId, $REX['ADDON']['siteclone']['settings']['copy_file']);
			$targetFile = str_replace('{id}', $newWebsiteId, $REX['ADDON']['siteclone']['settings']['copy_file']);

			$sourceFile = $REX['FRONTEND_PATH'] . DIRECTORY_SEPARATOR . trim($sourceFile, './');
			$targetFile = $REX['FRONTEND_PATH'] . DIRECTORY_SEPARATOR . trim($targetFile, './');

			$logMsg = '[COPY FILE] Source: ' . $sourceFile . ' | Target: ' . $targetFile;

			if (copy($sourceFile, $targetFile)) {
				$log->logInfo($logMsg);
			} else {
				$log->logError($logMsg);
			}
		}
	}

	public static function getSettingsFile() {
		global $REX;

		$dataDir = $REX['INCLUDE_PATH'] . '/data/addons/website_manager/siteclone/';

		return $dataDir . 'settings.inc.php';
	}

	public static function includeSettingsFile() {
		global $REX; // important for include

		$settingsFile = self::getSettingsFile();

		if (!file_exists($settingsFile)) {
			self::updateSettingsFile(false);
		}

		require_once($settingsFile);
	}

	public static function updateSettingsFile($showSuccessMsg = true) {
		global $REX, $I18N;

		$settingsFile = self::getSettingsFile();
		$msg = self::checkDirForFile($settingsFile);

		if ($msg != '') {
			if ($REX['REDAXO']) {
				echo rex_warning($msg);			
			}
		} else {
			if (!file_exists($settingsFile)) {
				self::createDynFile($settingsFile);
			}

			$content = "<?php\n\n";
		
			foreach ((array) $REX['ADDON']['siteclone']['settings'] as $key => $value) {
				$content .= "\$REX['ADDON']['siteclone']['settings']['$key'] = " . var_export($value, true) . ";\n";
			}

			if (rex_put_file_contents($settingsFile, $content)) {
				if ($REX['REDAXO'] && $showSuccessMsg) {
					echo rex_info($I18N->msg('website_manager_siteclone_config_ok'));
				}
			} else {
				if ($REX['REDAXO']) {
					echo rex_warning($I18N->msg('website_manager_siteclone_config_error'));
				}
			}
		}
	}

	public static function replaceSettings($settings) {
		global $REX;

		// type conversion
		foreach ($REX['ADDON']['siteclone']['settings'] as $key => $value) {
			if (isset($settings[$key])) {
				$settings[$key] = self::convertVarType($value, $settings[$key]);
			}
		}

		$REX['ADDON']['siteclone']['settings'] = array_merge((array) $REX['ADDON']['siteclone']['settings'], $settings);
	}

	public static function createDynFile($file) {
		$fileHandle = fopen($file, 'w');

		fwrite($fileHandle, "<?php\r\n");
		fwrite($fileHandle, "// --- DYN\r\n");
		fwrite($fileHandle, "// --- /DYN\r\n");

		fclose($fileHandle);
	}

	public static function checkDir($dir) {
		global $REX, $I18N;

		$path = $dir;

		if (!@is_dir($path)) {
			@mkdir($path, $REX['DIRPERM'], true);
		}

		if (!@is_dir($path)) {
			if ($REX['REDAXO']) {
				return $I18N->msg('website_manager_siteclone_install_make_dir', $dir);
			}
		} elseif (!@is_writable($path . '/.')) {
			if ($REX['REDAXO']) {
				return $I18N->msg('website_manager_siteclone_install_perm_dir', $dir);
			}
		}
		
		return '';
	}

	public static function checkDirForFile($fileWithPath) {
		$pathInfo = pathinfo($fileWithPath);

		return self::checkDir($pathInfo['dirname']);
	}

	public static function convertVarType($originalValue, $newValue) {
		$arrayDelimiter = ',';

		switch (gettype($originalValue)) {
			case 'string':
				return trim($newValue);
				break;
			case 'integer':
				return intval($newValue);
				break;
			case 'boolean':
				return (bool) $newValue;
				break;
			case 'array':
				if ($newValue == '') {
					return array();
				} else {
					return explode($arrayDelimiter, $newValue);
				}
				break;
			default:
				return $newValue;
				
		}
	}
}
