<?php

namespace MolnApps\Testing\Form;

class File extends AbstractFormElement
{
	public function setValue($value)
	{
		if ( ! is_array($value) || ! isset($value['tmp_name'])) {
			$value = null;
		} else {
			$value = json_encode($value);
		}

		$this->domElement->setAttribute('data-upload-file-json', $value);
	}

	public function getValue()
	{
		$json = $this->domElement->getAttribute('data-upload-file-json');
		
		if ( ! $json) {
			return null;
		}
		
		return (array)json_decode($json);
	}
}