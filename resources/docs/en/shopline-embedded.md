### Log in to your SHOPLINE store backend --> Settings --> Payment Service Provider (Search: UseePay) --> Bind the relevant information --> Activate

### 1) Enter your email and password to log in:

Email:

Password:

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568327/image-preview)

### 2) Credit/Debit Card Payment (Click "Add Payment Service Provider" and search: UseePay)

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568328/image-preview)

### 3) Add the Merchant ID and Sign Key (Note: The type of key obtained differs based on the website-building method. For SHOPLINE it is: MD5 Key). Check the supported payment methods --> Confirm to activate UseePay.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568330/image-preview)

### 4) Log in to the UseePay MC backend to obtain the Merchant ID and Key

(URL: https://mc.useepay.com. The login account and password are the ones set during activation. The operator is usually: admin. If you encounter issues logging in, please contact the operations personnel in your designated support group.)

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568331/image-preview)

**II. Other Operations**

1.  Consumer email is mandatory. Please set up email checkout in your SHOPLINE backend; otherwise, transactions may fail. The specific steps are shown in the image below:
    Click 【Settings】>【Checkout】>【Checkout Form Options】.

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568332/image-preview)

2.  Transaction Testing:

    Log in to your company's store website and proceed to pay for any item priced above $1.

    Card number selection guidelines for the payment step:

    1.  Use your own real card number.

        PS: If a real card is used for payment, certain fixed and percentage fees will be charged according to your contract.

    2.  Use a test card for testing: <span style="color:red;">4111111111111111

        PS: Enter a valid expiration date and a reasonable CVV. Since the test card will show an unsuccessful order, i.e., <span style="color:red;">transaction failed, a processing fee will be charged as per the contract.

    After payment, a successful or failed transaction order can be viewed in MC Backend -> Transaction Query.