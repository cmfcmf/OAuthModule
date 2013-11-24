{* purpose of this template: users xml inclusion template in admin area *}
<user id="{$item.id}" createdon="{$item.createdDate|dateformat}" updatedon="{$item.updatedDate|dateformat}">
    <id>{$item.id}</id>
    <userId>{usergetvar name='uname' uid=$item.userId}</userId>
    <claimedId><![CDATA[{$item.claimedId}]]></claimedId>
    <provider><![CDATA[{$item.provider}]]></provider>
    <workflowState>{$item.workflowState|cmfcmfoauthmoduleObjectState:false|lower}</workflowState>
</user>
