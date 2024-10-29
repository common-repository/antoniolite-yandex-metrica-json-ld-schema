<?php
/**
 * JSON-LD Schema for Yandex Metrica
 *
 * Plugin Name: JSON-LD Schema for Yandex Metrica
 * Plugin URI:  https://www.antoniolite.com/2019/12/plugin-wordpress-yandex-metrica-jsonld-schema/
 * Description: Insert the needed JSON-LD Schema in your post pages so you can use the content reports in Yandex Metrica.
 * Version:     1.1
 * Author:      Antonio Lite
 * Author URI:  https://www.antoniolite.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.html
 * Text Domain: ymschema
 * Domain Path: /languages
 */

if (get_option('ymschema_disable_yoast_schema') == 1) {
	add_filter('wpseo_json_ld_output', '__return_false');
}

function ymschema_add_plugin_page_settings_link($links) {
	$links[] = '<a href="' .
		admin_url('options-general.php?page=ymschema-settings-page').'">'.__('Settings').'</a>';
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'ymschema_add_plugin_page_settings_link');


function ymschema_settings_page() {
	add_submenu_page(
		'options-general.php',
		'JSON-LD Schema for Yandex Metrica',
		'JSON-LD Schema for Yandex Metrica',
		'manage_options',
		'ymschema-settings-page',
		'ymschema_settings_page_html'
	);
	add_action('admin_init', 'ymschema_settings_init');
}
add_action('admin_menu', 'ymschema_settings_page');

function ymschema_settings_init() {
	register_setting('ymschema-settings-page', 'ymschema_organization_name');
	register_setting('ymschema-settings-page', 'ymschema_organization_url');
	register_setting('ymschema-settings-page', 'ymschema_organization_logo');
	register_setting('ymschema-settings-page', 'ymschema_disable_yoast_schema');
}

function ymschema_settings_page_html() {
	if (!current_user_can('manage_options')) {
		return;
	}
	?>

<div class="wrap">
	<h1>JSON-LD Schema for Yandex Metrica</h1>

	<form method="post" action="options.php">
    <?php settings_fields('ymschema-settings-page'); ?>
    <?php do_settings_sections('ymschema-settings-page'); ?>
    <h3>Organization</h3>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Name</th>
      <td><input type="text" name="ymschema_organization_name" value="<?php echo esc_attr(get_option('ymschema_organization_name')); ?>" class="regular-text"></td>
      </tr>
       
      <tr valign="top">
      <th scope="row">Website</th>
      <td><input type="text" name="ymschema_organization_url" value="<?php echo esc_attr(get_option('ymschema_organization_url')); ?>" class="regular-text"><br><span style="font-weight: normal; font-style: italic; font-size: 0.75em">Full URL</span></td>
      </tr>
      
      <tr valign="top">
      <th scope="row">Logo</th>
      <td><input type="text" name="ymschema_organization_logo" value="<?php echo esc_attr(get_option('ymschema_organization_logo')); ?>" class="regular-text"><br><span style="font-weight: normal; font-style: italic; font-size: 0.75em">(Full URL, size: 600px * 60px)</span></td>
      </tr>
    </table>

    <h3>Additional settings</h3>
    <table class="form-table">
      
      <tr valign="top">
      <td><input type="checkbox" name="ymschema_disable_yoast_schema" value="1"<?php echo get_option('ymschema_disable_yoast_schema') == 1 ? ' checked="checked"' : ''; ?> class="regular-text"> Disable Yoast SEO Schema</td>
      </tr>
    </table>
    
    <?php submit_button(); ?>
	</form>

</div>

    <?php
}

// ***********************************************************************

function yandex_metrica_json_ld_schema(){
	if (get_post_type() === 'post' && !is_front_page()) {
		global $post;
		$pluginList = get_option( 'active_plugins' );
		$plugin = 'wordpress-seo/wp-seo.php'; 
		if (in_array($plugin,$pluginList)) {
		  $seoyoast = 1;
		} else {
			$seoyoast = -1;
		}
		$xpost = get_post($post->ID);
		$userdata = get_userdata($xpost->post_author);
		$usermeta = get_user_meta($xpost->post_author);
		/*
		$category_detail=get_the_category($xpost->ID);
		foreach($category_detail as $cd){
			$tmp_categories[$cd->term_id] = Array($cd->name,json_encode($cd->slug));
		}
		*/
		$post_categories_tmp = wp_get_post_terms($xpost->ID, 'category', array('fields'=>'all', 'orderby'=>'parent'));
		// print_r($term_list);
		// echo '<hr>';
		// unset($tmp_categories);
		foreach($post_categories_tmp as $term) {
			if($yoast === 1 && get_post_meta($xpost->ID, '_yoast_wpseo_primary_category',true) == $term->term_id) {
		  	// $tmp_categories[$term->term_id] = Array($term->name,json_encode($term->slug),$term->parent,'maincategory');
		  	$tmp_categories[$term->term_id] = Array(
		  		'name'=>$term->name,
		  		'slug'=>json_encode($term->slug),
		  		'parent'=>$term->parent,
		  		'main'=>0);
		  } else {
		  	$tmp_categories[$term->term_id] = Array(
		  		'name'=>$term->name,
		  		'slug'=>json_encode($term->slug),
		  		'parent'=>$term->parent,
		  		'main'=>1);
		  }
		}
		uasort($tmp_categories, function($a, $b) {
		  return [$a['main'], $a['parent'], $a['name']]
		  			 <=>
						 [$b['main'], $b['parent'], $b['name']];
		});
		$gotCategory = 0;
		$gotSubcategory = 0;
		foreach ($tmp_categories as $key => $val) {
			if ($gotCategory !== 1) {
				$categoriaID = $key;
				$category = Array($tmp_categories[$key]['name'],$tmp_categories[$key]['slug']);
				$gotCategory = 1;
			}
			if ($gotSubcategory !== 1 && $tmp_categories[$key]['parent'] == $categoriaID) {
				$subcategory = Array($tmp_categories[$key]['name'],$tmp_categories[$key]['slug']);
				$gotSubcategory = 1;
			}
		}
		$tmp_category = $category;
		if (isset($subcategory)) {
			$tmp_subcategory = $subcategory;
		}
		$post_tags = get_the_tags($xpost->ID);
		foreach($post_tags as $pt){
		  $tmp_tags[] = json_encode($pt->name);
		}
		/*
		if ($seoyoast === 1) {
			$term_list = wp_get_post_terms($xpost->ID, 'category', ['fields' => 'all']);
			foreach($term_list as $term) {
			   if(get_post_meta($xpost->ID, '_yoast_wpseo_primary_category',true) == $term->term_id) {
			     $tmp_category = $term->term_id;
			   }
			}
		}
		*/
		$post_thumbnail_id = get_post_thumbnail_id($xpost->ID);
		if ($post_thumbnail_id != "") {
			$featured = wp_get_attachment_url($post_thumbnail_id, 'full');
			if (wp_remote_retrieve_response_code(wp_remote_head($featured)) == 200) {
				list($tmp_image_width,$tmp_image_height) = getimagesize($featured);
				if ($tmp_image_width != "" && $tmp_image_height != "") {
					$tmp_image['url'] = $featured;
					$tmp_image['width'] = $tmp_image_width;
					$tmp_image['height'] = $tmp_image_height;
				}
			}
		}
		if (!isset($tmp_image)) {
			$tmp_image['url'] = 'https://via.placeholder.com/800x600/EEEEEE/222222/?text='.urlencode($xpost->post_title);
			$tmp_image['width'] = 800;
			$tmp_image['height'] = 600;
		}
		if (get_option('ymschema_organization_name') != "" && get_option('ymschema_organization_url') != "" && get_option('ymschema_organization_logo') != "") {
			$tmp_publisher['name'] = get_option('ymschema_organization_name');
			$tmp_publisher['url'] = get_option('ymschema_organization_url');
			$tmp_publisher['logo'] = get_option('ymschema_organization_logo');
		} else {
			$tmp_publisher['name'] = get_bloginfo('name');
			$tmp_publisher['url'] = site_url();
			$tmp_publisher['logo'] = 'https://via.placeholder.com/600x60/000000/FFFFFF/?text='.urlencode(get_bloginfo('name'));
		}
		
		$ymschema['id'] = post_permalink($xpost->ID);
		$ymschema['headline'] = $xpost->post_title;
		$ymschema['text'] = substr(preg_replace("/\r|\n/", "", strip_tags($xpost->post_content)),0,150);
		$ymschema['description'] = substr(preg_replace("/\r|\n/", "", strip_tags($xpost->post_content)),0,300);
		$ymschema['datePublished'] = $xpost->post_date;
		$ymschema['dateModified'] = $xpost->post_modified;
		if (isset($tmp_tags) && count($tmp_tags) >= 1) {
			$ymschema['about'] = implode(',', $tmp_tags);
		}
		$ymschema['author']['url'] = get_author_posts_url($xpost->post_author);
		$ymschema['author']['name'] = $userdata->data->display_name;
		$ymschema['author']['image'] = md5($userdata->data->user_email);
		if (isset($tmp_image)) {
			$ymschema['image'] = $tmp_image;
		}
		if (isset($tmp_publisher)) {
			$ymschema['publisher'] = $tmp_publisher;
		}
		$ymschema['itemListElement']['category']['name'] = $tmp_category[0];
		$ymschema['itemListElement']['category']['slug'] = $tmp_category[1];
		if (isset($tmp_subcategory)) {
			$ymschema['itemListElement']['subcategory']['name'] = $tmp_subcategory[0];
			$ymschema['itemListElement']['subcategory']['slug'] = $tmp_subcategory[1];
		}

	?>
	<script type="application/ld+json">
	{ 
		"@context":"http:\/\/schema.org\/",
		"@graph":[
			{
				"@type":"BlogPosting",
				"@id":<?php echo json_encode($ymschema['id'].'#BlogPosting'); ?>,
				"mainEntityOfPage":<?php echo json_encode($ymschema['id']); ?>,
				"headline":<?php echo json_encode($ymschema['headline']); ?>,
				"name":<?php echo json_encode($ymschema['headline']); ?>,
				"description":<?php echo json_encode($ymschema['description']); ?>,
				"datePublished":"<?php echo $ymschema['datePublished']; ?>",
				"dateModified":"<?php echo $ymschema['dateModified']; ?>",
				"url":<?php echo json_encode($ymschema['id']); ?>,
				<?php if (isset($ymschema['about'])) { ?>
				"about":[<?php echo $ymschema['about']; ?>],
				<?php } ?>
				"image":{ 
					"@type":"ImageObject",
					"@id":<?php echo json_encode($ymschema['image']['url']); ?>,
					"url":<?php echo json_encode($ymschema['image']['url']); ?>,
					"height":<?php echo $ymschema['image']['height']; ?>,
					"width":<?php echo $ymschema['image']['width']; ?>
				},
				"author":{ 
					"@type":"Person",
					"@id":<?php echo json_encode($ymschema['author']['url'].'#person'); ?>,
					"name":<?php echo json_encode($ymschema['author']['name']); ?>,
					"url":<?php echo json_encode($ymschema['author']['url']); ?>,
					"image":{ 
						"@type":"ImageObject",
						"@id":"https:\/\/secure.gravatar.com\/avatar\/<?php echo $ymschema['author']['image']; ?>?s=96&d=mm&r=g",
						"url":"https:\/\/secure.gravatar.com\/avatar\/<?php echo $ymschema['author']['image']; ?>?s=96&d=mm&r=g",
						"height":96,
						"width":96
					}
				},
				"publisher":{ 
					"@type":"Organization",
					"@id":<?php echo json_encode($ymschema['publisher']['url'].'/#organization'); ?>,
					"name":<?php echo json_encode($ymschema['publisher']['name']); ?>,
					"logo":{ 
						"@type":"ImageObject",
						"url":<?php echo json_encode($ymschema['publisher']['logo']); ?>,
						"width":600,
						"height":60
					}
				}
			},
			{ 
				"@type":"BreadcrumbList",
				"itemListElement":[ 
					{ 
						"@type":"ListItem",
						"position":1,
						"item":{ 
							"@id":<?php echo json_encode(site_url().'/'.$ymschema['itemListElement']['category']['slug'].'/#breadcrumbitem'); ?>,
							"name":<?php echo json_encode($ymschema['itemListElement']['category']['name']); ?>
						}
					}
					<?php if (isset($tmp_subcategory)) {?>
					,
					{ 
						"@type":"ListItem",
						"position":2,
						"item":{ 
							"@id":<?php echo json_encode(site_url().'/'.$ymschema['itemListElement']['subcategory']['slug'].'/#breadcrumbitem'); ?>,
							"name":<?php echo json_encode($ymschema['itemListElement']['subcategory']['name']); ?>
						}
					}
					<?php } ?>
				]
			}
		]
	}
	</script>
<?php
	}
	}
	add_action('wp_head', 'yandex_metrica_json_ld_schema');
?>