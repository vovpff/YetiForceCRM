<?php
/**
 * Security dependency check.
 *
 * @package   App
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Arkadiusz Adach <a.adach@yetiforce.com>
 * @author    Arkadiusz Dudek <a.dudek@yetiforce.com>
 */

namespace App\Security;

/**
 * Class Dependency - Security dependency check.
 */
class Dependency
{
	/**
	 * Cache file name.
	 */
	const CACHE_FILE_NAME = ROOT_DIRECTORY . \DIRECTORY_SEPARATOR . 'cache' . \DIRECTORY_SEPARATOR . 'security' . \DIRECTORY_SEPARATOR . 'dependency.json';
	/**
	 * Symfony check url.
	 *
	 * @var string
	 */
	private $checkUrl = 'https://security.symfony.com/check_lock';

	/**
	 * Security checker.
	 *
	 * @throws \App\Exceptions\AppException
	 *
	 * @return array
	 */
	public function securityChecker(): array
	{
		$result = [];
		if ($this->hasCache()) {
			$result = $this->getCache();
		} elseif (\App\RequestUtil::isNetConnection()) {
			$result = $this->check();
			$this->saveCache($result);
		}
		return $result;
	}

	/**
	 * Check if a cache is available.
	 *
	 * @return bool
	 */
	private function hasCache(): bool
	{
		return \file_exists(static::CACHE_FILE_NAME) &&
			\filesize(static::CACHE_FILE_NAME) > 0 &&
			\time() - \filemtime(static::CACHE_FILE_NAME) < (int) \App\Config::security('CACHE_LIFETIME_SENSIOLABS_SECURITY_CHECKER');
	}

	/**
	 * Save the data to the cache.
	 *
	 * @param array $result
	 *
	 * @throws \App\Exceptions\AppException
	 */
	private function saveCache(array $result)
	{
		\file_put_contents(static::CACHE_FILE_NAME, \App\Json::encode($result));
	}

	/**
	 * Get data from the cache.
	 *
	 * @throws \App\Exceptions\AppException
	 *
	 * @return array
	 */
	private function getCache(): array
	{
		return \App\Json::decode(\file_get_contents(static::CACHE_FILE_NAME));
	}

	/**
	 * Send lock file to verify.
	 *
	 * @return array
	 */
	public function check(): array
	{
		$result = [];
		if (\App\RequestUtil::isNetConnection() && !empty($lockFile = $this->getLockFile(ROOT_DIRECTORY))) {
			$options = \App\RequestHttp::getOptions();
			$options['headers']['Content-Type'] = 'application/octet-stream';
			$options['headers']['Content-Disposition'] = 'form-data; name="lock"; filename="composer.lock"';
			$options['headers']['Accept'] = 'application/json';
			\App\Log::beginProfile("POST|Dependency::check|{$this->checkUrl}", __NAMESPACE__);
			$response = (new \GuzzleHttp\Client($options))->post($this->checkUrl, [
				'body' => $lockFile
			]);
			\App\Log::endProfile("POST|Dependency::check|{$this->checkUrl}", __NAMESPACE__);
			$result = (array) \App\Json::decode($response->getBody());
			$result = (\is_array($result) && !empty($result)) ? $result : [];
		}
		return $result;
	}

	/**
	 * Get lock file content.
	 *
	 * @param string $lock
	 *
	 * @return string
	 */
	private function getLockFile($lock): string
	{
		if (is_dir($lock) && file_exists($lock . \DIRECTORY_SEPARATOR . 'composer.lock')) {
			$lock = $lock . \DIRECTORY_SEPARATOR . 'composer.lock';
		} elseif (preg_match('/composer\.json$/', $lock)) {
			$lock = str_replace('composer.json', 'composer.lock', $lock);
		}
		return is_file($lock) ? $this->getLockContent($lock) : '';
	}

	/**
	 * Get parsed lock file elements.
	 *
	 * @param string $lock
	 *
	 * @return string
	 */
	private function getLockContent($lock): string
	{
		$contents = json_decode(file_get_contents($lock), true);
		$hash = $contents['content-hash'] ?? ($contents['hash'] ?? '');
		$packages = ['content-hash' => $hash, 'packages' => []];
		if (\is_array($contents['packages'])) {
			foreach ($contents['packages'] as $package) {
				$data = [
					'name' => $package['name'],
					'version' => $package['version'],
				];
				if (isset($package['time']) && false !== strpos($package['version'], 'dev')) {
					$data['time'] = $package['time'];
				}
				$packages['packages'][] = $data;
			}
		}
		return json_encode($packages);
	}
}
