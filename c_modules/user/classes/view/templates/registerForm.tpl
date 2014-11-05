{$css_includes}
{$js_includes}

{$registerFormOpen}
  {foreach from=$registerFormFields key=key item=field}
    {$field}
  {/foreach}
{$registerFormClose}
{$registerFormValidations}
