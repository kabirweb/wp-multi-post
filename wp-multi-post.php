<?php
/*
Plugin Name: WP Multi Post
Plugin URI: http://webdeveloperszone.com/wordpress/plugins/post-manipulation
Description: <strong>WP Multi Post</strong> is a wordpress plugin, which make your multi post creation experience easier and faster.
Version: 0.0.1
Author: Ahsanul Kabir
Author URI: http://ahsanulkabir.com/
License: GPL2
License URI: license.txt
*/

add_action( 'admin_init', 'wppm_PluginStyles' );
function wppm_PluginStyles()
{
	 wp_register_style( 'wp-multi-post', plugins_url('wp-multi-post.css', __FILE__) );
     wp_enqueue_style( 'wp-multi-post' );
}

/* Register Admin Menu & Pages */
function wppm_AdminMenu()
{
	add_menu_page('WP Multi Post', 'WP Multi Post', 'manage_options', 'wpmultipost', 'wpMultiPost', '../wp-content/plugins/wp-multi-post/icon.png');
}
add_action('admin_menu', 'wppm_AdminMenu');

/* Plugin Foot */
function wppm_foot()
{
	$wppmLink = get_option( 'wppm_link' );
	$linkZ = '<a href="http://ahsanulkabir.com/?ref='.$_SERVER['SERVER_NAME'].'&rel=wp-welcome-message" title="Ahsanul Kabir" style="display:none;">Ahsanul Kabir</a><a href="http://webdeveloperszone.com/" title="Web Development" style="display:none;">Web Development</a>';
	if( !empty($wppmLink) )
	{
		if( $wppmLink == 'yes' )
		{
			echo $linkZ;
		}
	}
	else
	{
		echo $linkZ;
	}
}
add_action('wp_footer', 'wppm_foot', 100);

/* Add Option */
function wppm_activate()
{
	$wppmLink = get_option( 'wppm_link' );
	if(!empty($wppmLink))
	{
		update_option( 'wppm_link', 'yes' );
	}
	else
	{
		add_option( 'wppm_link', 'yes' );
	}
}
register_activation_hook( __FILE__, 'wppm_activate' );

/* Save link option */
if(isset($_POST["backlink"]))
{
	if($_POST["backlink"]=="yes")
	{
		update_option( 'wppm_link', 'yes' );
	}
	else
	{
		update_option( 'wppm_link', 'no' );
	}
}

/* Get Current User */
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

/* Create Post */
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

/* Get Categories */
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

/* Advert */
function wppm_advert()
{
	?>
    <div id="wppm_sidebar">
      <div class="postbox advert">
        <h3 class="hndle"><span>Hire Me</span></h3>
        <div class="inside">
          <a href="https://www.odesk.com/users/~010897326779e806fb" target="_blank"><img src="../wp-content/plugins/wp-multi-post/img/ak.jpg" width="100%" /></a>
          <a href="http://ahsanulkabir.com/?ref=<?php echo $_SERVER['SERVER_NAME']; ?>&rel=wp-multi-post" target="_blank" title="Ahsanul Kabir" style="float:right; height:26px; line-height:26px;">View Developer's Profile</a>
        </div>
      </div>
      <div class="postbox advert">
        <h3 class="hndle"><span>Wordpress Development</span></h3>
        <div class="inside">
          <a href="http://webdeveloperszone.com/" target="_blank"><img src="../wp-content/plugins/wp-multi-post/img/wp-multi-post.png" width="100%" /></a>
        </div>
      </div>
      <div class="postbox advert">
        <h3 class="hndle"><span>Support Us</span></h3>
        <div class="inside" style="background:none;">
          <ul>
          	<li style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #ddd;">Like us on facebook <iframe width="100px" height="25px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.facebook.com/plugins/like.php?href=https%3A%2F%2Ffacebook.com%2Fwebdevzone&send=false&layout=button_count&width=450&show_faces=false&font&colorscheme=light&action=like&height=21" style="display:inline-block; position:relative; margin-bottom:-11px; margin-left:5px;"></iframe></li>
            <li>Support us by keeping link. 
            <form action="" method="post" style="display:inline-block;">
            <input type="hidden" name="backlink" value="yes" />
            <button class="button button-primary" style="height:20px; line-height:10px; width: 36px;">Yes</button>
            </form> 
            <form action="" method="post" style="display:inline-block;">
            <input type="hidden" name="backlink" value="no" />
            <button class="button button-primary" style="height:20px; line-height:10px; width: 36px;">No</button>
            </form>
            <em style="color:#999;">(This link will not displayed on public)</em>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <?php
}

/* Post Manipulation */
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
			echo '<div id="message" class="updated below-h2"><p>Post cloned successfully.</p></div>';
		}
		?>
		<div id="paging_container5" class="container">
			<div class="page_navigation"></div>
			<table cellspacing="0" class="wp-list-table widefat fixed posts">
			  <thead>
				<tr>
				  <th style="" class="manage-column column-cb check-column" id="cb" scope="col">#</th>
				  <th style="" class="manage-column column-title sortable desc" id="title" scope="col"><span>Title</span></th>
				  <th scope="col" width="140" align="center">Create Post</th>
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
		<?php wppm_advert(); ?>
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
              <input type="button" id="expandBox" class="button button-primary button-large btnZR" value="Add Another Post Box" onClick="expandpbox()" />
              <input type="submit" class="button button-primary button-large" value="Create Post" />
              <input type="hidden" name="sid" value="12awe5as14yu35" />
            </form>
          </div>
          <?php wppm_advert(); ?>
        </div>
        <script type="text/javascript">
        var count = 1;
        function expandpbox()
        {
           document.getElementById("test"+count).style.display = 'block';
           var count2 = count + 1 ;
           var ifrmID = 'wppmeditor'+count2+'_ifr';
           document.getElementById(ifrmID).style.height = '300px' ;
           count++;
           if( count == 9 )
           {
              document.getElementById("expandBox").style.display = 'none' ;
           }
        }
        </script>
        <?php
	}
}

?>