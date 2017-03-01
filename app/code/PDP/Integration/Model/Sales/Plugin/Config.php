<?php
namespace PDP\Integration\Model\Sales\Plugin;

class Config
{
	public function afterGetAvailableProductTypes($subject, $result)
    {
		$result[] = 'pdpro';
        return $result;
    }
}