# Create Checkout Session

## OpenAPI Specification

```yaml
openapi: 3.0.1
info:
  title: ''
  description: ''
  version: 1.0.0
paths:
  /api/v1/checkout_sessions:
    post:
      summary: Create Checkout Session
      deprecated: false
      description: >-
        Create a payment session, supporting one-time payment (payment) and
        subscription payment (subscription) modes
      tags:
        - developer/Checkout Session
        - Checkout Session
      parameters:
        - name: x-merchant-no
          in: header
          description: ''
          required: true
          example: '{{x-merchant-no}}'
          schema:
            type: string
        - name: x-api-key
          in: header
          description: ''
          required: true
          example: '{{x-api-key}}'
          schema:
            type: string
        - name: x-app-id
          in: header
          description: ''
          required: true
          example: '{{x-app-id}}'
          schema:
            type: string
        - name: x-api-version
          in: header
          description: ''
          required: false
          example: '{{x-api-version}}'
          schema:
            type: string
      requestBody:
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/CheckoutSessionCreateRequest'
            examples:
              One-time Payment:
                value:
                  mode: payment
                  ui_mode: custom
                  amount: 99.99
                  currency: USD
                  merchant_order_id: order_abc123xyz456
                  customer:
                    email: customer@example.com
                    name: John Doe
                    phone: '+1234567890'
                    merchant_customer_id: cust_merchant_123456
                  line_items:
                    - quantity: 1
                      price_data:
                        product_data:
                          name: Premium Product
                          desc: High quality product with premium features
                        unit_amount: 99.99
                        currency: USD
                  payment_method_types:
                    - card
                    - apple_pay
                    - google_pay
                  metadata:
                    order_type: online
                    source: web
                    campaign: summer_sale
                summary: One-time payment example
              Subscription Without Discount:
                value:
                  mode: subscription
                  ui_mode: custom
                  amount: 99.99
                  currency: USD
                  merchant_order_id: sub_order_def789ghi012
                  customer:
                    email: subscriber@example.com
                    name: Premium User
                    phone: '+1234567890'
                    merchant_customer_id: cust_sub_merchant_789012
                  line_items:
                    - quantity: 1
                      price_data:
                        product_data:
                          name: Premium Monthly Plan
                          desc: Unlimited access to all premium features
                        unit_amount: 99.99
                        currency: USD
                        recurring:
                          interval: month
                          interval_count: 1
                          total_billing_cycles: 12
                  subscription_data: {}
                  collection_method: auto_charge
                  payment_method_types:
                    - card
                  metadata:
                    subscription_type: premium
                    billing_cycle: monthly
                summary: Subscription payment example (without discount period)
              Subscription With Discount:
                value:
                  mode: subscription
                  ui_mode: custom
                  amount: 99.99
                  currency: USD
                  merchant_order_id: sub_order_jkl345mno678
                  customer:
                    email: trial@example.com
                    name: Trial User
                    phone: '+1234567890'
                    merchant_customer_id: cust_trial_merchant_345678
                  line_items:
                    - quantity: 1
                      price_data:
                        product_data:
                          name: Premium Monthly Plan
                          desc: Unlimited access with first month discount
                        unit_amount: 99.99
                        currency: USD
                        recurring:
                          interval: month
                          interval_count: 1
                          total_billing_cycles: 12
                  subscription_data:
                    discount_period_config:
                      discount_period_count: 1
                      discount_period_amount: 0.01
                  collection_method: auto_charge
                  payment_method_types:
                    - card
                    - apple_pay
                    - google_pay
                  metadata:
                    subscription_type: premium
                    billing_cycle: monthly
                    trial_offer: first_month_99_percent_off
                summary: Subscription payment example (with first month discount)
      responses:
        '200':
          description: Successfully created
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/CheckoutSessionResponse'
              example:
                id: cs_1A2B3C4D5E6F7G8H
                object: checkout.session
                mode: subscription
                ui_mode: custom
                amount: 99.99
                currency: USD
                merchant_order_id: sub_order_jkl345mno678
                status: open
                customer:
                  id: cus_9Z8Y7X6W5V4U3T
                  email: trial@example.com
                  name: Trial User
                  phone: '+1234567890'
                  merchant_customer_id: cust_trial_merchant_345678
                line_items:
                  - quantity: 1
                    price_data:
                      product_data:
                        name: Premium Monthly Plan
                        desc: Unlimited access with first month discount
                      unit_amount: 99.99
                      currency: USD
                      recurring:
                        interval: month
                        interval_count: 1
                        total_billing_cycles: 12
                subscription_data:
                  discount_period_config:
                    discount_period_count: 1
                    discount_period_amount: 0.01
                collection_method: auto_charge
                payment_method_types:
                  - card
                  - apple_pay
                  - google_pay
                metadata:
                  subscription_type: premium
                  billing_cycle: monthly
                  trial_offer: first_month_99_percent_off
                created: 1705228800
                expires_at: 1705315200
          headers: {}
          x-apifox-name: ''
        '400':
          description: Invalid request parameters
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
              examples:
                Missing Required Field:
                  summary: Missing Required Field
                  value:
                    code: invalid_request_error
                    message: mode required
                    source: null
                Amount Mismatch:
                  summary: Amount Mismatch
                  value:
                    code: invalid_request_error
                    message: amount not equals to item lines
                    source: null
                Subscription Missing Config:
                  summary: Subscription Missing Config
                  value:
                    code: invalid_request_error
                    message: subscription_data required for subscription mode
                    source: null
          headers: {}
          x-apifox-name: ''
      security: []
      x-apifox-folder: developer/Checkout Session
      x-apifox-status: testing
      x-run-in-apifox: https://app.apifox.com/web/project/5337387/apis/api-405702890-run
components:
  schemas:
    CheckoutSessionCreateRequest:
      type: object
      required:
        - mode
        - ui_mode
        - amount
        - currency
        - merchant_order_id
        - line_items
        - billing
      properties:
        mode:
          type: string
          enum:
            - payment
            - subscription
          description: >-
            Payment mode. Enum values: payment (one-time payment), subscription
            (recurring subscription payment)
          examples:
            - subscription
        ui_mode:
          type: string
          enum:
            - custom
          default: custom
          description: >-
            UI rendering mode. Currently only supports 'custom' (custom
            frontend). Enum values: custom
          examples:
            - custom
        amount:
          type: number
          format: decimal
          minimum: 0.01
          description: Total product amount (must equal the sum of all line_items amounts)
          examples:
            - 99.99
        currency:
          type: string
          description: >-
            Currency code (ISO 4217). Common values: USD, EUR, GBP, CNY, JPY,
            etc.
          examples:
            - USD
        merchant_order_id:
          type: string
          description: Merchant order ID (unique identifier)
          examples:
            - order_abc123xyz456
          maxLength: 64
        customer_id:
          type: string
          description: Existing customer ID (if provided, customer object is not required)
        customer: &ref_4
          $ref: '#/components/schemas/Customer'
          description: Customer information (required if customer_id is not provided)
        line_items:
          type: array
          minItems: 1
          items: &ref_5
            $ref: '#/components/schemas/LineItem'
          description: Product list (at least one item required)
        payment_method_types:
          type: array
          items:
            type: string
            enum:
              - card
              - apple_pay
              - google_pay
          description: 'Supported payment methods. Enum values: card, apple_pay, google_pay'
          examples:
            - - card
              - apple_pay
              - google_pay
        subscription_data: &ref_6
          $ref: '#/components/schemas/SubscriptionData'
          description: Subscription configuration (required when mode=subscription)
        collection_method:
          type: string
          description: >-
            Collection method (must be auto_charge for subscription mode). Enum
            values: auto_charge
          enum:
            - auto_charge
          examples:
            - auto_charge
          nullable: true
        metadata:
          type: object
          additionalProperties: true
          description: Merchant custom metadata
          x-apifox-orders: []
          examples:
            - order_type: online
              source: web
              campaign: summer_sale
          properties: {}
          x-apifox-ignore-properties: []
        shipping:
          $ref: '#/components/schemas/Shipping'
          description: Shipping information
        billing:
          $ref: '#/components/schemas/Billing'
      x-apifox-orders:
        - mode
        - ui_mode
        - amount
        - currency
        - merchant_order_id
        - customer_id
        - customer
        - line_items
        - payment_method_types
        - subscription_data
        - collection_method
        - metadata
        - shipping
        - billing
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    Billing:
      type: object
      properties:
        address: &ref_0
          $ref: '#/components/schemas/Address'
          description: >-
            The billing address as it appears on the credit card issuer’s
            records
        date_of_birth:
          type: string
          description: 'Date of birth of the customer in the format: YYYY-MM-DD.'
        first_name:
          type: string
          description: The customer’s first name.
        last_name:
          type: string
          description: The customer’s last name.
        email:
          type: string
          description: Email address of the customer.
        phone:
          type: string
          description: Phone number of the customer.
      required:
        - first_name
        - last_name
        - email
      x-apifox-orders:
        - address
        - date_of_birth
        - first_name
        - last_name
        - email
        - phone
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    Address:
      type: object
      properties:
        line1:
          type: string
          description: Address line 1
          examples:
            - 123 Main Street
        line2:
          type: string
          description: Address line 2
          examples:
            - Apartment 4B
        city:
          type: string
          description: City
          examples:
            - New York
        state:
          type: string
          description: State/Province
          examples:
            - NY
        postcode:
          type: string
          description: Postal code
          examples:
            - '10001'
        country:
          type: string
          description: Country code (ISO 3166-1 alpha-2)
          examples:
            - US
      x-apifox-orders:
        - line1
        - line2
        - city
        - state
        - postcode
        - country
      required:
        - line1
        - city
        - state
        - postcode
        - country
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    Shipping:
      type: object
      properties:
        name:
          type: string
          description: Recipient name
          examples:
            - John Doe
        phone:
          type: string
          description: Recipient phone
          examples:
            - '+1234567890'
        address: *ref_0
      x-apifox-orders:
        - name
        - phone
        - address
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    SubscriptionData:
      type: object
      properties:
        discount_period_config:
          $ref: '#/components/schemas/DiscountPeriodConfig'
          description: Discount period configuration (optional)
      x-apifox-orders:
        - discount_period_config
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    DiscountPeriodConfig:
      type: object
      required:
        - discount_period_count
        - discount_period_amount
      properties:
        discount_period_count:
          type: integer
          minimum: 1
          description: Number of discount periods (e.g., 1 means first period discount)
          examples:
            - 1
        discount_period_amount:
          type: number
          format: decimal
          minimum: 0.01
          description: Discount period amount (must be less than regular amount)
          examples:
            - 0.01
      x-apifox-orders:
        - discount_period_count
        - discount_period_amount
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    LineItem:
      type: object
      required:
        - quantity
        - price_data
      properties:
        quantity:
          type: integer
          minimum: 1
          description: Product quantity (must be >= 1)
          examples:
            - 1
        price_data:
          type: object
          x-apifox-refs:
            01KF011A7NGG5AX6AMZ2EQJ60D:
              $ref: '#/components/schemas/PriceData'
              x-apifox-overrides:
                currency: null
          x-apifox-orders:
            - 01KF011A7NGG5AX6AMZ2EQJ60D
          properties:
            product_data: &ref_1
              $ref: '#/components/schemas/ProductData'
            unit_amount: &ref_2
              type: number
              format: decimal
              minimum: 0.01
              description: Unit price (must be >= 0.01)
              examples:
                - 99.99
            recurring: &ref_3
              $ref: '#/components/schemas/Recurring'
              description: Recurring configuration (required when mode=subscription)
          required:
            - product_data
            - unit_amount
          x-apifox-ignore-properties:
            - product_data
            - unit_amount
            - recurring
      x-apifox-orders:
        - quantity
        - price_data
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    Recurring:
      type: object
      required:
        - interval
        - unit_amount
        - interval_count
      properties:
        interval:
          type: string
          enum:
            - day
            - week
            - month
            - year
          description: 'Billing cycle unit. Enum values: day, week, month, year'
          examples:
            - month
        unit_amount:
          type: string
          description: The maximum allowable deduction amount
        interval_count:
          type: integer
          minimum: 1
          description: >-
            Billing cycle count (e.g., interval=month, interval_count=1 means
            monthly)
          examples:
            - 1
        total_billing_cycles:
          type: integer
          minimum: 1
          description: Total billing cycles (if not provided, means unlimited subscription)
          examples:
            - 12
      x-apifox-orders:
        - interval
        - unit_amount
        - interval_count
        - total_billing_cycles
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    ProductData:
      type: object
      required:
        - name
      properties:
        name:
          type: string
          description: Product name (required)
          examples:
            - Premium Monthly Plan
        desc:
          type: string
          description: Product description
          examples:
            - Unlimited access to all premium features
      x-apifox-orders:
        - name
        - desc
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    PriceData:
      type: object
      required:
        - product_data
        - unit_amount
      properties:
        product_data: *ref_1
        unit_amount: *ref_2
        currency:
          type: string
          description: >-
            Currency code (if not provided, defaults to CheckoutSession's
            currency)
          examples:
            - USD
        recurring: *ref_3
      x-apifox-orders:
        - product_data
        - unit_amount
        - currency
        - recurring
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    Customer:
      type: object
      required:
        - name
        - merchant_customer_id
      properties:
        id:
          type: string
          description: Customer ID (if already exists)
          examples:
            - cus_9Z8Y7X6W5V4U3T
        email:
          type: string
          format: email
          description: Customer email (at least one of email or phone is required)
          examples:
            - customer@example.com
        phone:
          type: string
          description: Customer phone number (at least one of email or phone is required)
          examples:
            - '+1234567890'
        name:
          type: string
          description: Customer name (required)
          examples:
            - John Doe
        merchant_customer_id:
          type: string
          description: Merchant's customer ID (required)
          examples:
            - cust_merchant_123456
      x-apifox-orders:
        - id
        - email
        - phone
        - name
        - merchant_customer_id
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    CheckoutSessionResponse:
      type: object
      properties:
        id:
          type: string
          description: Checkout Session ID
          examples:
            - cs_1A2B3C4D5E6F7G8H
        object:
          type: string
          enum:
            - checkout.session
          description: 'Object type. Enum values: checkout.session'
          examples:
            - checkout.session
        mode:
          type: string
          enum:
            - payment
            - subscription
          description: 'Payment mode. Enum values: payment, subscription'
          examples:
            - subscription
        ui_mode:
          type: string
          enum:
            - custom
          description: 'UI mode. Enum values: custom'
          examples:
            - custom
        amount:
          type: number
          description: Amount
          examples:
            - 99.99
        currency:
          type: string
          description: Currency
          examples:
            - USD
        merchant_order_id:
          type: string
          description: Merchant order ID
          examples:
            - sub_order_jkl345mno678
          maxLength: 64
        status:
          type: string
          description: >-
            Session status. Enum values: open (awaiting payment), complete
            (payment completed), expired (session expired)
          enum:
            - open
            - complete
            - expired
          examples:
            - open
          x-apifox-enum:
            - value: open
              name: ''
              description: >-
                The checkout session is still ongoing, and payment processing
                hasn't begun yet.
            - value: complete
              name: ''
              description: >-
                The checkout session has finished, but payment processing might
                still be underway.
            - value: expired
              name: ''
              description: The session has expired.
        payment_status:
          type: string
          enum:
            - unpaid
            - paid
            - no_payment_required
          x-apifox-enum:
            - value: unpaid
              name: ''
              description: A checkout session has not been paid.
            - value: paid
              name: ''
              description: The checkout session has been paid.
            - value: no_payment_required
              name: ''
              description: The checkout session need not paid.
        customer: *ref_4
        line_items:
          type: array
          items: *ref_5
        subscription_data: *ref_6
        collection_method:
          type: string
          enum:
            - auto_charge
          description: 'Collection method. Enum values: auto_charge'
          examples:
            - auto_charge
        payment_method_types:
          type: array
          items:
            type: string
            enum:
              - card
              - apple_pay
              - google_pay
          examples:
            - - card
              - apple_pay
              - google_pay
        metadata:
          type: object
          additionalProperties: true
          x-apifox-orders: []
          examples:
            - subscription_type: premium
              billing_cycle: monthly
          properties: {}
          x-apifox-ignore-properties: []
        created:
          type: integer
          description: Creation time (Unix timestamp)
          examples:
            - 1705228800
        expires_at:
          type: integer
          description: Expiration time (Unix timestamp)
          examples:
            - 1705315200
          x-apifox-mock: '{{$date.isoTimestamp}}'
        client_secret:
          type: string
          description: cs_11LP8EM9RJK00_0062a681694943f
        subscription_id:
          type: string
          description: subscription id
        payment_intent_id:
          type: string
          description: payment_intent_id
      x-apifox-orders:
        - id
        - object
        - mode
        - ui_mode
        - amount
        - currency
        - merchant_order_id
        - status
        - payment_status
        - customer
        - line_items
        - subscription_data
        - collection_method
        - payment_method_types
        - metadata
        - created
        - expires_at
        - client_secret
        - subscription_id
        - payment_intent_id
      required:
        - mode
        - ui_mode
        - amount
        - currency
        - merchant_order_id
        - client_secret
        - subscription_id
        - payment_intent_id
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
    ErrorResponse:
      type: object
      properties:
        code:
          type: string
          description: Error code
          examples:
            - resource_not_found
        message:
          type: string
          description: Error message
          examples:
            - Checkout Session not found
        source:
          type: string
          description: Error source
          examples:
            - null
          nullable: true
      x-apifox-orders:
        - code
        - message
        - source
      x-apifox-ignore-properties: []
      x-apifox-folder: ''
  securitySchemes:
    ApiKeyAuth:
      type: apikey
      in: header
      name: Authorization
      description: 'API Key, format: Bearer {your_api_key}'
servers:
  - url: https://openapi.useepay.com
    description: Prod Env
  - url: https://openapi1.uat.useepay.com
    description: Useepay API V2.0 Sandbox Environment
security:
  - ApiKeyAuth: []
    x-apifox:
      schemeGroups:
        - id: 2-wSr0BKi_j9qB92N-khx
          schemeIds:
            - ApiKeyAuth
      required: true
      use:
        id: 2-wSr0BKi_j9qB92N-khx
      scopes:
        2-wSr0BKi_j9qB92N-khx:
          ApiKeyAuth: []

```