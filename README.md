# magento2-import-category-cli
Simple import an array structure as category in your magento2 shop through CLI

## Installation

Upload this repo to your app/code/ and execute the following commands in Magento 2 root folder:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## How to use

Simple acces your SSH to your server and navigate to your Magento2 root folder.
Execute the following code there:

```
php bin/magento bramhammer:importcategories
```

## Options

Execute the command with `-e` (or `--exit`) to stop it at every error.

## Know to work with:

- Magento 2.3.5-p1
