<?php

namespace Amitshree\Customer\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

/**
 * Redirect to login
 */
class Redirect
{
    protected const MODULE_ENABLED = 'customerlogin/general/enable';

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @param Registry $registry
     * @param UrlInterface $url
     * @param ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Registry $registry,
        UrlInterface $url,
        ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        ResultFactory $resultFactory
    ) {
        $this->coreRegistry = $registry;
        $this->url = $url;
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->session = $customerSession;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Block auto login
     * @param \Magento\Customer\Model\Account\Redirect $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     */
    public function aroundGetRedirect($subject, \Closure $proceed)
    {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $enable = $this->scopeConfig->getValue(self::MODULE_ENABLED, $storeScope);

        if ($enable && $this->coreRegistry->registry('is_new_account')) {

            // Clear previous messages
            $this->messageManager->getMessages(true);

            // Adding a custom message
            $this->messageManager->addErrorMessage(__('Your account is not approved.
            Kindly contact website admin for assitance.'));

            // Destroy the customer session in order to redirect him to the login page
            $this->session->destroy();

            /** @var \Magento\Framework\Controller\Result\Redirect $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $result->setUrl($this->url->getUrl('customer/account/login'));
            return $result;
        }

        return $proceed();
    }
}
