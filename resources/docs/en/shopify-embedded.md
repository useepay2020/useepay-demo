Before you begin, please ensure your Shopify store account has the **Payment methods** permission for backend settings and that your MC backend domain review has passed.

**I. Installing UseePay Payment (Direct, supporting only Credit Cards V/M/DC/AE/JCB)**

1. Log in to your Shopify admin panel, go to the **Payments** section, click **View all providers**, search for **UseePay**, and select **<span style="color:red;">integration-credit-debit-card</span>** to install.

If you cannot find it via search, install via this link: https://apps.shopify.com/integration-credit-debit-card

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568276/image-preview)

1.2 <span style="color:red;">If you are using the Shopify Plus plan, please go to the **Payments** settings in your store admin, click **Manage**, then click **Third-party providers**, and select **UseePay's integration-credit-debit-card** to install.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568278/image-preview)

**2. Enter the corresponding Merchant ID and click Submit**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568280/image-preview)
How to obtain the Security Key (Log in to MC Backend - Configuration Center - Domain Management, click Details to view): https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590328m0

How to obtain Merchant ID information (Log in to MC Backend - Account Management - Account Information to view): https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590322m0

3. Select the corresponding payment method. **Important: Do not check the Test mode box.**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568281/image-preview)
**4. After successful activation, the corresponding integration-credit-debit-card payment method will be displayed in the Payments section.**

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568282/image-preview)

**II. Other Operations**
**<span style="color:red;">1. Consumer Email is Mandatory**
**<span style="color:red;">Please set up email checkout in your Shopify admin**, otherwise transactions may fail. See the operation method below:


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568284/image-preview)

**<span style="color:red;">2. Transaction Testing**
Log in to your company's store website and proceed to pay for any item priced above $1.

Card number selection guidelines for the payment step:

1) Use your own real card number.

PS: If a real card is used for payment, certain fixed and percentage fees will be charged according to your contract.

2) Use a test card for testing: **<span style="color:red;">4111111111111111**

PS: Enter a valid expiration date (beyond the current date) and a reasonable CVV. Since the test card will show an unsuccessful order (i.e., **<span style="color:red;">transaction failed**), a processing fee will be charged as per the contract.

After payment, a successful or failed transaction order can be viewed in **MC Backend -> Transaction Query**.

**<span style="color:red;">3. Logistics Plugin Binding**
**To avoid affecting your order settlement, please bind and synchronize the logistics plugin for your Shopify-built website in the MC Backend.**

MC Backend Link: https://mc.useepay.com/
Logistics Management - Logistics Binding - Click "Add New" and follow the operation manual to add.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568286/image-preview)