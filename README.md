# Makaira Headless Extension for Magento 2

[![Packagist Version](https://img.shields.io/packagist/v/codekunst/magento2-makaira)](https://packagist.org/packages/codekunst/magento2-makaira)
[![Build Status](https://travis-ci.org/codekunst/magento2-makaira.svg?branch=main)](https://travis-ci.org/codekunst/magento2-makaira)

This document helps you integrate the Makaira Headless Extension into your Magento 2 Shop.

## Table of contents
- [Requirements](#requirements)
- [Installation](#installation)
- [Activating the Module](#activating-the-module)
    
## Requirements

This module supports:

- Magento 2 version 2.2 and higher
- PHP version 7.1 and higher  
  **Warning**: PHP 7.0 is not supported

## Installation

To install module, open your terminal and run the command:

    composer require codekunst/magento2-makaira:dev-main

Refer to Composer manual for more information. If, for some reason, `composer` is not available globally, proceed to install it following the
instructions available on the [project website](https://getcomposer.org/doc/00-intro.md).

## Activating the Module

From the root of your Magento 2 installation, enter these commands in sequence:

    php bin/magento module:enable Makaira_Headless
    php bin/magento setup:upgrade

As a final step, check the module activation by running:

    php bin/magento module:status

The module should now appear in the upper list *List of enabled modules*.
