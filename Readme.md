# BDevCQRSBundle

Enables CQRS for Symfony2 application based on [LiteCQRS for PHP](https://github.com/beberlei/litecqrs-php) by [Benjamin Eberlei](http://www.whitewashing.de/).

This bundle extends the LiteCQRS Symfony bundle with a different command bus and some useful plugins.

[![Build Status](https://travis-ci.org/boltconcepts/BDevCQRSBundle.png?branch=master)](https://travis-ci.org/boltconcepts/BDevCQRSBundle)

## Installation

### Composer
```
"require" :  {
    // ...
    "bdev/bdev-cqrs-bundle":"dev-master",
}
```

### Register the bundle
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new LiteCQRS\Plugin\SymfonyBundle\LiteCQRSBundle(),
        new BDev\Bundle\RoutingExtraBundle\BDevRoutingExtraBundle(),
    );
    // ...
}
```

### Configure the bundle
```yaml
# app/config/config.yml
bdev_cqrs:
    command_validation: true # default is false
```


## Usage

Let's assume that you have read the [LiteCQRS for PHP Readme](https://github.com/beberlei/litecqrs-php#litecqrs-for-php).

Defining a command or event handler should now be done in the scope "command" (this is not forced just best practice).

The `command_bus` service now expects command triggered from outside the scope to be called using `execute` and not `handle` from within your command/event handlers `handle` should be used.

The `execute` method also has the nice extra that the command handler for this command can return a value/object (this saves you from having to make your controllers event handlers).

```php
<?php
// Controller class
public function someAction() {
    // ...
    $id = $this->get('command_bus')->execute($command);
}
```

### Command Validator

The Command Validator plugin will execute the validator belonging to a command when it is executed/handled.

## TODO

* Add security plugin for the command and event handlers
* Write some good docs