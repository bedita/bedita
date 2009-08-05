<?php /* Smarty version 2.6.18, created on 2009-08-05 10:53:25
         compiled from /home/ste/workspace/bedita/frontend/../bedita-app/views/pages/sitemap_xml.tpl */ ?>
<?php echo '<?xml'; ?>
 version='1.0' encoding='UTF-8'<?php echo '?>'; ?>

<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9"
	url="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php $_from = $this->_tpl_vars['urlset']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['s_url']):
?>
<url>
<loc><?php echo $this->_tpl_vars['s_url']['loc']; ?>
</loc>
<?php if (! empty ( $this->_tpl_vars['s_url']['lastmod'] )): ?><lastmod><?php echo $this->_tpl_vars['s_url']['lastmod']; ?>
</lastmod><?php endif; ?>
<?php if (! empty ( $this->_tpl_vars['s_url']['changefreq'] )): ?><changefreq><?php echo $this->_tpl_vars['s_url']['changefreq']; ?>
</changefreq><?php endif; ?>
<?php if (! empty ( $this->_tpl_vars['s_url']['priority'] )): ?><priority><?php echo $this->_tpl_vars['s_url']['priority']; ?>
</priority><?php endif; ?>
</url>
<?php endforeach; endif; unset($_from); ?>
</urlset>