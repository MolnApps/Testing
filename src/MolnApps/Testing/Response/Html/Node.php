<?php

namespace MolnApps\Testing\Response\Html;

interface Node
{
	public function getTag();
	public function setAttributes(array $attributes);
	public function setAttribute($name, $value);
	public function getAttribute($name);
	public function getAttributes();
	public function addChild($tag, array $attributes = []);
}