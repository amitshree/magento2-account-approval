# magento2-account-approval
###### What is this extension about?
This extension restricts customers to login on the website just after registration. Customers can login to website only after website admin approves customers account.

###### Download Extension in your project
```
composer config repositories.magento2-account-approval git https://github.com/amitshree/magento2-account-approval.git
composer require amitshree/magento2-customer-account-approval:dev-master
```

###### Enable and Install extension
Run commands
```
bin/magento module:enable Amitshree_Customer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
```

Enable extension by navigation to ```Stores => Configuration => Amitshree => Customer Login```

###### Un-Install/Remove Extension
```
php bin/magento module:uninstall Amitshree_Customer
```
