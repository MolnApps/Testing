<?php

namespace MolnApps\Testing\Response;

interface Controller
{
	public function bootApplication();
	public function shutdownApplication();
	public function signedInAs($userId);
	public function signedOut();
	public function run($requestParams = [], $uploadedFiles = []);
}