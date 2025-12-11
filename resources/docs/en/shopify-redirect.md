**I. Installing UseePay Payment (Redirect Checkout)**

1. <span style="color:red;">Connect to VPN, log in to your Shopify admin, enter the following URL in the web browser: <span style="color:red;">https://apps.shopify.com/2-advanced-credit-debit-card</span>, and add the application.

<span style="color:red;">**Note**: Please add the **2-advanced-credit-debit-card** app. Do not select any other app applications.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568287/image-preview)
**2. Click Install app --> Fill in the binding information --> Activate** <span style="color:red;">(**Important: Do not check JCB or Test mode. Select payment methods based on your website's actual payment acceptance.**)


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568288/image-preview)

How to obtain the Security Key (Log in to MC Backend - Configuration Center - Domain Management, click Details to view): https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590328m0

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568289/image-preview)
How to obtain Merchant ID information (Log in to MC Backend - Account Management - Account Information to view): https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590322m0

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568290/image-preview)


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568291/image-preview)

**II. Other Operations**

**<span style="color:red;">1. Consumer Email is Mandatory**
**<span style="color:red;">Please set up email checkout in your Shopify admin, otherwise transactions may fail.** See the operation method below:


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568292/image-preview)

**<span style="color:red;">2. Transaction Testing**
Log in to your company's store website and proceed to pay for any item priced above $1.

Card number selection guidelines for the payment step:

1) Use your own real card number.

PS: If a real card is used for payment, certain fixed and percentage fees will be charged according to your contract.

2) Use a test card for testing: **<span style="color:red;">4111111111111111**

PS: Enter a valid expiration date (beyond the current date) and a reasonable CVV. Since the test card will show an unsuccessful order (i.e., **<span style="color:red;">transaction failed**), a processing fee will be charged as per the contract.

After payment, a successful or failed transaction order can be viewed in **MC Backend -> Transaction Query**.

**<span style="color:red;">3. Logistics Plugin Binding**
<span style="color:red;">To avoid affecting your order settlement, please bind and synchronize the logistics plugin for your Shopify-built website in the MC Backend.

MC Backend Link: https://mc.useepay.com/
Logistics Management - Logistics Binding - Click "Add New" and follow the operation manual to add.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568293/image-preview)