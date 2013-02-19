<?php
/*
 Plugin Name: flexslider-top-gallery
 Plugin URI: http://lovelog.eternal-tears.com/
 Description: トップのメイン画像を投稿した記事画像の中からランダムで表示してくれるプラグインです。
 Version: 1.0
 Author: Eternal-tears
 Author URI: http://lovelog.eternal-tears.com/

 jQuery's plugin by jQuery FlexSlider v2.0(http://www.woothemes.com/flexslider/)
 */
load_plugin_textdomain( 'flexslidertopgallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/* ==================================================
■ギャラリー画像専用の画像サイズ設定
 ================================================== */
add_image_size( 'top-thumbnail', $flexsilder_top_width, $flexsilder_top_height); //大画像設定

/* ==================================================
■サムネイル画像とメイン画像の表示ソース
 ================================================== */
function flexslider_gallery_top() {
	global $post;

	$flexslidertopgalleryoutput = '';

	$images = get_posts(array(
		'post_parent' => $post->ID,
		'include' => $includepost,
		'exclude' => $excludepost,
		'category' => $categoryid,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'orderby' => 'rand',
		'order' => 'ASC',
		'posts_per_page' => $viewcount
	));

	foreach ($images as $image) {
		$top_attributes = wp_get_attachment_image_src($image->ID,'top-thumbnail');

//echo '<pre>';
//var_dump($top_attributes);
//echo '</pre>';

		//サムネイル画像の表示部分
		$flexslidertopgalleryoutput .= '<li>';
		$flexslidertopgalleryoutput .= '<img';
		$flexslidertopgalleryoutput .= ' src="' . esc_attr($top_attributes[0]) . '"';
		$flexslidertopgalleryoutput .= ' width="' . esc_attr($top_attributes[1]) . '"';
		$flexslidertopgalleryoutput .= ' height="' . esc_attr($top_attributes[2]) . '"';
		$flexslidertopgalleryoutput .= ' /></li>' . "\n";

	}

	if (!empty($flexslidertopgalleryoutput)) {
		$flexslidertopgalleryoutput = '<ul class="slides">' . "\n"
			. $flexslidertopgalleryoutput
			. '</ul>' . "\n";
	}
		echo $flexslidertopgalleryoutput;
}

/* ==================================================
■表示したい部分に入れるソース
 ================================================== */

function flexslider_top_gallery(){
	global $post;
	echo '<div class="itemimg">' . "\n";

		echo '<div class="flexslider">' . "\n";
		flexslider_gallery_top($post->ID);
		echo '</div>' . "\n";

	echo '</div>' . "\n";
}

/* ==================================================
■ヘッダーにソース
 ================================================== */
function add_flexslider_top_js() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery.flexslider-min',plugins_url('js/jquery.flexslider-min.js', __FILE__),array('jquery'),false,false);

	wp_enqueue_script('flexslider-basicslider',plugins_url('js/flexslider-basicslider.js', __FILE__),array('jquery'),false,false);
}
function add_flexslider_top_css(){
	wp_register_style( 'flexslider', plugins_url('css/flexslider.css', __FILE__),'screen' );
	wp_enqueue_style('flexslider');
}
add_action('wp_enqueue_scripts','add_flexslider_top_js');
add_action('wp_enqueue_scripts','add_flexslider_top_css');

//プラグイン設定画面get_option('flexslidertopgallery_slider')

// 「プラグイン」メニューのサブメニュー
function flexslidertopgallery_add_admin_menu() {
	add_submenu_page('options-general.php', 'FlexSlider Top Galleryの設定', 'FlexSlider Top Gallery', manage_options, __FILE__, 'flexslidertopgallery_admin_page');
}
add_action('admin_menu', 'flexslidertopgallery_add_admin_menu');

// 設定画面の表示
function flexslidertopgallery_admin_page() {
	$includepost = get_option('flexslidertopgallery_includepost',null);
	$excludepost = get_option('flexslidertopgallery_excludepost',null);
	$categoryid = get_option('flexslidertopgallery_categoryid',null);
	$viewcount = get_option('flexslidertopgallery_viewcount',5);
	$flexsilder_top_width = get_option('flexslidertopgallery_MainImgWidth',700);
	$flexsilder_top_height = get_option('flexslidertopgallery_MainImgHeight',200);

	// 「変更を保存」ボタンがクリックされたときは、設定を保存する
	if ($_POST['posted'] == 'Y') {

		$includepost = stripslashes($_POST['includepost']);
		$excludepost = stripslashes($_POST['excludepost']);
		$categoryid = stripslashes($_POST['categoryid']);
		$viewcount = stripslashes($_POST['viewcount']);

		$flexsilder_top_width = stripslashes($_POST['MainImgWidth']);
		$flexsilder_top_height = stripslashes($_POST['MainImgHeight']);

		update_option('flexslidertopgallery_includepost', $includepost);
		update_option('flexslidertopgallery_excludepost', $excludepost);
		update_option('flexslidertopgallery_categoryid', $categoryid);
		update_option('flexslidertopgallery_viewcount', $viewcount);
		update_option('flexslidertopgallery_MainImgWidth', $flexsilder_top_width);
		update_option('flexslidertopgallery_MainImgHeight', $flexsilder_top_height);
	}
?>

<?php if($_POST['posted'] == 'Y') : ?>
	<div class="updated"><p><strong><?php echo __( '設定を保存しました', 'flexslidertopgallery' ); ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
	<h2><?php echo __( 'flexslidertopgalleryの設定', 'flexslidertopgallery' ); ?></h2>
	<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="posted" value="Y">
		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="MainImgSize"><?php echo __( 'ギャラリーのメイン画像サイズ', 'flexslidertopgallery' ); ?><label></th>
				<td>
					Width:<input name="MainImgWidth" type="number" id="MainImgWidth" value="<?php echo esc_attr($flexsilder_top_width); ?>" class="small-text" />,height:<input name="MainImgHeight" type="number" id="MainImgHeight" value="<?php echo esc_attr($flexsilder_top_height); ?>" class="small-text" /><br />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="includepost"><?php echo __( 'インクルードする記事ID、ページID', 'flexslidertopgallery' ); ?><label></th>
				<td>
					<input name="includepost" type="text" id="includepost" value="<?php echo esc_attr($includepost); ?>" class="regular-text code" /><br />
					<?php echo __( '複数ある場合はコンマ（,）区切り', 'flexslidergallery' ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="excludepost"><?php echo __( '除外する記事ID、ページID', 'flexslidertopgallery' ); ?><label></th>
				<td>
					<input name="excludepost" type="text" id="excludepost" value="<?php echo esc_attr($excludepost); ?>" class="regular-text code" /><br />
					<?php echo __( '複数ある場合はコンマ（,）区切り', 'flexslidergallery' ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="categoryid"><?php echo __( 'カテゴリー指定', 'flexslidertopgallery' ); ?><label></th>
				<td>
					<input name="categoryid" type="text" id="categoryid" value="<?php echo esc_attr($categoryid); ?>" class="regular-text code" /><br />
					<?php echo __( '複数ある場合はコンマ（,）区切り', 'flexslidergallery' ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><legend for="viewcount"><?php echo __( '切り替える画像数', 'flexslidertopgallery' ); ?></legend></th>
				<td>
					<select name="viewcount" id="viewcount">
					<?php
						$options = array(
							array('value' => '1', 'text' => '1件'),
							array('value' => '2', 'text' => '2件'),
							array('value' => '3', 'text' => '3件'),
							array('value' => '4', 'text' => '4件'),
							array('value' => '5', 'text' => '5件'),
							array('value' => '6', 'text' => '6件'),
							array('value' => '7', 'text' => '7件'),
							array('value' => '8', 'text' => '8件'),
							array('value' => '9', 'text' => '9件'),
							array('value' => '10', 'text' => '10件'),
						);
						foreach ($options as $option) : ?>
						<option value="<?php echo esc_attr($option['value']); ?>"<?php if ($viewcount == $option['value']) : ?> selected="selected"<?php endif; ?>><?php echo esc_attr($option['text']); ?></option>
					<?php endforeach; ?>
					</select><br />
					<?php echo __( '切り替える画像表示数を指定します。', 'flexslidergallery' ); ?>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php echo __( '変更を保存', 'flexslidertopgallery' ); ?>" />
		</p>
	</form>
	<h2><?php echo __( 'プラグインの使い方', 'flexslidertopgallery' ); ?></h2>
	<p><?php echo __( 'テーマ内の表示したい箇所に下記のソースを表示してください。', 'flexslidertopgallery' ); ?><br>
	<?php echo __( '&lt;?php flexslider_top_gallery(); ?&gt;', 'flexslidertopgallery' ); ?>
	</p>

</div>
<?php
}
?>