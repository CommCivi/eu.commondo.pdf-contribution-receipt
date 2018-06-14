{* HEADER *}

<h3><b>for contact:</b> {$contact.first_name} {$contact.middle_name} {$contact.last_name}</h3>

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
    <div style="margin-top:30px;" class="crm-section">
        <div class="label" style="margin-right: 5px;">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
    </div>
{/foreach}



{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

  <div>
    <span>{$form.favorite_color.label}</span>
    <span>{$form.favorite_color.html}</span>
  </div>

{* FOOTER *}
<div class="crm-submit-buttons" style="margin-top: 30px;">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script>
    CRM.$(document).ready(function () {

        // To/from date fields are initially hidden.
        if(CRM.$("#donation-period").val() !== 'custom') {
            CRM.$("[for=custom-date_to]").parent().parent().hide();
            CRM.$("[for=custom-date_from]").parent().parent().hide();
        }

        // Add date picker to to/from date fields.
        CRM.$('[name=custom-date_to]').crmDatepicker({time: false});
        CRM.$('[name=custom-date_from]').crmDatepicker({time: false});

        // To/from date fields are only shown if 'Custom' option is selected.
        CRM.$("#donation-period").change(function(){
            var currentVal = CRM.$(this).val();

            if(currentVal === 'custom') {
                CRM.$("[for=custom-date_to]").parent().parent().show();
                CRM.$("[for=custom-date_from]").parent().parent().show();
            } else {
                CRM.$("[for=custom-date_to]").parent().parent().hide();
                CRM.$("[for=custom-date_from]").parent().parent().hide();
            }
        });
    });
</script>
{/literal}