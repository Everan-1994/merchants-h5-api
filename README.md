## 第一步：clone 项目后在根目录 执行以下命令 进行项目依赖安装

```
composer install
```
clone 完毕，根目录新建文件命名为 ```.env``` 复制以下内容为你的真实内容
```
APP_ENV="merchants-h5-api"
APP_LOCALE=zh-CN
APP_DEBUG=true
APP_KEY=wl2QHVL5tENACb8EPDf9QOABntlVGlub
APP_TIMEZONE=PRC
// DB_TIMEZONE=+08:00 如果发现时间少8小时，就去掉前面的双斜杠
APP_URL=http://www.xxx.com // 改为你的域名

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=

// 数据库配置
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=merchants
DB_USERNAME=root
DB_PASSWORD=root

CACHE_DRIVER=file
QUEUE_DRIVER=sync

JWT_TTL=120
JWT_SECRET=4m0QbCKnTxvB2HncxfkfDkRy07E7mATp27FCjB8FrmaN6lsTBeMskcAdsVV2UJV7

// 微信公众号配置
WECHAT_OFFICIAL_ACCOUNT_APPID=appid
WECHAT_OFFICIAL_ACCOUNT_SECRET=secret
WECHAT_OFFICIAL_ACCOUNT_TOKEN=token

// 七牛云配置
QINIU_ACCESS_KEY=
QINIU_SECRET_KEY=
QINIU_BUCKET=
QINIU_DOMAIN=

// 微信模板消息
TEMPLATE_LUCK_DOG= // 活动报名结束，通知用户报名成功的模板id 格式=》 {{first.DATA}} 内容：{{keyword1.DATA}} {{remark.DATA}}
TEMPLATE_WRITE_REPORT= // 通知用户写报告的模板id 格式=》 {{first.DATA}} 内容：{{keyword1.DATA}} {{remark.DATA}}

```

## 第二步：新建数据库，然后将数据库文件导入数据库

注意：无需执行 migrate 和 数据填充

## 第三步：执行 以下命令 生成 jwt 密钥

```php artisan jwt:secret```

## 第四步：修改计划任务脚本，根目录文件 ```cron.txt```
服务器端使用 ```crontab``` 来执行计划任务，具体使用可百度

## 注意事项：
nginx 配置好后，出现访问错误，首先考虑 是否给到 storage/logs 目录 775 以上权限


