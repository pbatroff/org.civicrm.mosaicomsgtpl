{*-------------------------------------------------------+
| Mosaicomsgtpl Extension                                |
| Author: P. Batroff (batroff@systopia.de)               |
+-------------------------------------------------------*}

{*<h3>{ts domain='org.civicrm.mosaicomsgtpl'}Synchronization Configuration{/ts}</h3>*}

<div class="crm-section mosaicomsgtpl">
    <div class="crm-section">
        <div class="label">{$form.mosaico_msg_template_name_filter.label} <a onclick='CRM.help("{ts domain="org.civicrm.mosaicomsgtpl"}{/ts}", {literal}{"id":"id-beginning_regex","file":"CRM\/Mosaicomsgtpl\/Form\/Settings"}{/literal}); return false;' href="#" title="{ts domain="org.civicrm.mosaicomsgtpl"}Help{/ts}" class="helpicon">&nbsp;</a></div>
        <div class="content">{$form.mosaico_msg_template_name_filter.html}</div>
        <div class="clear"></div>
    </div>
    &nbsp;    <div class="crm-section">
        <div class="label">{$form.mosaico_global_sync_activated.label}</div>
        <div class="content">{$form.mosaico_global_sync_activated.html}</div>
        <div class="clear"></div>
    </div>
</div>


<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
