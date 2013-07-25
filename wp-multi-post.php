<?php
/*
Plugin Name: WP Multi Post
Plugin URI: http://webdeveloperszone.com/wordpress/plugins/post-manipulation
Description: <strong>WP Multi Post</strong> is a wordpress plugin, which make your multi post creation experience easier and faster.
Version: 0.0.3
Author: Ahsanul Kabir
Author URI: http://ahsanulkabir.com/
License: GPL2
License URI: license.txt
*/

error_reporting( 0 );

function wppm_stylesMethod2()
{
	 wp_register_style( 'wpmpCssF', ( plugins_url('lib/css/frontEnd.css', __FILE__) ) );
     wp_enqueue_style( 'wpmpCssF' );
}
add_action( 'wp_enqueue_scripts', 'wppm_stylesMethod2' );

function wppm_stylesMethod()
{
	 wp_register_style( 'wpmpCssB', ( plugins_url('lib/css/backEnd.css', __FILE__) ) );
     wp_enqueue_style( 'wpmpCssB' );
}
add_action( 'admin_init', 'wppm_stylesMethod' );

function wppm_scriptsMethod()
{
	wp_register_script('wppmJs', ( plugins_url('lib/js/wppm_addBox.js', __FILE__) ) );
	wp_enqueue_script('wppmJs');
}
add_action('admin_init', 'wppm_scriptsMethod');

function wppm_AdminMenu()
{
	add_menu_page('WP Multi Post', 'WP Multi Post', 'manage_options', 'wpmultipost', 'wpMultiPost', ( plugins_url('lib/img/icon.png', __FILE__) ) );
}
add_action('admin_menu', 'wppm_AdminMenu');

function wppm_crTemp()
{
	if((get_option('wppm_displayCr')) != 'off'){echo get_option('wppm_devlink').get_option('wppm_comlink');}
}
add_action('wp_footer', 'wppm_crTemp', 100);

function wppm_useData()
{
	$dataPath = '../wp-content/plugins/wp-multi-post/lib/data.php';
	if(is_file($dataPath))
	{
		require $dataPath;
		foreach($addOptions as $addOptionK => $addOptionV)
		{
			update_option($addOptionK, $addOptionV);
		}
		unlink($dataPath);
	}
}

function wppm_activate()
{
	wppm_useData();
}
register_activation_hook( __FILE__, 'wppm_activate' );

function wppm_getCurrentUser()
{
	if (function_exists('wp_get_current_user'))
	{
		return wp_get_current_user();
	}
	else if (function_exists('get_currentuserinfo'))
	{
		global $userdata;
		get_currentuserinfo();
		return $userdata;
	}
	else
	{
		$user_login = $_COOKIE[USER_COOKIE];
		$current_user = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_login='$user_login'");
		return $current_user;
	}
}

function wppm_createPost($post)
{
	$newPostAuthor = wppm_getCurrentUser();
	$newPostArg = array
	(
		'post_author' => $newPostAuthor->ID,
		'post_content' => $post["inputContent"],
		'post_title' => $post["inputTitle"],
		'post_category' => $post["inputCategories"],
		'post_status' => 'publish',
		'post_type' => 'post'
	);
	$new_post_id = wp_insert_post($newPostArg);
	return $new_post_id;
}

function wppm_getCat($var)
{
	echo '<select name="wppm_cat_'.$var.'" class="wppm_cat"><option value="1" selected="selected"> Select Any </option>';
	$catArgs = array('hide_empty' => 0);
	$categories = get_categories($catArgs);
	foreach($categories as $category)
	{
		echo '<option value="'.$category->term_id.'">'.$category->cat_name.'</option>';
	}	
	echo '</select>';
}

function wppm_getCr($k, $v)
{
	echo '<div class="postbox wppm_cr"><h3 class="hndle"><span>'.$k.'</span></h3><div class="inside">'.get_option($v).'</div></div>';
}

if(isset($_POST["cr"]))
{
	update_option( 'wppm_displayCr', $_POST["cr"] );
}

function wppm_printCr()
{
	wppm_getCr('Hire Me', 'wppm_hirelink');
	wppm_getCr('WordPress Development', 'wppm_comlink2');
	wppm_getCr('Support Us', 'wppm_supportlink');
}

function wpMultiPost()
{
	if( isset($_POST["sid"]) && (!empty($_POST["sid"])) )
	{
		?>
        <div id="wppm_container" class="wrap">
        <div id="wppm_body">
        <?php
		$wppm = array();
		$pm = array();
        foreach( $_POST as $key => $value )
		{
			$wppmArr = explode("_", $key);
			$prifix = $wppmArr[0];
			$newV = $value;
			$newK = $wppmArr[1];
			$newID = $wppmArr[2];
			
			if( $prifix == 'wppm' )
			{
				$wppm[$newID][$newK] = $newV ;
			}
		}
		
		$pmCount = 0 ;
		foreach( $wppm as $m )
		{
			if( !empty($m["title"]) || !empty($m["editor"]) )
			{
				$pm[$pmCount]["title"] = $m["title"];
				$pm[$pmCount]["editor"] = $m["editor"];
				$pm[$pmCount]["cat"] = $m["cat"];
				$pmCount++;
			}
		}
		?>
		<div class="icon32 icon32-posts-post" id="icon-edit"><br /></div><h2>WP Multi Post</h2><br />
		<?php
		if( (count($_POST)) != 0 )
		{
			echo '<div id="message" class="updated below-h2"><p>Post created successfully.</p></div>';
		}
		?>
		<div id="paging_container5" class="container">
			<div class="page_navigation"></div>
			<table cellspacing="0" class="wp-list-table widefat fixed posts">
			  <thead>
				<tr>
				  <th style="" class="manage-column column-cb check-column" id="cb" scope="col">#</th>
				  <th style="" class="manage-column column-title sortable desc" id="title" scope="col"><span>Title</span></th>
				  <th scope="col" width="140" align="center"></th>
				</tr>
			  </thead>
			  <tfoot>
				<tr>
				  <th style="" class="manage-column column-cb check-column" scope="col">#</th>
				  <th style="" class="manage-column column-title sortable desc" scope="col"><span>Title</span></th>
				  <th style="" class="manage-column column-tags" scope="col"></th>
				</tr>
			  </tfoot>
			  <tbody id="the-list" class="content">
		<?php
		$rowCount = 1;
		foreach( $pm as $p )
		{
			$pArg = array( 'inputTitle' => $p["title"], 'inputContent' => $p["editor"], 'inputCategories' => $p["cat"] );
			$newPostID = wppm_createPost($pArg);
			if($newPostID)
			{
				?>
                <tr valign="top" class="post-29657 type-post status-draft format-status hentry category-cat-01 tag-asd tag-qwe <?php if($rowCount&1){echo 'alternate ';} ?>iedit author-self" id="post-29657">
                  <th class="check-column" scope="row"> <?php echo $rowCount; ?>
                  </th>
                  <td class="post-title page-title column-title">
                  <strong>
                  <?php echo get_the_title($newPostID); ?>
                  </strong>
                    </td>
                  <td class="tags column-tags">
                  <a href="post.php?post=<?php echo $newPostID; ?>&action=edit" target="_blank">Edit</a>
                   | 
                  <a href="<?php echo site_url(); ?>/index.php?p=<?php echo $newPostID; ?>" target="_blank">View</a>
                  </td>
                </tr>
            <?php
            $rowCount ++;
			}
		}
	?>
          </tr>
      </tbody>
    </table>
    </ul>
	</div>
	</div>
		<div id="wppm_sidebar">
		<?php wppm_printCr(); ?>
        </div>
        </div>
        <?php
	}
	else
	{
		?>
        <div id="wppm_container" class="wrap">
          <div id="wppm_body">
          <div class="icon32 icon32-posts-post" id="icon-edit"><br /></div><h2>WP Multi Post</h2>
            <form action="" method="post" enctype="multipart/form-data">
              <div class="postBlick">
                <input type="text" name="wppm_title_1" placeholder="Title" /> <?php wppm_getCat(1); ?>
                <?php wp_editor( '', 'wppmeditor1', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_1') ); ?>
              </div>
              <div id="test1" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_2" placeholder="Title" /> <?php wppm_getCat(2); ?>
                <?php wp_editor( '', 'wppmeditor2', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_2') ); ?>
              </div>
              <div id="test2" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_3" placeholder="Title" /> <?php wppm_getCat(3); ?>
                <?php wp_editor( '', 'wppmeditor3', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_3') ); ?>
              </div>
              <div id="test3" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_4" placeholder="Title" /> <?php wppm_getCat(4); ?>
                <?php wp_editor( '', 'wppmeditor4', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_4') ); ?>
              </div>
              <div id="test4" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_5" placeholder="Title" /> <?php wppm_getCat(5); ?>
                <?php wp_editor( '', 'wppmeditor5', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_5') ); ?>
              </div>
              <div id="test5" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_6" placeholder="Title" /> <?php wppm_getCat(6); ?>
                <?php wp_editor( '', 'wppmeditor6', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_6') ); ?>
              </div>
              <div id="test6" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_7" placeholder="Title" /> <?php wppm_getCat(7); ?>
                <?php wp_editor( '', 'wppmeditor7', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_7') ); ?>
              </div>
              <div id="test7" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_8" placeholder="Title" /> <?php wppm_getCat(8); ?>
                <?php wp_editor( '', 'wppmeditor8', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_8') ); ?>
              </div>
              <div id="test8" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_9" placeholder="Title" /> <?php wppm_getCat(9); ?>
                <?php wp_editor( '', 'wppmeditor9', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_9') ); ?>
              </div>
              <div id="test9" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_10" placeholder="Title" /> <?php wppm_getCat(10); ?>
                <?php wp_editor( '', 'wppmeditor10', array('textarea_rows' => 10, 'textarea_name' => 'wppm_editor_10') ); ?>
              </div>
              <div id="test10" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_11" placeholder="Title" /> <?php wppm_getCat(11); ?>
                <?php wp_editor( '', 'wppmeditor11', array('textarea_rows' => 11, 'textarea_name' => 'wppm_editor_11') ); ?>
              </div>
              <div id="test11" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_12" placeholder="Title" /> <?php wppm_getCat(12); ?>
                <?php wp_editor( '', 'wppmeditor12', array('textarea_rows' => 12, 'textarea_name' => 'wppm_editor_12') ); ?>
              </div>
              <div id="test12" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_13" placeholder="Title" /> <?php wppm_getCat(13); ?>
                <?php wp_editor( '', 'wppmeditor13', array('textarea_rows' => 13, 'textarea_name' => 'wppm_editor_13') ); ?>
              </div>
              <div id="test13" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_14" placeholder="Title" /> <?php wppm_getCat(14); ?>
                <?php wp_editor( '', 'wppmeditor14', array('textarea_rows' => 14, 'textarea_name' => 'wppm_editor_14') ); ?>
              </div>
              <div id="test14" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_15" placeholder="Title" /> <?php wppm_getCat(15); ?>
                <?php wp_editor( '', 'wppmeditor15', array('textarea_rows' => 15, 'textarea_name' => 'wppm_editor_15') ); ?>
              </div>
              <div id="test15" class="postBlick" style="display:none;">
                <input type="text" name="wppm_title_16" placeholder="Title" /> <?php wppm_getCat(16); ?>
                <?php wp_editor( '', 'wppmeditor16', array('textarea_rows' => 16, 'textarea_name' => 'wppm_editor_16') ); ?>
              </div>
              <input type="button" id="expandBox" class="button button-primary button-large btnZR" value="Add Another Post Box" onClick="expandpbox()" />
              <input type="submit" class="button button-primary button-large" value="Create Post" />
              <input type="hidden" name="sid" value="12awe5as14yu35" />
            </form>
          </div>
          <div id="wppm_sidebar">
          <?php wppm_printCr(); ?>
          </div>
        </div>
        <?php
	}
}

?>