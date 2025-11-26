<?php
class CuratescapeGalleries_View_Helper_GalleriesPublicHead extends Zend_View_Helper_Abstract{
	public function GalleriesPublicHead($args){
		// CSS items/show
		if(is_current_url('/items/show')){

			queue_css_file('gallery-global', 'all', false, 'css', get_plugin_ini('CuratescapeGalleries', 'version'));

			if(option('curatescapegalleries_gallery_style') == 'gallery-inline-captions'){
				queue_css_file('gallery-inline-captions', 'all', false, 'css', get_plugin_ini('CuratescapeGalleries', 'version'));
			}
			if(option('curatescapegalleries_gallery_style') == 'gallery-grid'){
				queue_css_file('gallery-grid', 'all', false, 'css', get_plugin_ini('CuratescapeGalleries', 'version'));
			}
			if(option('curatescapegalleries_gallery_style') == 'gallery-slides'){
				queue_css_file('gallery-slides', 'all', false, 'css', get_plugin_ini('CuratescapeGalleries', 'version'));
			}
			if(option('curatescapegalleries_theme_fixes')){ 
				queue_css_file('gallery-theme-fixes', 'all', false, 'css', get_plugin_ini('CuratescapeGalleries', 'version'));
			}
		}
		// JS items/show
		if(is_current_url('/items/show')){
			if(
				option('curatescapegalleries_lightbox') && 
				option('curatescapegalleries_gallery_style') !== 'gallery-slides'
			){
				$this->photoSwipeModule();
			}
			if(option('curatescapegalleries_gallery_style') == 'gallery-slides'){
				$this->lightGallerySetup();
			}
		}
	}
	private function lightGallerySetup()
	{
		if(!get_theme_option('lightgallery_caption')){
			set_theme_option('lightgallery_caption', 'none'); // @todo: add title/description option?
		}
		queue_lightgallery_assets();
		queue_js_file('lightgallery', 'javascripts', array('defer'=>'defer'));
	}
	private function photoSwipeModule()
	{
	?>
	<!-- PhotoSwipe (Curatescape Galleries plugin) -->
	<link rel="stylesheet" href="https://unpkg.com/photoswipe@^5.4.4/dist/photoswipe.css">
	<script type="module" src="<?php echo src('photoswipe.js', 'javascripts');?>"></script>
	<?php
	}
}