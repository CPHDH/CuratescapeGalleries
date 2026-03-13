<?php
class CuratescapeGalleries_View_Helper_GalleriesConfigForm extends Zend_View_Helper_Abstract{
	public function GalleriesConfigForm(){
		?>
		<p class="intro"><?php echo __('To learn about Curatescape mobile apps for iOS and Android, visit %1s. For support, create an account at %2s.', '<a href="https://curatescape.org" target="_blank">curatescape.org</a>', '<a href="https://forum.curatescape.org" target="_blank">forum.curatescape.org</a>');?></p>
		<fieldset>
			<legend><?php echo __('File Display Settings'); ?></legend>
			<p><?php echo __('Use the following options to customize the display of files on Item and File records.');?></p>
			<!-- Media Style-->
			<?php echo $this->configFormSelect('curatescapegalleries_gallery_style', 'Media Gallery', 'Select the style to be used for displaying images and other media files. Each style will adapt to the available space as determined by the theme layout and browser dimensions. May not be supported by all themes.', 
				array(
				'gallery-grid' => __('Thumbnail Grid (default)'),
				'gallery-inline-captions' => __('Inline Captions'),
				'gallery-slides' => __('Slides'),
				'gallery-table' => __('Files Table'), 
				)
			);?>
			<!-- Image Lightbox-->
			<?php echo $this->configFormCheckBox('curatescapegalleries_lightbox', 'Image Lightbox', 'If checked, image links will open in lightbox overlay (PhotoSwipe). If unchecked, image links will open to either the file or the file record, based on site settings. Requires use of plugin media styles, selected above.');?>
			<!-- Docs in Lightbox-->
			<?php echo $this->configFormCheckBox('curatescapegalleries_lightbox_docs', 'PDF Lightbox', 'If checked, PDF document files will be presented alongside images and use the lightbox overlay (PhotoSwipe) with select gallery types. Note that the presentation of PDF document files will vary across different browsers and devices. If unchecked, PDF document files will be listed uniformly in a separate table when the Thumbnail Grid gallery type is active.');?>
			<!-- Files Show File Markup-->
			<?php echo $this->configFormCheckBox('curatescapegalleries_file_markup', 'File Record Markup', 'If checked, the HTML for files on each single file record (i.e. files/show) will use Curatescape style markup. Potentially useful for projects with audio, video, and PDF files.');?>
			<!-- Theme Fixes -->
			<?php echo $this->configFormCheckBox('curatescapegalleries_theme_fixes', 'Theme Fixes', __('If checked, use theme-specific CSS styles provided by the Curatescape Galleries plugin. These styles apply to select themes and only affect gallery-related content.'));?>
		</fieldset>
		<?php
	}
	private function configFormCheckBox($optionName, $labelName, $helperText){
		if(!$optionName || !$labelName || !$helperText) return null;
		?>
		<div class="field">
			<div class="two columns alpha">
				<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
			</div>
			<div class="inputs five columns omega">
				<p class="explanation"><?php echo __($helperText); ?></p>
				<?php echo get_view()->formCheckbox($optionName, true,
				array('checked'=>(boolean)get_option($optionName))); ?>
			</div>
		</div>
		<?php
	}
	
	private function configFormSelect($optionName, $labelName, $helperText, $options=array()){
		if(!$optionName || !$labelName || !$helperText || !count($options)) return null;
		?>
		<div class="field">
			<div class="two columns alpha">
				<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
			</div>
			<div class="inputs five columns omega">
				<p class="explanation"><?php echo __($helperText); ?></p>
				<?php echo get_view()->formSelect($optionName, get_option($optionName), null, $options); ?>
			</div>
		</div>
		<?php
	}
	
	private function configFormText($optionName, $labelName, $helperText, $placeholder=null){
		if(!$optionName || !$labelName || !$helperText) return null;
		?>
		<div class="field">
			<div class="two columns alpha">
				<label for="<?php echo $optionName;?>"><?php echo __($labelName); ?></label>
			</div>
			<div class="inputs five columns omega">
				<p class="explanation"><?php echo __($helperText); ?></p>
				<?php echo get_view()->formText($optionName, get_option($optionName), array('placeholder' => __($placeholder))); ?>
			</div>
		</div>
		<?php
	}

}