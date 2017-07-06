<?php

namespace MolnApps\Testing\Response\Testing;

use \MolnApps\Testing\Response\Controller;

class ControllerStub implements Controller
{
	private $booted = false;
	private $signedIn = false;
	private $params = [];
	private $uploadedFiles = [];

	public function bootApplication()
	{
		$this->booted = true;
	}

	public function shutdownApplication()
	{
		$this->booted = false;
	}

	public function wasBooted()
	{
		return !! $this->booted;
	}

	public function signedInAs($userId)
	{
		$this->signedIn = true;
	}

	public function signedOut()
	{
		$this->signedIn = false;
	}

	public function isSignedIn()
	{
		return !! $this->signedIn;
	}

	public function run($params = [], $uploadedFiles = [])
	{
		$this->params = $params;
		$this->uploadedFiles = $uploadedFiles;

		if ($this->hasParam('cmd')) {
			return $this->createCommandResponse($this->getParam('cmd'));
		}

		return $this->createDefaultResponse();
	}

	private function getParam($param)
	{
		return ($this->hasParam($param)) ? $this->params[$param] : null;
	}

	private function hasParam($param)
	{
		return isset($this->params[$param]);
	}

	private function createCommandResponse($commandName)
	{
		$this->params[] = $commandName.' response';

		$params = implode(' ', $this->convertParamsToString($this->params));

		$uploadedFiles = 'Uploaded files: '
			. "\r\n"
			. implode("\r\n", $this->convertUploadedFilesToString($this->uploadedFiles));

		return "\r\n".$params."\r\n".$uploadedFiles."\r\n";
	}

	private function convertParamsToString(array $params)
	{
		$strParams = [];
		
		foreach ($params as $pname => $pvalue) {
			if (is_array($pvalue)) {
				$pindexes = '[' . implode(', ', array_keys($pvalue)) . ']';
				$pvalues = '[' . implode(', ', array_values($pvalue)) . ']';
				$pvalue = $pindexes . $pvalues;
			}

			$strParams[] = $pname . ' = ' . $pvalue;
		}

		return $strParams;
	}

	private function convertUploadedFilesToString(array $uploadedFiles)
	{
		$strUploadedFiles = [];

		foreach ($uploadedFiles as $uploadedFiles) {
			$strUploadedFiles[] = json_encode($uploadedFiles);
		}

		return $strUploadedFiles;
	}

	private function createDefaultResponse()
	{
		return '<form id="myForm"></form>';
	}
}