# TGNotifyForEpay
为你的彩虹易支付增添电报机器人通知功能

无需修改易支付数据库。

代码开源，没有授权及各种费用，随意查看使用和下载。

<img src="Telegram (25665) 2022_7_21 18_21_31.png" alt="Telegram (25665) 2022_7_21 18_21_31" style="zoom:75%;" />

# 1.使用aaPanel/删库塔手动部署

## 	1.1确保含有如下软件

- [ ] php>=7
- [ ] Nginx
- [ ] redis

## 	1.2安装Redis（以php7.4为例）

aaPanel 面板 > App Store > 找到PHP 7.4点击Setting > Install extentions > redis进行安装。

删库塔面板 > 应用商店 > 找到PHP 7.4点击设置> 安装扩展 > redis进行安装。

## 	1.3添加站点

## 	1.4安装TGNotifyForEpay

通过SSH登录到服务器后访问站点路径如：/www/wwwroot/你的站点域名。

以下命令都需要在站点目录进行执行。

```shell
# 删除目录下文件
chattr -i .user.ini
rm -rf .htaccess 404.html index.html .user.ini
```

执行命令从 Github 克隆到当前目录。

```shell
git clone https://github.com/yangningmou/TGNotifyForEpay.git ./
```

## 	1.5填写配置

填写config.php的配置

## 	1.6配置定时任务

执行时间按个人需求设定，如果嫌弃宝塔定时任务太慢，可以使用crontab。

通知方式一，需要更改支付插件，文件名notifyv1.php

<<<<<<< HEAD
通知方式二，不需要更改支付插件，文件名notifyv2.php.
=======
通知方式一，不需要更改支付插件，文件名notifyv2.php.
>>>>>>> 577c3dc129aab19daa01563f3b8b2e582cf63dda

aaPanel 面板 > Cron

- 在 Type of Task 选择 Shell Script
- 在 Name of Task 填写 TGNotifyForEpay
- 在 Period 选择 N Minutes 1 Minute
- 在 Script content 填写 php /www/wwwroot/网站路径/文件名.php schedule:run

删库塔面板 > 计划任务

- 在 任务类型 选择 Shell 脚本
- 在 任务名称 填写 TGNotifyForEpay
- 在 执行周期 选择 N Minutes 1 Minute
- 在 脚本内容 填写 php /www/wwwroot/网站路径/文件名.php schedule:run

## 	1.7启动队列服务（可选）

自行摸索

## 	1.8修改易支付

只有选择通知方式一的需要进行这个操作。

将./plugins目录下对应插件替换到相应位置，github目录下没有的插件是还未修改，可自行探索。

```php
//可自行在相应位置添加以下代码
$redis = new Redis();
$redis->connect("127.0.0.1", 6379);
$redis->rPush('orderno',$_GET['out_trade_no']);
$redis->close();
```

==**注意未适配老版易支付 ，请自行寻找新版源码。**==

==**易支付所在的环境也必须有redis和php redis扩展。**==

# 2.使用方法

添加用户

```php+HTML
域名/account.php?pid=商户号&key=密钥&tgid=电报id&type=add
```

删除用户

```php+HTML
域名/account.php?pid=商户号&key=密钥&tgid=电报id&type=del
```

# 3.获得帮助

https://t.me/TalkToJshi

反馈bug建议直接提交issure，不提供无偿安装搭建服务，教程已经写的很清楚了。
