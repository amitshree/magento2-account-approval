<?php

namespace Amitshree\Customer\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    const APPROVE_ACCOUNT = 'approve_account';

    /** @var EavSetupFactory  */
    protected $eavSetupFactory;

    /** @var CustomerSetupFactory */
    protected $customerSetupFactory;

    /** @var Customer */
    protected $_customer;

    /** @var SetFactory  */
    protected $attributeSetFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Customer $customer
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        Customer $customer,
        SetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_customer = $customer;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            // Unit
            $eavSetup->updateAttribute(
                Customer::ENTITY,
                self::APPROVE_ACCOUNT,
                [
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                ]
            );
        }
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $customers = $this->_customer->getCollection()->addAttributeToSelect("*")->load();
            foreach ($customers as $key => $customer){

                $customer->setData('approve_account','1');
                $customer->save();
            }
        }

    }
}
