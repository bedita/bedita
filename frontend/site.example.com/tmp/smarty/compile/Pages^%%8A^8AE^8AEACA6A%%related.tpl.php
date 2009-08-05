<?php /* Smarty version 2.6.18, created on 2009-08-05 10:44:19
         compiled from /home/ste/workspace/bedita/frontend/site.example.com/views/elements/related.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/related.tpl', 4, false),array('function', 'assign_associative', '/home/ste/workspace/bedita/frontend/site.example.com/views/elements/related.tpl', 6, false),)), $this); ?>
<?php if (! empty ( $this->_tpl_vars['section']['currentContent']['relations'] )): ?>
<h3>&nbsp;</h3>

			<?php $this->assign('attach', ((is_array($_tmp=@$this->_tpl_vars['section']['currentContent']['relations']['attach'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, ''))); ?>
			<?php if (! empty ( $this->_tpl_vars['attach'] )): ?>
			<?php echo smarty_function_assign_associative(array('var' => 'paramsBig','width' => 680,'mode' => 'fill','upscale' => false,'URLonly' => true), $this);?>

			<?php echo smarty_function_assign_associative(array('var' => 'params','width' => 220,'mode' => 'fill','upscale' => false), $this);?>

			<?php echo smarty_function_assign_associative(array('var' => 'paramsVideo','presentation' => 'full'), $this);?>

			<?php echo smarty_function_assign_associative(array('var' => 'paramsHtml','height' => 165,'width' => 220), $this);?>


			<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['attach']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
			<div class="related">
				<?php if ($this->_tpl_vars['attach'][$this->_sections['i']['index']]['object_type_id'] == $this->_tpl_vars['conf']->objectTypes['video']['id']): ?>
					<?php echo $this->_tpl_vars['beEmbedMedia']->object($this->_tpl_vars['attach'][$this->_sections['i']['index']],$this->_tpl_vars['paramsVideo'],$this->_tpl_vars['paramsHtml']); ?>

				<?php else: ?>
					<a class="thickbox" href="<?php echo $this->_tpl_vars['beEmbedMedia']->object($this->_tpl_vars['attach'][$this->_sections['i']['index']],$this->_tpl_vars['paramsBig']); ?>
" 
					title="<?php echo $this->_tpl_vars['attach'][$this->_sections['i']['index']]['description']; ?>
" rel="gallery">
					<?php echo $this->_tpl_vars['beEmbedMedia']->object($this->_tpl_vars['attach'][$this->_sections['i']['index']],$this->_tpl_vars['params']); ?>

					</a>
				<?php endif; ?>
					<p class="dida"><?php echo $this->_tpl_vars['attach'][$this->_sections['i']['index']]['description']; ?>
</p>
			</div>
			<?php endfor; endif; ?>
			<?php endif; ?>
			
			<?php $this->assign('seealso', ((is_array($_tmp=@$this->_tpl_vars['section']['currentContent']['relations']['seealso'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, ''))); ?>
			<?php if (! empty ( $this->_tpl_vars['seealso'] )): ?>
			<div class="related">
			<h2>See also:</h2>
			<ul>
			<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['seealso']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
				<li>
					<a title="<?php echo $this->_tpl_vars['seealso'][$this->_sections['i']['index']]['title']; ?>
" href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
<?php echo $this->_tpl_vars['seealso'][$this->_sections['i']['index']]['nickname']; ?>
">
						<?php echo $this->_tpl_vars['seealso'][$this->_sections['i']['index']]['title']; ?>

					</a>
				</li>
			<?php endfor; endif; ?>
			</ul>
			</div>
			<?php endif; ?>
			
			<?php $this->assign('links', ((is_array($_tmp=@$this->_tpl_vars['section']['currentContent']['relations']['link'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, ''))); ?>
			<?php if (! empty ( $this->_tpl_vars['links'] )): ?>
			<div class="related">
			<h2>Links:</h2>
			<ul>
			<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['links']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
				<li>
					<a title="<?php echo $this->_tpl_vars['links'][$this->_sections['i']['index']]['title']; ?>
" href="<?php echo $this->_tpl_vars['links'][$this->_sections['i']['index']]['url']; ?>
" target="<?php echo ((is_array($_tmp=@$this->_tpl_vars['links'][$this->_sections['i']['index']]['target'])) ? $this->_run_mod_handler('default', true, $_tmp, '_blank') : smarty_modifier_default($_tmp, '_blank')); ?>
">
						<?php echo $this->_tpl_vars['links'][$this->_sections['i']['index']]['title']; ?>

					</a>
				</li>
			<?php endfor; endif; ?>
			</ul>
			</div>
			<?php endif; ?>
			
			<?php $this->assign('downloads', ((is_array($_tmp=@$this->_tpl_vars['section']['currentContent']['relations']['download'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, ''))); ?>
			<?php if (! empty ( $this->_tpl_vars['download'] )): ?>
			<div class="related">
			<h2>Download:</h2>
			<ul>
			<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['downloads']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
				<li>
					<a title="<?php echo $this->_tpl_vars['downloads'][$this->_sections['i']['index']]['title']; ?>
" href="<?php echo $this->_tpl_vars['html']->url('/'); ?>
download/<?php echo $this->_tpl_vars['downloads'][$this->_sections['i']['index']]['nickname']; ?>
">
						<?php echo ((is_array($_tmp=@$this->_tpl_vars['downloads'][$this->_sections['i']['index']]['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['downloads'][$this->_sections['i']['index']]['nickname']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['downloads'][$this->_sections['i']['index']]['nickname'])); ?>

					</a>
				</li>
			<?php endfor; endif; ?>
			</ul>
			</div>
			<?php endif; ?>
			
<?php endif; ?>