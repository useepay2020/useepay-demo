
> 在开始之前，请确保您的 Shopify 店铺账户拥有该 Shopify 店铺后台设置Payment methods权限且MC后台域名审核通过
> 
**一、安装UseePay Payment （直连，仅支持信用卡V/M/DC/AE/JCB）**

1.登录shopify后台点击收款页面，查看所有提供商，搜索UseePay选择<span style="color:red;">**integration-credit-debit-card**</span>进行安装

如无法搜索请点击链接进行安装：https://apps.shopify.com/integration-credit-debit-card

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568276/image-preview)
1.2 <span style="color:red;">如果您是使用Shopify Plus版本，请在店铺后台的收款 (Payments) 设置界面点击管理 (Manage)，然后点击第三方支付，再选择UseePay的integration-credit-debit-card进行安装。

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568278/image-preview)

**2. 输入对应商户号，点击Submit**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568280/image-preview)
Security Key获取(需登录MC后台-配置中心-域名管理，点击详情查看)：https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590328m0

商户号信息获取（需登录MC后台-账号管理-账号信息查看）：https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590322m0
3. 选择对应的支付方式，**注意不要勾选测试模式**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568281/image-preview)
**4. 激活成功后，在Payments可显示对应的integration-credit-debit-card支付方式**

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568282/image-preview)
**二、其他操作**
**<span style="color:red;">1. 消费者邮箱必填**
**<span style="color:red;">请在shopify后台设置以邮箱结账**，否则会导致交易失败，具体操作方式见如下图：



![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568284/image-preview)

**<span style="color:red;">2. 交易测试**
登录贵司店铺网站，任意选择大于1美元的商品进行支付

支付环节卡号选择说明：

1）选择自己真实的卡号

ps：若是使用真实的卡进行支付，会根据合同收取一定手续费和比例费。

2）选择假卡进行测试：<span style="color:red;">4111111111111111

ps：卡号有效期（超过当前日期）及cvv填写合理即可，由于测试卡会显示订单不成功即<span style="color:red;">交易失败，会根据合同收取处理费

支付后均可在MC后台-->交易查询中看到一笔交易成功/失败的订单

<span style="color:red;">**3. 物流插件绑定**
**为了不影响您订单结算请shopfiy建站网站在MC后台绑定物流插件并同步。**

MC后台链接：https://mc.useepay.com/
物流管理-物流绑定-点击新增并查根据操作手册新增添加

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568286/image-preview)