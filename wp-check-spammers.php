<?php /*
Plugin Name: WP Check Spammers
Plugin URI: http://www.xaviermedia.com/wordpress/plugins/wp-check-spammers.php
Description: This plugin will use the <a href="http://temerc.com/forums/viewtopic.php?f=71&t=6103">SpamBot Search Tool</a> to search for spammers based on IP, name and email. 
Version: 0.4
Author: Xavier Media&reg;
Author URI: http://www.xaviermedia.com/

*/

//************ you shouldn't edit below this line!*******************************/



// hooks, call wp_check_spammers function just before comment is posted. 
add_filter('preprocess_comment','wp_check_spammers',0);
add_action('admin_menu', 'wpcs_menu');
add_action('comment_form', 'wpcs_commentform', 5, 0);
register_activation_hook(__FILE__, 'wpcs_activate');

define('WPCS_TEMERC_DIRECTORY', WP_CONTENT_DIR."/Check_Spammers/");
define('WPCS_TEMERC_URL', WP_CONTENT_URL."/Check_Spammers/Check_Spammers/");

function wpcs_menu() 
{
	add_options_page('WP Check Spammers', 'WP Check Spammers', 8, 'wp-check-spammers.php', 'wpcs_options');

	$options  = unserialize(get_option('wpcsoptions'));
	if (!isset($options['everset'])) {
		// Set default values
		$options = array(
			"checkserver" => "http://checkspammers.xaviermedia.com/",
			"email" => "",
			"subject" => "Spammer blocked by WP Check Spammer",
			"fromemail" => "nobody@". str_replace('www.','',$_SERVER[HTTP_HOST]));
		$opt = serialize($options);
		update_option('wpcsoptions', $opt);
	}



	if ( $_GET['page'] == basename(__FILE__) ) 
	{
	    global $wpcs_options;

      	if ( 'save_temerc' == $_REQUEST['action'] ) 
		{
		    $options = get_option('wpcsoptions');
            $options = unserialize($options);

			$options["checkserver"] = $_REQUEST[checkserver];

			$opt = serialize($options);
			update_option('wpcsoptions', $opt);
			update_option('wpcsoptionssaved', 'yes');
		}
		
      	if ( 'save_email' == $_REQUEST['action'] ) 
		{
		    $options = get_option('wpcsoptions');
            $options = unserialize($options);

			$options["everset"] = true;
			$options["email"] = $_REQUEST[email];
		    $options["subject"] = $_REQUEST[subject];
		    $options["fromemail"] = $_REQUEST[fromemail];

			$opt = serialize($options);
			update_option('wpcsoptions', $opt);
			update_option('wpcsoptionssaved', 'yes');
		}
		
		
		if ( 'smtp_test' == $_REQUEST['action'] ) {
		    wpcs_bag('smtp_required', wpcs_smtp_auth_required());
		}
		
		if ( 'install_temerc' == $_REQUEST['action'] ) {
		    wpcs_install_temerc();
		}

	}
}

function wpcs_options() 
{
	$opt  = get_option('wpcsoptions');

	if(is_array($opt)) {
	    $options = $opt;
	}
	else {
        $options = unserialize($opt);
	}

	echo '<div class="wrap">';
	echo '<h2>WP Check Spammers Options</h2>';
						
	echo '<div class="updated fade-ff0000"><p><strong>Stuck on these options?</strong> <a href="http://www.xaviermedia.com/php/wp-check-spammers.php" target="_blank">Read The Documentation Here</a> or <a href="http://forum.xaviermedia.com/php-&-cgi-scripts-f3.html" target="blank">Visit Our Support Forum</a></p></div>';
	echo '<div style="clear:both;"></div>';
	
	
	if(wpcs_bag('installed_temerc')) 
	{
	    echo '<div class="updated fade-ff0000"><p><strong>SpamBot Search Tool</strong> installed in '.WPCS_TEMERC_DIRECTORY.'</p></div>';
	    echo '<div style="clear:both;"></div>';
	}
						

	echo '<h3>Spam Checking Server</h3>';
	echo '<form action="'. $_SERVER['REQUEST_URI'] .'" method="post">';
	echo '<p>This is the location of the SpamBot Search Tool. Include <i>http://</i> and a slash at the end.<br /><input name="checkserver" size=65 type="text" value="'. $options[checkserver] .'" /><br />';
	
	if(wpcs_temerc_installed()) 
	{	
		echo '<p>You have the SpamBot Search Tool installed at '.WPCS_TEMERC_URL.'</p>';        
	}
	echo '<input type="hidden" name="action" value="save_temerc" />';
	
	echo '<input name="save" type="submit" value="Save Spam Checking Server" class="button-primary"   />';
	echo '</form>';
	
	if(!wpcs_temerc_installed()) 
	{	
      	echo '<div>If you use our server <i>http://checkspammers.xaviermedia.com/</i> links to our site will show up in your blog, to have the link removed ';

    		echo '<div style="display: inline;"><form action="'. $_SERVER['REQUEST_URI'] .'" method="post" style="display: inline;">';
		echo '<input name="install_temerc" type="submit" value="Install Temerc" class="button-primary"   />';
    		echo '<input type="hidden" name="action" value="install_temerc" /></form>';
		echo '</form></div>';

		echo ' on your own server, or install manually by downloading from <A HREF="http://temerc.com/Check_Spammers/check_spammers.zip">this link</A>.</div>';
	}

	echo '<h3>Email Settings</h3>';
	echo '<form action="'. $_SERVER['REQUEST_URI'] .'" method="post">';	
	echo '<p>Fill in your email address here if you would like the plugin to send you an email everytime a spammer is blocked. Leaving this field blank will turn off the notify emails.<br /><input name="email" size=65 type="text" value="'. $options[email] .'" /></p>';
	echo '<p>Fill in the from email address to be used as sender for the emails sent.<br /><input name="fromemail" size=65 type="text" value="'. $options[fromemail] .'" /></p>';
	echo '<p>Subject of the email you get when a spammer is blocked.<br /><input name="subject" size=65 type="text" value="'. $options[subject] .'" /></p>';

	echo '<input name="save_email" type="submit" value="Save email settings" class="button-primary"/>';

	echo '<input type="hidden" name="action" value="save" /></form>';
		
	echo '<p>Some hosts need SMTP authentication when sending emails. This button tests if your server requires this additional authentication.</p>';

	if('smtp_test' == $_REQUEST['action']) {
	  if(wpcs_bag('smtp_required')) {
        echo '<div class="updated fade-ff0000"><p>Your server appears to require SMTP authentication. You can enable this feature by installing and configuring the <a href="http://wordpress.org/extend/plugins/wp-mail-smtp/" target="_blank">WP Mail SMTP</a> plugin.</p></div>';
	    echo '<div style="clear:both;"></div>';
  	  }
	  else {
        echo '<div class="updated fade-ff0000"><p>Your server does not appear to require SMTP authentication.</p></div>';
	    echo '<div style="clear:both;"></div>';
	  }
	}

	echo '<div><form action="'. $_SERVER['REQUEST_URI'] .'" method="post">';
	echo '<input name="smtp_test" type="submit" value="Test SMTP" class="button-primary"   />';
	echo '<input type="hidden" name="action" value="smtp_test" /></form>';
	echo '</form></div>';
	
	echo '</div>';

	echo '<div class="updated fade-ff0000"><p><strong>Need web hosting for your blog?</strong> Get 10 Gb web space and unlimited transfer for only $3.99/month at <a href="http://2ve.org/xMY3/" target="_blank">eXavier.com</a></p></div>';

?>
	<a target="_blank" href="http://feed.xaviermedia.com/xm-wordpress-stuff/"><img src="http://feeds.feedburner.com/xm-wordpress-stuff.1.gif" alt="XavierMedia.com - Wordpress Stuff" style="border:0"></a><BR/>

	<h2>Wordpress plugins from Xavier Media&reg;</h2>
	<UL>
	<li><a href="http://wordpress.org/extend/plugins/wp-statusnet/" TARGET="_blank">WP-Status.net</a> - Posts your blog posts to one or multiple Status.net servers and even to Twitter 
	<li><a href="http://wordpress.org/extend/plugins/wp-email-to-facebook/" TARGET="_blank">WP Email-to-Facebook</a> - Posts your blog posts to one or multiple Facebook pages from your WordPress blog 
	<li><a href="http://wordpress.org/extend/plugins/wp-check-spammers/" TARGET="_blank">WP-Check Spammers</a> - Check comment against the SpamBot Search Tool using the IP address, the email and the name of the poster as search criteria 
	<li><a href="http://wordpress.org/extend/plugins/xm-backup/" TARGET="_blank">XM Backup</a> - Do backups of your Wordpress database and files in the uploads folder. Backups can be saved to Dropbox, FTP accounts or emailed
	</UL>
<?php	



}

function wpcs_warning() 
{
	if ( get_option('wpcsoptionssaved') != 'yes') 
	{
			echo "
			<div id='wpcs-warning' class='updated fade-ff0000'><p><strong>".__('WP Check Spammers is almost ready.')."</strong> ".sprintf(__('You must <a href="options-general.php?page=wp-check-spammers.php">select the server to check spammers against</a> for the plugin to work.'), "plugins.php?page=wpcs-options")."</p></div>
			";
	}
}
add_action('admin_notices', 'wpcs_warning');



function wpcs_commentform()
{
	$opt  = get_option('wpcsoptions');
	$options = unserialize($opt);
	if ($options[checkserver] == "http://checkspammers.xaviermedia.com/")
	{
		echo '<p>This site is using <a href="http://www.xaviermedia.com/wordpress/plugins/wp-check-spammers.php">WP Check Spammers</A> from <A HREF="http://www.xaviermedia.com/">Xavier Media</A> to filter out spam comments.</p>';
	}
}

// This is where the checking is done. The code is from http://temerc.com/forums/viewtopic.php?f=71&t=6103
// with some modifications to work for Wordpress

function wp_check_spammers($comment_data)
{
	$opt  = get_option('wpcsoptions');
	$options = unserialize($opt);

	if (isset($options['everset'])) 
	{

		$comment_author		= $comment_data[comment_author];
		$comment_author_email	= $comment_data[comment_author_email];
		$ip 				= $_SERVER['REMOTE_ADDR'];

		$fspamcheck = file_get_contents($options[checkserver] .'check_spammers_plain.php?name='. $comment_author .'&email='. $comment_author_email .'&ip='. $ip);

		if (strpos($fspamcheck, 'TRUE') !==False) 
		{
			// Notify admin via e-mail
			$blockedby = str_replace(' TRUE', '', $fspamcheck);
			$blockedby = str_replace(' ', ' & ', $blockedby);
			if ($options[email] != "")
			{
				$msg = 'The following was blocked by the '. $blockedby .' filter<br><br>Username: '. $comment_author .'<br><br>Email: '. $comment_author_email .'<br><br>IP: '. $ip .'<br><br>Comment: '. htmlspecialchars($comment_data[comment_content]);
				$to 		= $options[email];
				$from 	= $options[fromemail];
				$subject 	= $options[subject];
				$headers 	=    "MIME-Versin: 1.0\r\n" .
					"Content-type: text/html; charset=ISO-8859-1; format=flowed\r\n" .
					"Content-Transfer-Encoding: 8bit\r\n" .
					"From: " . $from . "\r\n" .
					"X-Mailer: hpHosts Spam Filter";
				wp_mail($to, $subject, $msg, $headers);
			}
			// Notify user
			 wp_die( __('Your comment has been blocked by our <a href="http://temerc.com/Check_Spammers/">spam filter</a> and <a href="http://www.xaviermedia.com/wordpress/plugins/wp-check-spammers.php">WP Check Spammers</a>. Please either try again or contact an administrator') );
		}
	}
	
	return $comment_data;
} // end function

// Test if SMTP needed
function wpcs_smtp_auth_required() 
{
    
	global $phpmailer;

	// (Re)create it, if it's gone missing
	// Code from wp_mail() function in pluggable.php
	if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
		require_once ABSPATH . WPINC . '/class-phpmailer.php';
		require_once ABSPATH . WPINC . '/class-smtp.php';
		$phpmailer = new PHPMailer();
	}
	    
	$phpmailer = new PHPMailer();  // create a new object
	$phpmailer->IsSMTP(); // enable SMTP
	$phpmailer->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$phpmailer->SMTPAuth = false;  // authentication enabled
	$phpmailer->Subject = 'SMTP auth test';
	$phpmailer->From = 'test@example.com';
	$phpmailer->FromName = 'Test';
	$phpmailer->Body = 'This test message was generated by the WP-Check Spammers plugin to determine if this server requires SMTP authentication. Please contact dave@davidmichaelross.com with any questions or concerns.';
	$phpmailer->AddAddress('test@example.com');
	if(!$phpmailer->Send()) {
		if(strpos($phpmailer->ErrorInfo, '530') !== FALSE) {
		    return TRUE;
		} 
	}  

    return FALSE;
}

/**
 * More transient than a transient.
 */
function wpcs_bag($key, $value = FALSE) 
{
  static $bag = array();
  
  if($value) {
    $bag[$key] = $value;    
  }
  else {
    if(array_key_exists($key, $bag)) {
      return $bag[$key];
    }
  }
  
  return FALSE;
}

function wpcs_temerc_installed() 
{
  return file_exists(WPCS_TEMERC_DIRECTORY);    
}


function wpcs_install_temerc() 
{
	require_once (ABSPATH . 'wp-admin/includes/file.php');
	WP_Filesystem();
  
	$url = "http://temerc.com/Check_Spammers/check_spammers.zip";
	$tmpfile = download_url( $url );

	$res = unzip_file($tmpfile, WPCS_TEMERC_DIRECTORY);

	unlink($tmpfile);
  
	$options = get_option('wpcsoptions');
	$options = unserialize($options);
	$options['checkserver'] = WPCS_TEMERC_URL;
	$options['everset'] = TRUE;
	update_option('wpcsoptions', serialize($options));
  
	// Prime the Check_Spammers service so we
	// don't get any errors/missing CSS on first visit
	wp_remote_get($check_spammers_url);
  
	wpcs_bag('installed_temerc', WPCS_TEMERC_DIRECTORY);
}

function wpcs_activate() 
{
	$opt = get_option('wpcsoptions',
	// Defaults
	serialize(array(
		"checkserver" => "http://checkspammers.xaviermedia.com/",
		"email" => "",
		"subject" => "Spammer blocked by WP Check Spammer",
		"fromemail" => "nobody@". str_replace('www.','',$_SERVER[HTTP_HOST])        
		))
	);
	update_option('wpcsoptions', $opt);
}