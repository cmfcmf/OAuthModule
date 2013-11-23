# Modified MOST files.
This file gives an overview of all the modified MOST files. Pay attention to these files on regeneration!

- `composer.json`: Added `"lusitanian/oauth": "~0.2"` to requirements.
- `OAuthModuleVersion.php`: Added better display name and authentication capability.
- `bootstrap.php`: Added call of composer autoloader: `require_once(__DIR__ . '/vendor/autoload.php');`.
- `Controller/AdminController.php`: `indexAction` shall redirect to `configAction` instead of `viewAction`.
- `Entity/Repository/User.php`: Fixed MOST bug with the request not beeing set in `selectWhere()`.