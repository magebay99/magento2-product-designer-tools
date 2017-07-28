<?php
namespace PDP\Integration\Model\Sales\Plugin;

class Config
{
	public function afterGetAvailableProductTypes($subject, $result)
    {
		$result[] = \PDP\Integration\Model\Product\Type\Pdpro::TYPE_CODE;
        return $result;
    }
}