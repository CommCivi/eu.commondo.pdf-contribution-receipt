<table class="display" id="option11" style="margin-top: 20px; margin-bottom: 30px;">
    <thead>
    <tr>
        <th rowspan="1" colspan="1" style="font-size: 18px;">{ts}Title{/ts}</th>
        <th style="width:10%; font-size: 18px;"></th>
        <th style="width:10%; font-size: 18px;"></th>
    </tr>
    </thead>
    <tbody>
    {if $templates }
        {foreach from=$templates key=key item=template}

            {assign var="templateId" value=$template.id}
            <tr class="{cycle values="odd-row,even-row"}">
                <td style="font-size: 16px;">{$template.title}</td>
                <td style="text-align: center; font-size: 14px;"><a
                            href="{crmURL p='civicrm/pdf-contribution-receipt/new-template' q="edit=$templateId"}">Edit</a></td>
                <td style="text-align: center; font-size: 14px;"><a
                            href="{crmURL p='civicrm/pdf-contribution-receipt/templates' q="delete=$templateId"}">Delete</a>
                </td>
            </tr>
        {/foreach}
    {else}
        <div>No templates created. Create your first template.</div>
    {/if}

    </tbody>
</table>
<div style="margin-top: 20px;">
    <a href="{crmURL p='civicrm/pdf-contribution-receipt/new-template'}"><span class="crm-button"
                                                                           style="padding: 5px 10px 5px 10px; font-size: 16px;">Add new template</span></a>
</div>