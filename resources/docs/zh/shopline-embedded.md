
### 登录shopline店铺后台-->设置-->支付服务提供商（搜索：UseePay）-->绑定相关信息-->激活

### 1) 输入邮箱和密码登录：

邮箱:

密码：

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568327/image-preview)

### 2）信用卡/借记卡收款 （点击添加支付服务商搜索：UseePay）

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568328/image-preview)

### 3）添加商户号和sign key（注意不同建站方式获取的密钥的类型不同，shopline为:MD5密钥）并勾选支持的付款方式-->确认后激活UseePay


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568330/image-preview)

### 4) 登录UseePay-MC后台获取商户号及密钥

（网址：https://mc.useepay.com，登录账号和密码为激活时设置的密码和账号，操作员一般为：admin，如遇无法登陆等问题请联系对接群中运营人员）


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568331/image-preview)
**二、其他操作**

1.消费者邮箱必填请在shopline后台设置以邮箱结账，否则会导致交易失败，具体操作方式见如下图：
点击 【设置】>【结账】>【结账表单选项】。

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568332/image-preview)

2.交易测试：

登录贵司店铺网站，任意选择大于1美元的商品进行支付

支付环节卡号选择说明：

1.选择自己真实的卡号

ps：若是使用真实的卡进行支付，会根据合同收取一定手续费和比例费。

2.选择假卡进行测试：<span style="color:red;">4111111111111111

ps：卡号有效期及cvv填写合理即可，由于测试卡会显示订单不成功即<span style="color:red;">交易失败，会根据合同收取处理费

支付后均可在MC后台-->交易查询中看到一笔交易成功/失败的订单