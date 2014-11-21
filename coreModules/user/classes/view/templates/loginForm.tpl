{$css_includes}
{$js_includes}

{$loginFormOpen}
  {foreach from=$loginFormFields key=key item=field}
    {$field}
  {/foreach}
{$loginFormClose}
{$loginFormValidations}
