<?php
/*
Plugin Name: TinyMCE Media Plugin
Plugin URI: http://shamiao.com/tinymce-media-plugin
Description: Install & activate legacy "media" function of TinyMCE, make flash & other multimedia elements inserted in HTML mode show again in the visual editor, rather than disappear directly as WordPress 3.1+ does originally. 恢复可视化编辑器中被删除的，原生的“媒体插入”功能。这样使得HTML模式中存在的Flash等多媒体元素，在可视化编辑器中可以直观显示出来，并且能方便的增删、移动、调整大小甚至更改媒体URL，而不至由于可视化编辑器缺少功能，而直接消失不见。BUG修正插件，WordPress 3.1+ 适用。
Version: 1.1
Author: shamiao
Author URI: http://shamiao.com
License: GPLv2
*/

// Hook on initialize, run in Rich Editing Mode
function tinymce_media_plugin() {
	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;

	// Add only in Rich Editor mode
	if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "tinymce_media_plugin_register");
		add_filter('mce_buttons', 'tinymce_media_plugin_register_button');
	}
}

// Attach Media Plugin to TinyMCE
function tinymce_media_plugin_register($plugin_array) {
	$tinymce_media_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$plugin_array['media'] = $tinymce_media_plugin_url.'media/editor_plugin.js';
	return $plugin_array;
}

// Add Media Button to TinyMCE
function tinymce_media_plugin_register_button($buttons) {
   array_splice($buttons, count($buttons)-1, 0, "media");
   return $buttons;
}

add_action('init', 'tinymce_media_plugin'); // Ready to go 

// Add Media Button to Upload Area
function wp_tinymce_mediabutton($context) {
	$tinymce_media_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$flashbutton_html = <<<EOF
<a href="javascript:wp_upload_mediabtn_click();" id="addupload_media_btn" title="添加Flash"><img src="{$tinymce_media_plugin_url}flash-upload-button.gif" alt="添加Flash"></a><script type="text/javascript">
function wp_upload_mediabtn_click(){document.getElementById("content").focus();tinyMCE.activeEditor.execCommand('mceMedia');}
</script>
EOF;
	$wp_customized_mediabutton = '%s'.$flashbutton_html;
	return sprintf($context, $wp_customized_mediabutton);
}
add_filter('media_buttons_context', 'wp_tinymce_mediabutton');
