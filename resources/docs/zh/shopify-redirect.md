

**一、安装UseePay Payment （跳转收银台）**

1. <span style="color:red;">连接VPN并登录shopify后台，网页框输入：<span style="color:red;">https://apps.shopify.com/2-advanced-credit-debit-card，添加应用。

<span style="color:red;">**注意**：请添加2-advanced-credit-debit-card的app应用，不要勾选其他app应用

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568287/image-preview)
**2. 点击Install app-->填写绑定信息-->进行激活**<span style="color:red;">（**需注意勿勾选JCB及测试模式，支付方式请根据网站实际支付情况勾选）**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568288/image-preview)

Security Key获取(需登录MC后台-配置中心-域名管理，点击详情查看)：https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590328m0

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568289/image-preview)
商户号信息获取（需登录MC后台-账号管理-账号信息查看）：https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590322m0

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568290/image-preview)


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568291/image-preview)
**二、其他操作**

**<span style="color:red;">1. 消费者邮箱必填**
**<span style="color:red;">请在shopify后台设置以邮箱结账，否则会导致交易失败**，具体操作方式见如下图：


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568292/image-preview)

**<span style="color:red;">2. 交易测试**
登录贵司店铺网站，任意选择大于1美元的商品进行支付

支付环节卡号选择说明：

1）选择自己真实的卡号

ps：若是使用真实的卡进行支付，会根据合同收取一定手续费和比例费。

2）选择假卡进行测试：<span style="color:red;">4111111111111111

ps：卡号有效期（超过当前日期）及cvv填写合理即可，由于测试卡会显示订单不成功即<span style="color:red;">交易失败，会根据合同收取处理费

支付后均可在MC后台-->交易查询中看到一笔交易成功/失败的订单

**<span style="color:red;">3. 物流插件绑定**
<span style="color:red;">为了不影响您订单结算请shopfiy建站网站在MC后台绑定物流插件并同步。

MC后台链接：https://mc.useepay.com/
物流管理-物流绑定-点击新增并查根据操作手册新增添加

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568293/image-preview)