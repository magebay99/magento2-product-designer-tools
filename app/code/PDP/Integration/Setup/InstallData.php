<?php
namespace PDP\Integration\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use PDP\Integration\Model\Product\Type\Pdpro as PDPRO;

class InstallData implements InstallDataInterface {
   /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$attributes = [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'minimal_price',
            'msrp',
            'msrp_display_actual_price_type',			
            'cost',
            'tier_price',
            'weight',
			'country_of_manufacture',
			'color'
		];
		foreach ($attributes as $attributeCode) {
			$relatedProductTypes = explode(
				',',
				$eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, 'apply_to')
			);
			if(!in_array(PDPRO::TYPE_CODE,$relatedProductTypes)) {
				$relatedProductTypes[] = PDPRO::TYPE_CODE;
				$eavSetup->updateAttribute(
					\Magento\Catalog\Model\Product::ENTITY,
					$attributeCode,
					'apply_to',
					implode(',', $relatedProductTypes)
				);
			}
		}
	}	
}