{*-------------------------------------------------------+
| Mosaicomsgtpl Extension                                |
| Author: P. Batroff (batroff@systopia.de)               |
+-------------------------------------------------------*}

{*<h3>{ts domain='org.civicrm.mosaicomsgtpl'}Synchronization Configuration{/ts}</h3>*}

<div class="crm-section mosaicomsgtpl">
    <div class="crm-section">
        <div class="label">{$form.mosaico_msg_template_name_filter.label}</div>
        <div class="content">{$form.mosaico_msg_template_name_filter.html}</div>
        <div class="clear"></div>
    </div>

    <div class="crm-section">
        <div class="label">{$form.mosaico_global_sync_activated.label}</div>
        <div class="content">{$form.mosaico_global_sync_activated.html}</div>
        <div class="clear"></div>
    </div>
</div>


<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
