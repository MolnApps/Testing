<?php

namespace MolnApps\Testing\Response\Html;

interface DomParser
{
	public function load($markup);
	public function find($selectors);
}