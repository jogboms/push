<?php
/**
 * PUSH MVC Framework.
 * @package PUSH MVC Framework
 * @version See PUSH.json
 * @author See PUSH.json
 * @copyright See PUSH.json
 * @license See PUSH.json
 * PUSH MVC Framework is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See PUSH.json for copyright notices and details.
 */

namespace Push\Extensions;

Class Mimes 
{
	
	static public $Mime = array(
		"avi" => "video/avi",
		"bmp" => "image/avi",
		"bz2" => "application/x-bzip2",
		"avi" => "video/avi",
		'3gp' => 'video/3gpp',
		'7z' => 'application/x-7z-compressed',
		'aac' => 'audio/x-aac',
		'aiff' => 'audio/x-aiff',
		'apk' => 'application/vnd.android.package-archive',
		'avi' => 'video/x-msvideo',
		'bz' => 'application/x-bzip',
		'bz2' => 'application/x-bzip2',
		'c' => 'text/x-c',
		'cab' => 'application/vnd.ms-cab-compressed',
		'chm' => 'application/vnd.ms-htmlhelp',
		'conf' => 'text/plain',
		'css' => 'text/css',
		'csv' => 'text/csv',
		'doc' => 'application/msword',
		'docm' => 'application/vnd.ms-word.document.macroenabled.12',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dot' => 'application/msword',
		'dotm' => 'application/vnd.ms-word.template.macroenabled.12',
		'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'epub' => 'application/epub+zip',
		'exe' => 'application/x-exe',
		'f' => 'text/x-fortran',
		'flv' => 'video/x-flv',
		'gif' => 'image/gif',
		'gtar' => 'application/x-gtar',
		'gz' => 'application/x-gzip',
		'htm' => 'text/html',
		'html' => 'text/html',
		'ico' => 'image/x-icon',
		'jad' => 'text/vnd.sun.j2me.app-descriptor',
		'jar' => 'application/java-archive',
		'java' => 'text/x-java-source',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'js' => 'application/x-javascript',
		'json' => 'application/json',
		'jsonml' => 'application/jsonml+json',
		'mid' => 'audio/x-midi',
		'midi' => 'audio/midi',
		'mk3d' => 'video/x-matroska',
		'mka' => 'audio/x-matroska',
		'mks' => 'video/x-matroska',
		'mkv' => 'video/x-matroska',
		'mov' => 'video/quicktime',
		'movie' => 'video/x-sgi-movie',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4',
		'mp4a' => 'audio/mp4',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpg4' => 'video/mp4',
		'mpga' => 'audio/mpeg',
		'n-gage' => 'application/vnd.nokia.n-gage.symbian.install',
		'oga' => 'audio/ogg',
		'ogg' => 'audio/ogg',
		'ogv' => 'video/ogg',
		'p' => 'text/x-pascal',
		'pdb' => 'application/vnd.palm',
		'pdf' => 'application/pdf',
		'php' => 'text/x-php',
		'pl' => 'application/x-perl',
		'png' => 'image/png',
		'pot' => 'application/vnd.ms-powerpoint',
		'potm' => 'application/vnd.ms-powerpoint.template.macroenabled.12',
		'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
		'ppam' => 'application/vnd.ms-powerpoint.addin.macroenabled.12',
		'ppd' => 'application/vnd.cups-ppd',
		'pps' => 'application/vnd.ms-powerpoint',
		'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroenabled.12',
		'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptm' => 'application/vnd.ms-powerpoint.presentation.macroenabled.12',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'psd' => 'image/x-psd',
		'py' => 'text/x-python',
		'rar' => 'application/x-rar-compressed',
		'sis' => 'application/vnd.symbian.install',
		'sisx' => 'application/vnd.symbian.install',
		'sql' => 'text/x-sql',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'swf' => 'application/x-shockwave-flash',
		'tar.gz' => 'application/x-tar',
		'txt' => 'text/plain',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'tgz' => 'application/x-tar',
		'tmo' => 'application/vnd.tmobile-livetv',
		'torrent' => 'application/x-bittorrent',
		'tpl' => 'application/vnd.groove-tool-template',
		'wav' => 'audio/x-wav',
		'weba' => 'audio/webm',
		'webm' => 'video/webm',
		'webp' => 'image/webp',
		'wml' => 'text/vnd.wap.wml',
		'woff' => 'application/x-font-woff',
		'xhtml' => 'application/xhtml+xml',
		'xml' => 'application/xml',
		'zip' => 'application/zip',
	);


	static function getMime($filename)
	{
		$ext = array_reverse(explode('.',$filename));
		if(function_exists('finfo_open')){
			$info = finfo_open(FILEINFO_MIME);
			$type = finfo_file($info, $filename);
			return $type;
		} 
		elseif(function_exists('mime_content_type')) {} 
		else 
			return (array_key_exists($ext[0], self::$Mime)) ? self::$Mime[$ext[0]] : 'application/octet-stream';

	}
}