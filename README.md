
![Alt text](http://demo.productsdesignerpro.com/magento2/lab/public/admin/images/pushandpull.svg "Enable Shopping Cart") 

# Free download The Magento2 Bridge plugin for Online Product Designer Tools
- The Magento2 Bridge plugin integrates your PDP system into Magento2 at https://goo.gl/AFlnBQ
- Manage order with customized design (Export and Edit customized design from customer's order details)
- Manage personalize products with PDP
- Manage My Customized Design section (Magento user can save or order customized design)
- Source code is open for any suggest or customization.
- Only contributors can submit commit 

# Installation Guide 

1. Download and upload into your Magento2 root directory
2. Run install command line from Magento 2 root directory (via SSH)

php bin/magento setup:upgrade 
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy

3. Config module in Magento Backend

![Alt text](http://image.prntscr.com/image/4b2545e197ee44ea99ddcda62fc480fa.png "Enable Module") 
![Alt text](http://image.prntscr.com/image/0544b49a946a484596d908a5a1bead12.png "Config Module") 

# How to push product from PDP to Magento2

1. Config Bridge PDP with Magento 2. 
- Enable Shopping cart and bridge to Magento2:

![Alt text](http://image.prntscr.com/image/d590b720a652453da0851ae3d8770309.png "Enable Shopping Cart") 

- Enter API access information to connect with Magento2(it can be Magento2 admin access)
![Alt text](http://image.prntscr.com/image/64b97bb64d7c44a7be25b7adbcf33284.png "Integrate with Magento2") 

2. Push products into Magento2 for ready to publish.
- Push single product to Magento2
![Alt text](http://image.prntscr.com/image/c9a9e469a1a046b5a8efcb5fc7d849be.png "Push single product to live") 
- Push multiple products (max 12)
![Alt text](http://g.recordit.co/wPC1LI8pcw.gif "Push multiple products (max 12)") 

After product is pushed succesful, that item status will be change to LIVE


# About The PDP System
An Online Products Designer Tools For most popular ecommerce platform like Magento, Woocommerce, OpenCart, Shopify, Prestashop and  your custom cart. Here are some useful information:
- How it works? Check here https://productsdesignerpro.com/product-design-tools-how-it-works/
- Key Features  https://www.productsdesignerpro.com/key-features-product-design-tools 
- Pricing https://www.productsdesignerpro.com/pricing 
- Help Center https://productsdesignerpro.com/support/

