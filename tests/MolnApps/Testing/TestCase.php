<?php

namespace MolnApps\Testing;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
	protected function setExpectedException($exceptionClass, $exceptionMessage = null)
	{
		$this->expectException($exceptionClass);
		if ($exceptionMessage) {
			$this->expectExceptionMessage($exceptionMessage);
		}
	}
}