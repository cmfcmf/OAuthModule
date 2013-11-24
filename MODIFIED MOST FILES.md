# Modified MOST files.
This file gives an overview of all the modified MOST files. Pay attention to these files on regeneration!

- `composer.json`: Added `"lusitanian/oauth": "dev-master"` to requirements.
- `OAuthModuleVersion.php`: Added better display name and authentication capability.
- `Controller/AdminController.php`: `indexAction` shall redirect to `configAction` instead of `viewAction`.
- `Entity/Repository/User.php`: Fixed MOST bug with the request not beeing set in `selectWhere()`.
- `Form/Handler/Admin/ConfigHandler.php`: Assign providers to template and properly save module variables.
- `Resources/views/Admin/config.tpl`: Added proper configuration screen.