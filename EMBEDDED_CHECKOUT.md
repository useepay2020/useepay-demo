1. Include UseePay SDK:
<script src="https://checkout-sdk.useepay.com/1.0.1/useepay.min.js"></script>
2. Verify SDK Availability:
   if (window.UseePay) {
   // SDK loaded successfully
   } else {
   // SDK failed to load
   }
3. Initialize UseePay with your OpenAPI Public Key:
   const useepay = UseePay('UseePay_PK_TEST_1234')
   🧠 Note:
   You do not need to manually specify whether you’re using sandbox or production.
   UseePay SDK will automatically detect the environment based on the prefix of your public key:

PK_TEST_**** → sandbox

PK_**** → production

4. Initialize UseePay Elements:
   const elements = useepay.elements({ clientSecret, paymentIntentId })
   Example:

// PaymentIntent.id = 1012507081957803516
// PaymentIntent.clientSecret = 3707fc886bbba446f3c5dacfd13d46d4

const elements = useepay.elements({clientSecret: '3707fc886bbba446f3c5dacfd13d46d4', paymentIntentId: '1012507081957803516'})
5. Create the Payment Element:
   const paymentElement = elements.create('payment')
6. Mount the Element to the DOM:
   paymentElement.mount(ElementId)
   Example:

paymentElement.mount('payment-element') // where 'payment-element' is the container ID
7. Confirm the Payment:
   const { paymentIntent, error } = await useepay.confirmPayment({ elements })