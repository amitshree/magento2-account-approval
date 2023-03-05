<?php

namespace Amitshree\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

/**
 * Check customer account is new
 */
class CheckCustomer implements ObserverInterface
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->coreRegistry = $registry;
    }

    /**
     * Set is_new_account true
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->coreRegistry->register('is_new_account', true);
    }
}
