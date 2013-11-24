OAuthModule
===========

An OAuth implementation for Zikula. Currently supported providers:
- GitHub (working)
- Twitter (working)
- Facebook (to be implemented, see #1)
- Google (to be implemented, see #1)

This module is intended for being used with Zikula 1.3.7 and later.

OAuth module generated by ModuleStudio 0.6.1, the modified MOST files can be found [here](https://github.com/cmfcmf/OAuthModule/blob/master/MODIFIED%20MOST%20FILES.md).

**IMPORTANT:** This module is in early developement. There might be **SECURITY RISKS!**

**IMPORTANT:** This module relies on composer. If you are on linux, run the following commands:

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar install

If you don't know what to do, you are propably not a module developer. If you still want to test this module, please
ask me (i.e. via an issue) to provide you a non-composer package of the module.