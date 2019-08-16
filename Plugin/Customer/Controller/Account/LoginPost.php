<?php

namespace Amitshree\Customer\Plugin\Customer\Controller\Account;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\Action\Context;

class LoginPost
{

    const MODULE_ENABLED = 'customerlogin/general/enable';

    /**
     * @var Session
     */
    protected $session;

    /** @var Validator */
    protected $formKeyValidator;

    /** @var CustomerRepositoryInterface */
    protected $customerRepositoryInterface;

    /** @var ManagerInterface **/
    protected $messageManager;

    protected $currentCustomer;

    public function __construct(
        Session $customerSession,
        Validator $formKeyValidator,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ManagerInterface $messageManager,
        ScopeConfig $scopeConfig,
        Context $context
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->session = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    public function aroundExecute(\Magento\Customer\Controller\Account\LoginPost $loginPost, \Closure $proceed)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $enable = $this->scopeConfig->getValue (self::MODULE_ENABLED, $storeScope);

        if(!$enable){
            return $proceed();
        }

        if ($loginPost->getRequest()->isPost()) {
            $login = $loginPost->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {

                $customer = $this->getCustomer($login['username']);
                if(!$customer){
                    return $proceed();
                }

                try {
                    if (!empty($customer->getCustomAttributes())) {
                        if ($this->isAccountNotApproved($customer)) {
                            $this->messageManager->addErrorMessage(__('Your account is not approved. Kindly contact website admin for assitance.'));
                        
                            return $this->resultRedirectFactory->create()
                                    ->setPath('customer/account/login');
                            //@todo:: redirect to last visited url
                        } else {
                            return $proceed();
                        }
                    } else {
                        // if no custom attributes found
                        return $proceed();
                    }
                }
                catch (\Exception $e)
                {
                    $message = "Invalid User credentials.";
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                    return $this->resultRedirectFactory->create()
                                    ->setPath('customer/account/login');
                }

            }
            else {
                // call the original execute function
                return $proceed();
            }
        }
        else {
            // call the original execute function
            return $proceed();
        }
    }

    /**
     * @param $email
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer($email)
    {
        try{
            $this->currentCustomer = $this->customerRepositoryInterface->get($email);
            return $this->currentCustomer;
        }catch (\Exception $e){
            return false;
        }

    }
    /**
     * Check if customer is a vendor and account is approved
     * @return bool
     */
    public function isAccountNotApproved($customer)
    {

        $customAttribute = $customer->getCustomAttribute('approve_account');
        if(empty($customAttribute)){
            return true;
        }
        $isApprovedAccount = $customer->getCustomAttribute('approve_account')->getValue();
        if($isApprovedAccount)
        {
            return false;
        }
        return true;
    }
}