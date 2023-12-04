# [BidBae.tech](bidbae.tech)

# Prerequisite

In order to run BidBae you need to install WAMP (Windows) and MAMP (Mac) and set it up until it runs a template server.

# Dependencies

This project have a PHP, and MySql dependencies that comes in package with the WAMP / MAMP runner installed

1. To fully run our email alerts and scheduler functionality you need to install php globally (unless you want to use php executable under MAMP / WAMP)

```bash
$ brew install php
```

2. cd to your project directory and install composer

```bash
$ curl -sS https://getcomposer.org/installer | php
```

3. install Sendgrid dependencies

```bash
$ php composer.phar require sendgrid/sendgrid
```

# Installation

1. Download MAMP / WAMP and install on your machine
2. Open MAMP / WAMP
3. Start your server and check if it's running on your designated port, default is http://localhost:8888
4. Click on the PHP My Admin or navigate to http://localhost:8888/phpMyAdmin5/index.php
5. Run (copy and paste to inline editor) two of these SQL query on your PHP My Admin

```bash
init.sql
seeder.sql
```

6. Two ways you can run our scheduler system, via cron job or php runner in the background

using cronjob (if you run into problems with $PATH not picking up or crontab not executing, use the php runner)

```
$ crontab 'absolute/path/to/your/folder/auction_cron.php'
```

using php runner
`$ php auction_cron.php`

you should see logs running in your terminal like this

```
[04-Dec-2023 12:51:16 UTC] Executing findInitAuction()
[04-Dec-2023 12:51:16 UTC] Auction ID: 8 has started, updating status
[04-Dec-2023 12:51:16 UTC] Auction ID: 9 has started, updating status
[04-Dec-2023 12:51:16 UTC] Auction ID: 10 has started, updating status
[04-Dec-2023 12:51:16 UTC] Auction ID: 11 has started, updating status
[04-Dec-2023 12:51:16 UTC] Auction ID: 12 has started, updating status
[04-Dec-2023 12:51:21 UTC] Executing findInitAuction()
[04-Dec-2023 12:51:26 UTC] Executing findInitAuction()
```

7. You may now enjoy creating auction with BidBae.tech

# Disclaimer

As we are currently live and deploying only on heroku -- which run on top ephemeral servers, we are using php runner rather than cronjob to automatically update any auction-related states.
