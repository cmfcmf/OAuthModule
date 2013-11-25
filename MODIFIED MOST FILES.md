# Modified MOST files.
This file gives an overview of all the modified MOST files. Pay attention to these files on regeneration!

- `composer.json`: Added `"lusitanian/oauth": "dev-master"` to requirements.
- `OAuthModuleVersion.php`: Added better display name and authentication capability.
- `Controller/AdminController.php`: `indexAction` shall redirect to `configAction` instead of `viewAction`.
- `Controller/UserController.php`: Tweaked the security check of the `viewAction`.
- `Entity/Repository/User.php`: Fixed MOST bug with the request not beeing set in `selectWhere()`.
- `Form/Handler/Admin/ConfigHandler.php`: Assign providers to template and properly save module variables.
- `Resources/views/Admin/config.tpl`: Added proper configuration screen.
- `workflows/function.none_permissioncheck.php`: Do not allow deleting the mapped id if it is the only authentication
  method. Check permission by `createdUserId` if checking by current user fails.
- `OAuthModuleInstaller.php` Added permission rule creation.