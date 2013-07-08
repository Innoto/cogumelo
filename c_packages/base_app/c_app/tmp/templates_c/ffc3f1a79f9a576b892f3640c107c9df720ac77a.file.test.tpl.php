<?php /* Smarty version Smarty-3.1.13, created on 2013-04-05 14:48:19
         compiled from "/home/pblanco/Desarrollo/cogumelo_gcode/branches/1.0a/c_modules/testmodule/templates/test.tpl" */ ?>
<?php /*%%SmartyHeaderCode:447341910515ec337cbdcd7-42239895%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ffc3f1a79f9a576b892f3640c107c9df720ac77a' => 
    array (
      0 => '/home/pblanco/Desarrollo/cogumelo_gcode/branches/1.0a/c_modules/testmodule/templates/test.tpl',
      1 => 1365166096,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '447341910515ec337cbdcd7-42239895',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_515ec337d85f01_18782111',
  'variables' => 
  array (
    'css_array' => 0,
    'foo' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_515ec337d85f01_18782111')) {function content_515ec337d85f01_18782111($_smarty_tpl) {?>
<?php  $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['foo']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['css_array']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['foo']->key => $_smarty_tpl->tpl_vars['foo']->value){
$_smarty_tpl->tpl_vars['foo']->_loop = true;
?>
    <div><?php echo $_smarty_tpl->tpl_vars['foo']->value;?>
</div>
<?php } ?>

Ola mundo modulo test<?php }} ?>