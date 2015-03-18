# PTel-Adapters #

It's a small library for creating automated scripts for some devices (Cisco, DLink, etc) via telnet.
All 'adapters' based on [PTel](https://github.com/bosha/PTel) library.

In most cases PTel library itself can handle all of those things, but some
devices required additional setup, parameters, etc (for example: some devices
does not require username, only password; privilege levels, custom string and
page delimiters, and so on..). Creating your custom
adapters can reduce your keystrokes and increase readablity.

I created adapters only for devices i've worked with. If you created adapters for
some device - fill free to "pull". :)

## Installation ##

Package can be easily installed via composer. Inside folder with your project
simply run:

```bash
composer require bosha/ptel-adatpters
```

Or add to your composer.json:

```json
"require" : {
    "bosha/ptel-adapters" : "dev-master"
}
```

And run:

```bash
composer update
```

## Using ##

```php
try {
    // Connect
    $cisco = new PTel_Adapters\Cisco\Catalyst();
    $cisco->connect("yoursupercisco.com");
    $cisco->login("username", "password");

    // Some helpful functions:
    $cisco->enable("enable password here");
    $cisco->saveConfiguration();
    $running_config = $cisco->getOutputOf("show running");
    $cisco->logout();
} catch (Exception $e) {
    echo "There was error while running script: " . $e->getMessage();
}
```

For another methods see [PTel README](https://github.com/bosha/PTel/blob/master/README.md#using-ptel).

## Creating your own 'adapters' ##

Just create class, which extends PTel, setup public variables, create new
methods or rewrite exists to suit your needs. Also see adapters adapters from
this repo.
