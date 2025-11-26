<?php
function browserCategory(){
	// used to determine document viewer availability
	// used to determine audio player characteristics
	if($user_agent = $_SERVER['HTTP_USER_AGENT']){
		if (strpos($user_agent, 'Chrome')) {
			return 'chromium'; // doc viewer, round/light audio player
		}
		if (strpos($user_agent, 'Firefox')) {
			return 'firefox'; // doc viewer, square/dark audio player
		};
	}	
	return 'other';
}

function filesOutputFigures($images = array(), $audio = array(), $video = array(), $other = array(), $galleryType = 'gallery-inline-captions', $html = null)
{
	if(!count($images) && !count($audio) && !count($video) && !count($other)) return null;
	$html .= '<div id="pswp-container" class="curatescape-image-gallery '.$galleryType.'">';
		$html .= filesOutputImages($images);
		$html .= filesOutputAudio($audio);
		$html .= filesOutputVideo($video);
		$html .= filesOutputDocument($other);
	$html .= '</div>';
	return $html;
}

function imageLinkMarkup($file, $size='fullsize', $linkClass='gallery-image', $imgClass='item-file', $itemprop='associatedMedia', $html = null)
{
	if(!$file) return null;
	$dimensions = dimensions($file, $size);
	$fileHref = !option('link_to_file_metadata') ? record_image_url($file, $size) : $file->getProperty('permalink');
	$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' '.$dimensions['orientation'].' file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.record_image_url($file, $size).'" data-pswp-type="image" data-pswp-fallbackmessage="'.__('Download').'">';
		$html .= '<img loading="lazy" class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
	$html .= '</a>';
	return $html;
}

function mediaLinkMarkup($file, $filetype, $linkClass='gallery-image', $imgClass='fallback', $itemprop='associatedMedia', $html = null)
{
	if(!$file || !$filetype) return null;
	$dimensions = array(
		'height' => round(flexOption('fullsize_constraint', 200) * 9/16),
		'width' => flexOption('fullsize_constraint', 200),
	); // 16:9 placeholder/fallback dimensions (see JS)
	$fileHref = !option('link_to_file_metadata') ? $file->getProperty('uri') : $file->getProperty('permalink');
	if(boolval(option('curatescapegalleries_gallery_style') === 'gallery-inline-captions')){
		$html .= '<div'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="curatescape-inline-media file-'.$file->id.'">';
			if($filetype == 'audio'){
				$html .= '<audio class="curatescape-inline-audio" data-browser="'.browserCategory().'" controls src="'.$file->getProperty('uri').'"></audio>';
			}elseif($filetype == 'video'){
				$html .= '<video class="curatescape-inline-video" data-browser="'.browserCategory().'" controls src="'.$file->getProperty('uri').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'"></video>';
			}
		$html .= '</div>';
	}else{
		$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' square file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.$file->getProperty('uri').'" data-pswp-type="'.$filetype.'" data-pswp-fallbackmessage="'.__('Download').'">';
			$html .= '<img loading="lazy" class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
		$html .= '</a>';
	}
	
	return $html;
}

function documentLinkMarkup($file, $linkClass='gallery-image', $imgClass='document', $itemprop='associatedMedia', $html = null)
{
	if(!$file ) return null;
	$dimensions = array(
		'height' => flexOption('fullsize_constraint', 200),
		'width' => flexOption('fullsize_constraint', 200),
	); // 16:9 placeholder/fallback dimensions (see JS)
	$fileHref = !option('link_to_file_metadata') ? $file->getProperty('uri') : $file->getProperty('permalink');
	if(boolval(option('curatescapegalleries_gallery_style') === 'gallery-inline-captions') && option('curatescapegalleries_lightbox_docs') === '0'){
		$html .= '<div'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="curatescape-inline-document file-'.$file->id.'">';
			if(browserCategory() == 'chromium'){
				$html .= '<iframe width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" src="'.$file->getWebPath('original').'"></iframe>';
			}elseif(browserCategory() == 'firefox'){
				$html .= '<object width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" data="'.$file->getWebPath('original').'"></object>';
			}else{
				$html .= '<a width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" download href="'.$file->getWebPath('original').'"><img src="'.$file->getWebPath('fullsize').'"/></a>';
			}
		$html .= '</div>';
	}else{
		$html .= '<a'.($itemprop ? ' itemprop='.$itemprop : null).' href="'.$fileHref.'" class="pswp-item '.$linkClass.' square file-'.$file->id.'" data-pswp-width="'.$dimensions['width'].'" data-pswp-height="'.$dimensions['height'].'" data-pswp-src="'.$file->getProperty('uri').'" data-pswp-type="document" data-pswp-fallbackmessage="'.__('Download').'">';
			$html .= '<img loading="lazy" class="'.$imgClass.'" src="'.record_image_url($file, 'fullsize').'" width="'.$dimensions['width'].'" height="'.$dimensions['height'].'" alt="'.htmlentities($file->getProperty('display_title')).'" />';
		$html .= '</a>';
	}
	return $html;
}

function filesOutputImages($files, $schemaURI = 'https://schema.org/ImageObject', $html = null)
{
	if(!$files) return null;
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Image').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= imageLinkMarkup($file, 'fullsize');
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	return $html;
}

function filesOutputAudio($files, $schemaURI = 'https://schema.org/AudioObject', $html = null)
{
	if(!$files) return null;
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Audio').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= mediaLinkMarkup($file, 'audio');
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	return $html;
}

function filesOutputDocument($files, $schemaURI = 'https://schema.org/DigitalDocument', $html = null)
{
	if(!$files) return null;
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Document').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= documentLinkMarkup($file);
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	return $html;
}

function filesOutputVideo($files, $schemaURI = 'https://schema.org/VideoObject', $html = null)
{
	if(!$files) return null;
	foreach($files as $file){
		$html .= '<figure aria-label="'.__('Video').': '.htmlentities($file->getProperty('display_title')).'"class="curatescape-image-figure" '.($schemaURI ? 'itemtype="'.$schemaURI.'"' : null).'>';
			$html .= mediaLinkMarkup($file, 'video');
			$html .= '<figcaption>'.mediaCaptionText($file).'</figcaption>';
		$html .= '</figure>';
	}
	return $html;
}

function filesOutputTable($files, $subhead = true, $html = null)
{
	if(!$files) return null;
	$html .= '<div class="filestablecontainer"><table class="curatescape-additional-files">';
		$html .= ($subhead ? '<caption><h3>'.__('Documents').'</h3></caption>' : null);
		$html .= '<thead><th>'.__('File Details').'</th><th>'.__('Download').'</th></thead>';
		$html .= '<tbody>';
		foreach($files as $file){
			$meta = array(oxfordAmp(dc($file,'Creator',array('all'=>'true'))),dc($file, 'Source'),dc($file, 'Date'));
			$info = implode(' | ', array_filter($meta));
			$title = mediaCaptionText($file);
			$type = '<span class="type">'.fileSubTypeString($file).' / '.formatSize($file->size).'</span>';
			$download = '<span class="file-download"><a class="button" download href="'.record_image_url($file, 'original').'">'.__('Download').'</a>'.$type.'</span>';
			$html .= '<tr><td>'.$title.'</td><td>'.$download.'</td></tr>';
		}
		$html .= '</tbody>';
	$html .= '</table></div>';
	return $html;
}

function formatSize($bytes){
	if(!$bytes) return '0 Kb';
	if($bytes < 1048576){
		return number_format($bytes / 1024).' KB';
	}
	if($bytes < 1073741824){
		return number_format($bytes / 1048576).' MB';
	}
	if ($size < 1099511627776){
		$bytes < number_format($bytes / 1073741824).' GB';
	}
	return '1 TB+';
}

function mediaCaptionText($file, $caption = array())
{
	if(!$file) return null;
	$title = dc($file, 'Title') ? dc($file, 'Title') : __('File #%s: [Untitled]', $file->getProperty('id'));
	$captionTitle = '<span class="file-title" itemprop="name"><h3><a title="'.__('View File Record').'" href="'.$file->getProperty('permalink').'">'.strip_tags($title).'</a></h3></span>';
	if($description = dc($file, 'Description')) {
		$caption['description'] = '<span class="file-description">'.strip_tags($description,'<a><cite><em><i><strong><b>').'</span>';
	}
	if($source = dc($file, 'Source')) {
		$caption['source'] = '<span class="file-source"><span>'.__('Source').'</span>: '.strip_tags($source, '<a><cite><em><i><strong><b>').'</span>';
	}
	if($date = dc($file, 'Date')) {
		$caption['date'] = '<span class="file-date"><span>'.__('Date').'</span>: '.strip_tags($date).'</span>';
	}
	return $captionTitle.implode(' | ', $caption);
}

function localImageFilePath($file, $size = 'fullsize')
{
	if(!$file) return null;
	$pathInfo = pathinfo($file->filename);
	$filename = $pathInfo['filename'] . ($size !== 'original' ? '.jpg' : '.'.$pathInfo['extension']);
	return FILES_DIR.DIRECTORY_SEPARATOR.$size.DIRECTORY_SEPARATOR.$filename;
}

function dimensions($file, $size = 'fullsize')
{
	if(!$file) return null;
	$info = array(
		'height'=>'',
		'width'=>'',
		'orientation'=>'',
	);
	$imgsize = getimagesize(localImageFilePath($file, $size));
	if(!$imgsize || !isset($size[1])) return $info;
	$info['width'] = $imgsize[0];
	$info['height'] = $imgsize[1];
	$info['orientation'] = $imgsize[0] > $imgsize[1] ? 'landscape' : 'portrait';
	return $info;
}

function getThemeClass($default='unknown'){
	if($themeDir = Theme::getCurrentThemeName()){
		return strtolower(str_ireplace(' ', '-', $themeDir));
	}
	return $default;
}