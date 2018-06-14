{* HEADER *}
<!--{literal}-->
<script language="JavaScript" type="text/JavaScript">
    <!--
    var templateId = {/literal} {$templateId} {literal};
    -->
</script><!--{/literal}-->
<div style="width: 94%;">
    <span class="crm-submit-buttons" style="float: left;">
        {include file="CRM/common/formButtons.tpl" location="top"}
    </span>
    <span style="float: right;">
    <a href="{crmURL p='civicrm/pdf-contribution-receipt/new-template'}"><span
                class="crm-button" style="padding: 5px 2px 5px 2px; margin-right: 30px;">Add new template</span></a>
    <a href="{crmURL p='civicrm/pdf-contribution-receipt/templates'}"><span
                class="crm-button" style="padding: 5px 2px 5px 2px; margin-right: 30px;">Back to template list</span></a>
    </span>
</div>
<div class="clear"></div>
<div style="margin-top: 20px; max-width: 1000px;">
    {foreach from=$elementNames item=elementName}
        <div class="crm-section">
            <div class="" style="font-size: 18px; margin-bottom: 20px; margin-top: 30px;">{$form.$elementName.label}</div>
            <div class="">{$form.$elementName.html}</div>
            <div class="clear"></div>
        </div>
    {/foreach}
<div style="width:94%">
    <span class="crm-submit-buttons" style="float: left">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </span>
    {if $templateId}
        <span style="float: right; margin-top: 10px;">
    <a href="{crmURL p='civicrm/pdf-contribution-receipt/templates' q="delete=$templateId"}"><span
                class="" style="padding: 5px 2px 5px 2px; margin-right: 30px;">Delete template</span></a>
    </span>
    {/if}
</div>
</div>
<div class="clear"></div>
<div style="margin-top: 20px; font-size: 16px;">
    <p style="font-weight: 700;">Currently available short codes:
    </p>
    <p>[[first_name]], [[last_name]], [[gender]], [[contribution_amount]], [[street_address]], [[postal_code]], [[city]]
        [[donation_period]] [[date]] [[prefix]] [[suffix]]</p>
    <p>Need more additional codes? Contact developer.</p>
</div>

{* FOOTER *}
<div class="clear"></div>