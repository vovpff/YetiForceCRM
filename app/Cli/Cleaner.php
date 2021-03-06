<?php
/**
 * Cleaner cli file.
 *
 * @package   App
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace App\Cli;

/**
 * Cleaner cli class.
 */
class Cleaner extends Base
{
	/** {@inheritdoc} */
	public $moduleName = 'Cleaner';

	/** @var string[] Methods list */
	public $methods = [
		'logs' => 'Delete all logs',
		'session' => 'Delete all session',
		'cacheData' => 'Cache data',
		'cacheFiles' => 'Cache files',
	];

	/**
	 * Delete all logs.
	 *
	 * @return void
	 */
	public function logs(): void
	{
		$i = 0;
		$this->climate->bold('Removing all logs...');
		foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOT_DIRECTORY . '/cache/logs', \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
			if ($item->isFile() && 'index.html' !== $item->getBasename()) {
				$this->climate->bold($iterator->getSubPathName() . ' - ' . \vtlib\Functions::showBytes($item->getSize()));
				unlink($item->getPathname());
				++$i;
			}
		}
		$this->climate->lightYellow()->border('─', 200);
		$this->climate->bold('Number of deleted files log: ' . $i);
		$this->climate->lightYellow()->border('─', 200);
	}

	/**
	 * Delete all session.
	 *
	 * @return void
	 */
	public function session(): void
	{
		$this->climate->bold('Removing all sessions...');
		\App\Session::load();
		$this->climate->bold('Number of sessions deleted: ' . \App\Session::cleanAll());
		$this->climate->lightYellow()->border('─', 200);
		$this->cli->actionsList('Cleaner');
	}

	/**
	 * Clear cache data.
	 *
	 * @return void
	 */
	public function cacheData(): void
	{
		$this->climate->bold('Clear: ' . \App\Cache::clear());
		$this->climate->bold('Clear opcache: ' . \App\Cache::clearOpcache());
		$this->climate->lightYellow()->border('─', 200);
		$this->cli->actionsList('Cleaner');
	}

	/**
	 * Clear cache files.
	 *
	 * @return void
	 */
	public function cacheFiles(): void
	{
		foreach (['cache', 'cache/vtlib', 'cache/upload', 'cache/pdf', 'cache/mail', 'cache/import', 'cache/templates_c'] as $dir) {
			$s = $i = 0;
			$this->climate->inline($dir);
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOT_DIRECTORY . '/' . $dir, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
				if ($item->isFile() && 'index.html' !== $item->getBasename()) {
					$s += $item->getSize();
					unlink($item->getPathname());
					++$i;
				}
			}
			$this->climate->bold(" - files: $i , size: " . \vtlib\Functions::showBytes($s));
		}
		$this->climate->lightYellow()->border('─', 200);
		$this->cli->actionsList('Cleaner');
	}
}
