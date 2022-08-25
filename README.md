# magento2-account-approval
###### What is this extension about?
This extension restricts customers to login on the website just after registration. Customers can login to website only after website admin approves customers account.

###### Install Extension
```
composer config repositories.magento2-account-approval git https://github.com/amitshree/magento2-account-approval.git
composer require amitshree/magento2-customer-account-approval:dev-master
php bin/magento setup:upgrade
```

###### Enable extension
Run commands
```
bin/magento mod:en Amitshree_Customer
bin/magento setup:upgrade
```

Enable extension by navigation to ```Stores => Configuration => Amitshree customer => Customer Login```

###### Un-Install/Remove Extension
```
php bin/magento module:uninstall Amitshree_Customer
```
