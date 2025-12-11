<?php
include 'functions.php';
class CuratescapeGalleriesPlugin extends Omeka_Plugin_AbstractPlugin{
	protected $_hooks = array(
		'admin_head',
		'config_form',
		'config',
		'initialize',
		'install',
		'public_head',
		'uninstall',
	);
	protected $_filters = array(
		'body_tag_attributes',
		'files_for_item',
		'file_markup',
	);
	protected $_options = array(
		'curatescapegalleries_file_markup'=> 1,
		'curatescapegalleries_gallery_style'=> 'gallery-grid',
		'curatescapegalleries_lightbox_docs' => 0,
		'curatescapegalleries_lightbox' => 1,
		'curatescapegalleries_theme_fixes' => 1,
	);
	public function hookAdminHead()
	{
		if(
			is_current_url('/admin/plugins/config?name=CuratescapeGalleries') ||
			is_current_url('/admin/plugins/config/name/CuratescapeGalleries')
		){
			queue_css_file('galleries-config', 'all', false, 'css', get_plugin_ini('CuratescapeGalleries', 'version'));
		}
	}
	public function hookInitialize()
	{
		add_translation_source(dirname(__FILE__).'/languages');
	}
	public function hookInstall()
	{
		return $this->_installOptions();
	}
	public function hookUninstall()
	{
		return $this->_uninstallOptions();
	}
	public function hookConfig()
	{
		set_option('curatescapegalleries_file_markup', $_POST['curatescapegalleries_file_markup']);
		set_option('curatescapegalleries_gallery_style', $_POST['curatescapegalleries_gallery_style']);
		set_option('curatescapegalleries_lightbox_docs', $_POST['curatescapegalleries_lightbox_docs']);
		set_option('curatescapegalleries_lightbox', $_POST['curatescapegalleries_lightbox']);
		set_option('curatescapegalleries_theme_fixes', $_POST['curatescapegalleries_theme_fixes']);
	}
	public function hookConfigForm()
	{
		return get_view()->GalleriesConfigForm();
	}
	public function hookPublicHead($args)
	{
		return get_view()->GalleriesPublicHead($args);
	}
	public function filterBodyTagAttributes($attributes){
		if(
			is_admin_theme() || 
			!is_current_url('/items/show') ||
			!option('curatescapegalleries_theme_fixes')
		) return $attributes;
		$attributes['class'] = $attributes['class'].' curatescapegalleries-fix-'.getThemeClass();
		return $attributes;
	}
	public function filterFilesForItem($html, $args)
	{
		return get_view()->GalleriesFilesForItem($html, $args);
	}
	
	public function filterFileMarkup($html, $args)
	{
		return get_view()->GalleriesFileMarkup($html, $args);
	}
}