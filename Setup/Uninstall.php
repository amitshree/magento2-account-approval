<?php


namespace Amitshree\Customer\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Uninstallation scripts
 */
class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Remove customer attribute
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup =  $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'approve_account');
        $setup->endSetup();
    }
}
