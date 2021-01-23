{* HEADER *}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

<table class="form-layout">
  <tr class="crm-civi-flexmailer-form-block-embedimages">
    <td class="label">{$form.civi_flexmailer_embedimages.html}</td>
    <td>{$form.civi_flexmailer_embedimages.label} {help id=civi_flexmailer_embedimages}</td>
    </td>
  </tr>
  <tr class="crm-civi-flexmailer-form-block-embedimageslocal">
    <td class="label">{$form.civi_flexmailer_embedimageslocal.html}</td>
    <td>{$form.civi_flexmailer_embedimageslocal.label} {help id=civi_flexmailer_embedimageslocal}</td>
    </td>
  </tr>
  <tr class="crm-civi-flexmailer-form-block-flushcache">
    <td class="label">
      <a class="button" href="{crmURL p='civicrm/admin/flexmailer_embedimages' q="flush=1"}"><span>Flush image cache</span></a>
    </td>
    <td>{help id=civi_flexmailer_flushcache}</td>
  </tr>
</table>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
