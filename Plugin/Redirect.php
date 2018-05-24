<?php

namespace Amitshree\Customer\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class Redirect
{
    const MODULE_ENABLED = 'customerlogin/general/enable';

    protected $coreRegistry;
    protected $url;
    protected $resultFactory;
    protected $messageManager;
    protected $session;

    public function __construct(Registry $registry,
                                UrlInterface $url,
                                ManagerInterface $messageManager,
                                ScopeConfigInterface $scopeConfig,
                                Session $customerSession,
                                ResultFactory $resultFactory
    )
    {
        $this->session = $customerSession;
        $this->coreRegistry = $registry;
        $this->url = $url;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->_scopeConfig = $scopeConfig;


    }

    public function aroundGetRedirect ($subject, \Closure $proceed)
    {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $enable = $this->_scopeConfig->getValue (self::MODULE_ENABLED, $storeScope);

        if ($enable && $this->coreRegistry->registry('is_new_account')) {

            // Clear previous messages
            $this->messageManager->getMessages(true);

            // Adding a custom message
            $this->messageManager->addErrorMessage(__('Your account is not approved. Kindly contact website admin for assitance.'));

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
