
> 在开始之前，请确保您的 Shopify 店铺账户拥有该 Shopify 店铺后台设置Payment methods权限

### **安装UseePay Payment（本地化）**
1)<span style="color:red;">连接VPN并登录shopify后台，网页框输入：https://apps.shopify.com/local-payment-1 添加该应用

<span style="color:red;">**注意**：请添加Local Payment的app应用，不要勾选其他app应用


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568301/image-preview)



Security Key获取(需登录MC后台-配置中心-域名管理，点击详情查看)：https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590328m0

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568303/image-preview)

商户号信息获取（需登录MC后台-账号管理-账号信息查看）：https://apifox.com/apidoc/shared/aa960074-646d-4844-9160-2df57e7f73f7/6590322m05

**点击Install**

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568305/image-preview)
2)选择对应的本地化支付方式进行勾选，并激活Active，**请勿勾选测试模式**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568307/image-preview)
**如您开通了Afterpay支付，需单独展示Afterpay支付，请在网页框输入**：https://apps.shopify.com/afterpay-3?locale=zh-CN，添加应用

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568308/image-preview)
**如您开通了klarna支付，需单独展示klarna支付，请在网页框输入**：https://apps.shopify.com/klarna-useepay?st_source=autocomplete添加应用


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568310/image-preview)
<span style="color:red;">**3)请在shopify后台设置以邮箱结账，否则会导致交易失败。具体操作方式见如下图：**


![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568311/image-preview)

<span style="color:red;">**4)以上全部设置完成后请进行交易测试：**

登录贵司店铺网站，任意选择大于1美元的商品进行支付

支付环节卡号选择说明：

1.选择自己真实的卡号

ps：若是使用真实的卡进行支付，会根据合同收取一定手续费和比例费。

2.选择假卡进行测试：<span style="color:red;">4111111111111111

ps：卡号有效期及cvv填写合理即可，由于测试卡会显示订单不成功即<span style="color:red;">交易失败，会根据合同收取处理费

支付后均可在MC后台-->交易查询中看到一笔交易成功/失败的订单

<span style="color:red;">**5)物流插件绑定**

**<span style="color:red;">为了不影响您订单结算请shopfiy建站网站在MC后台绑定物流插件并同步。**

MC后台链接：https://mc.useepay.com/
<span style="color:red;">物流管理-物流绑定-点击新增并查根据操作手册新增添加

![image.png](https://api.apifox.com/api/v1/projects/6993167/resources/568313/image-preview)
