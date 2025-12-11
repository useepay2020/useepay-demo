> Before you begin, please ensure your Shopify store account has the **Payment methods** permission in the Shopify admin settings.

### **Installing UseePay Payment (Localized)**
1) <span style="color:red;">Connect to VPN, log in to your Shopify admin, enter the following URL in the web browser: https://apps.shopify.com/local-payment-1, and add this application.

<span style="color:red;">**Note**: Please add the **Local Payment** app. Do not select any other app applications.


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568301/image-preview)

How to obtain the Security Key (Log in to MC Backend - Configuration Center - Domain Management, click Details to view): https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590328m0

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568303/image-preview)

How to obtain Merchant ID information (Log in to MC Backend - Account Management - Account Information to view): https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590322m0

**Click Install**

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568305/image-preview)
2) Select and check the corresponding localized payment methods, then activate them. **Do not check the Test mode box.**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568307/image-preview)
**If you have activated Afterpay and need to display it separately, please enter the following URL in your web browser:** https://apps.shopify.com/afterpay-3?locale=zh-CN, and add the application.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568308/image-preview)
**If you have activated Klarna payment and need to display it separately, please enter the following URL in your web browser:** https://apps.shopify.com/klarna-useepay?st_source=autocomplete to add the application.


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568310/image-preview)
<span style="color:red;">**3) Please set up email checkout in your Shopify admin, otherwise transactions may fail. See the operation method below:**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568311/image-preview)

<span style="color:red;">**4) After completing all the above settings, please proceed with transaction testing:**

Log in to your company's store website and proceed to pay for any item priced above $1.

Card number selection guidelines for the payment step:

1. Use your own real card number.

PS: If a real card is used for payment, certain fixed and percentage fees will be charged according to your contract.

2. Use a test card for testing: **<span style="color:red;">4111111111111111**

PS: Enter a valid expiration date and a reasonable CVV. Since the test card will show an unsuccessful order (i.e., **<span style="color:red;">transaction failed**), a processing fee will be charged as per the contract.

After payment, a successful or failed transaction order can be viewed in **MC Backend -> Transaction Query**.

<span style="color:red;">**5) Logistics Plugin Binding**

**<span style="color:red;">To avoid affecting your order settlement, please bind and synchronize the logistics plugin for your Shopify-built website in the MC Backend.**

MC Backend Link: https://mc.useepay.com/
<span style="color:red;">Logistics Management - Logistics Binding - Click "Add New" and follow the operation manual to add.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568313/image-preview)