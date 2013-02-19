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
add_image_size( 'flexslider_topimg', get_option('flexslidertopgallery_topwidth'), get_option('flexslidertopgallery_topheight'),true); //大画像設定


/* ==================================================
■サムネイル画像とメイン画像の表示ソース
 ================================================== */
function flexslider_gallery_top() {
	$flexslidertopgalleryoutput = '';

global $wp_query;
$wp_query = new WP_Query(array(
	'category' => '12',
	'post_type'=> 'post',
	'posts_per_page'=> get_option('flexslidertopgallery_viewcount')
	));

global $post;
while ( have_posts() ) : the_post();
$fimages = get_children( array(
	'post_parent' => $post->ID,
	'post_type' => 'attachment',
	'post_mime_type' => 'image',
	'orderby' => 'rand',
	'numberposts' => 999
	));

if( count( $fimages )>0 ) {
  $fimage = array_shift( $fimages );
  $image_src = wp_get_attachment_image_src( $fimage->ID, 'flexslider_topimg' );
  echo '<img width="'.$image_src[1].'" heifht="'.$image_src[2].'" src="'.$image_src[0].'">';
echo '<pre>';
var_dump($image_src);
echo '</pre>';
}
endwhile;

	$images = get_posts(array(
		'post_parent' => '549',//記事IDで絞る
		//'category' => '549',
		//'include' => ,
		//'exclude' => null,
		'post_type' => 'attachment',
		'orderby' => 'rand',
		'post_mime_type' => 'image',
		'posts_per_page' => get_option('flexslidertopgallery_viewcount')
	));

	foreach ($images as $image) {
		$top_attributes = wp_get_attachment_image_src($image->ID,'flexslider_topimg');

//echo '<pre>';
//var_dump($images);
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
		flexslider_gallery_top();
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

/* ==================================================
■設定画面の表示
 ================================================== */
function flexslidertopgallery_admin_page() {

	$flexsilder_includepost = get_option('flexslidertopgallery_includepost',null);
	$flexsilder_excludepost = get_option('flexslidertopgallery_excludepost',null);
	$flexsilder_categoryid = get_option('flexslidertopgallery_categoryid',null);
	$flexsilder_viewcount = get_option('flexslidertopgallery_viewcount',5);
	$flexsilder_topwidth = get_option('flexslidertopgallery_topwidth',700);
	$flexsilder_topheight = get_option('flexslidertopgallery_topheight',200);

	// 「変更を保存」ボタンがクリックされたときは、設定を保存する
	if ($_POST['posted'] == 'Y') {

		$flexsilder_includepost = stripslashes($_POST['includepost']);
		$flexsilder_excludepost = stripslashes($_POST['excludepost']);
		$flexsilder_categoryid = stripslashes($_POST['categoryid']);
		$flexsilder_viewcount = stripslashes($_POST['viewcount']);
		$flexsilder_topwidth = stripslashes($_POST['topwidth']);
		$flexsilder_topheight = stripslashes($_POST['topheight']);

		update_option('flexslidertopgallery_includepost', $flexsilder_includepost);
		update_option('flexslidertopgallery_excludepost', $flexsilder_excludepost);
		update_option('flexslidertopgallery_categoryid', $flexsilder_categoryid);
		update_option('flexslidertopgallery_viewcount', $flexsilder_viewcount);
		update_option('flexslidertopgallery_topwidth', $flexsilder_topwidth);
		update_option('flexslidertopgallery_topheight', $flexsilder_topheight);
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
				<th scope="row"><label for="includepost"><?php echo __( 'インクルードする記事ID、ページID', 'flexslidertopgallery' ); ?><label></th>
				<td>
					<input name="includepost" type="text" id="includepost" value="<?php echo esc_attr($flexsilder_includepost); ?>" class="regular-text code" /><br />
					<?php echo __( '複数ある場合はコンマ（,）区切り', 'flexslidergallery' ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="excludepost"><?php echo __( '除外する記事ID、ページID', 'flexslidertopgallery' ); ?><label></th>
				<td>
					<input name="excludepost" type="text" id="excludepost" value="<?php echo esc_attr($flexsilder_excludepost); ?>" class="regular-text code" /><br />
					<?php echo __( '複数ある場合はコンマ（,）区切り', 'flexslidergallery' ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="categoryid"><?php echo __( 'カテゴリー指定', 'flexslidertopgallery' ); ?><label></th>
				<td>
					<input name="categoryid" type="text" id="categoryid" value="<?php echo esc_attr($flexsilder_categoryid); ?>" class="regular-text code" /><br />
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
						<option value="<?php echo esc_attr($option['value']); ?>"<?php if ($flexsilder_viewcount == $option['value']) : ?> selected="selected"<?php endif; ?>><?php echo esc_attr($option['text']); ?></option>
					<?php endforeach; ?>
					</select><br />
					<?php echo __( '切り替える画像表示数を指定します。', 'flexslidergallery' ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php echo __( 'ギャラリーのメイン画像サイズ', 'flexslidertopgallery' ); ?></th>
				<td>
					<label for="topwidth">幅</label>
					<input name="topwidth" type="number" id="topwidth" value="<?php echo esc_attr($flexsilder_topwidth); ?>" class="small-text" />
					<label for="topheight">高さ</label>
					<input name="topheight" type="number" id="topheight" value="<?php echo esc_attr($flexsilder_topheight); ?>" class="small-text" /><br />
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
echo $flexsilder_categoryid;
echo $flexsilder_topheight;
echo '<pre>';
var_dump($flexsilder_topwidth);
echo '</pre>';
}
?>