<?php
/*
Plugin Name: Just Tweet That Shit
Plugin URI: http://www.marcelpauly.de/just-tweet-that-shit/
Description: This Plugin connects WordPress with your Twitter account: When you publish a new article <strong>Just Tweet That Shit</strong> informs your followers with a shorten link.
Version: 0.2
Author: Marcel Pauly
Author URI: http://www.marcelpauly.de/
Min WP Version: 2.3
*/

/*  Copyright (C) 2010  Marcel Pauly

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


function jtts_tweet($post_ID = 0) {
	$post = get_post($post_ID);	
	if ($post_ID == 0 || $post->post_type != 'post' || $post->post_password != '') return $post_ID;
	require_once('twitteroauth.php');
	$options = get_option('jtts');	
	$url = get_permalink($post_ID);
	if ($options['shortener'] != 'none') $url = jtts_short($url);
	$cat = '';
	$cats = get_the_category($post->ID);
	if ($cats) foreach ($cats as $singlecat) $cat .= '#' . strtolower($singlecat->cat_name) . ' ';
	if ($cat != '') $cat = substr($cat, 0, -1);
	$tag = '';
	$tags = get_the_tags($post->ID);
	if ($tags) foreach ($tags as $singletag) $tag .= '#' . strtolower($singletag->name) . ' ';
	if ($tag != '') $tag = substr($tag, 0, -1);	
	$title = substr($post->post_title, 0, 140 - strlen(str_replace('[%tag%]', $tag, str_replace('[%cat%]', $cat, str_replace('[%url%]', $url, str_replace('[%title%]', '', $options['tweet']))))));
	$status = str_replace('[%tag%]', $tag, str_replace('[%cat%]', $cat, str_replace('[%url%]', $url, str_replace('[%title%]', $title, $options['tweet']))));
	$connection = new TwitterOAuth($options['consumer_key'], $options['consumer_secret'], $options['oauth_token'], $options['oauth_token_secret']);
	$connection->format = 'xml';
	$connection->post('statuses/update', array('status' => $status));
	return $post_ID;
}

function jtts_short($url) {
	$options = get_option('jtts');	
	switch ($options['shortener']) {
		case 'bitly':
			$api = 'http://api.bit.ly/v3/shorten?login=' . $options['bitly_usr'] . '&apiKey=' . $options['bitly_key'] . '&longUrl=' . urlencode($url) . '&format=txt';
			break;
		case 'jmp':
			$api = 'http://api.bit.ly/v3/shorten?login=' . $options['bitly_usr'] . '&apiKey=' . $options['bitly_key'] . '&longUrl=' . urlencode($url) . '&domain=j.mp&format=txt';
			break;
		case 'tinyurl':
			$api = 'http://tinyurl.com/api-create.php?url=' . urlencode($url);
			break;
		case 'twiturl':
			$api = 'http://api.twiturl.de/friends.php?new_url=' . urlencode($url) . '&output=txt';
			break;
		case 'isgd':
			$api = 'http://is.gd/api.php?longurl=' . urlencode($url);
			break;
		default:
			return $url;
	}
	if (function_exists('wp_remote_get')) $url_short = trim(wp_remote_retrieve_body(wp_remote_get($api)));
	else $url_short = trim(file_get_contents($api));
	if ($url_short == false || $url_short == 'INVALID_LOGIN') return $url;
	return $url_short;
}

function jtts_optionpage() {
?>
<div class="wrap">
<h2>Just Tweet That Shit</h2>
<div style="float: right; margin-top: 2em; border: 1px solid #ddd; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; padding: 0 1em; background: #fff; width: 240px;">
<h3><?php _e('Help', 'jtts'); ?></h3>
<p><?php _e('If you have some issues during the configuration, you\'ll find further information <a href="http://wordpress.org/extend/plugins/just-tweet-that-shit/faq/" target="_blank">in the FAQ</a> and <a href="http://www.marcelpauly.de/just-tweet-that-shit/#english" target="_blank">in my blog</a>. Feel free to post a comment for asking questions or reporting bugs.', 'jtts'); ?></p>
<h3><?php _e('Spread your Love', 'jtts'); ?></h3>
<iframe width="50" scrolling="no" height="60" frameborder="0" src="http://api.flattr.com/button/view/?uid=2832&amp;url=http%3A%2F%2Fwww.marcelpauly.de%2Fjust-tweet-that-shit%2F&amp;language=de_DE&amp;hidden=0&amp;title=Just%20Tweet%20That%20Shit&amp;category=software&amp;tags=&amp;description=Mit%20Just%20Tweet%20That%20Shit%20verbindest%20du%20dein%20WordPress-Blog%20mit%20deinem%20Twitter-Account%3A%20Sobald%20du%20einen%20neuen%20Artikel%20ver%C3%B6ffentlichst%2C%20informiert%20es%20deine%20Follower%20per%20Tweet%20dar%C3%BCber." border="0" marginheight="0" marginwidth="0" allowtransparency="true" style="float: right; margin-left: 0.5em; width: 50px;"></iframe>
<p><?php _e('You like the Plugin? Recommend it to your friends on <a href="http://twitter.com/home?status=Great+WordPress+plugin+to+tweet+your+new+articles:+Just+Tweet+That+Shit+http://j.mp/dpDvlC" title="Recommend this plugin to your followers on Twitter" target="_blank">Twitter</a> and <a href="http://www.facebook.com/sharer.php?u=http://www.marcelpauly.de/just-tweet-that-shit/&t=Just%20Tweet%20That%20Shit" title="Recommend this plugin to your friends on Facebook" target="_blank" onclick="window.open(this.href, \'facebook\', \'width=626, height=432\'); return false;">Facebook</a> or donate via <a href="http://www.flattr.com/" target="_blank">Flattr</a>.', 'jtts'); ?></p>
<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.marcelpauly.de%2Fjust-tweet-that-shit%2F&amp;layout=standard&amp;show_faces=true&amp;width=220&amp;action=like&amp;font=lucida+grande&amp;colorscheme=light&amp;height=80" scrolling="no" frameborder="0" style="border: none; overflow: hidden; width: 240px; height: 80px;" allowTransparency="true"></iframe>
</div>
<?php
	$options = get_option('jtts');
	if ($_GET['consumer_key'] != '' || $_GET['consumer_secret'] != '') {
		if ($_GET['oauth_token'] != '' && $_GET['oauth_token_secret'] != '') {
			echo '<div class="updated fade" style="margin: 2em 290px 2em 0;"><p><strong>' . __('Your blog has been successfully connected to your Twitter account.', 'jtts') . '</strong></p></div>';
			$options['consumer_key'] = $_GET['consumer_key'];
			$options['consumer_secret'] = $_GET['consumer_secret'];
			$options['oauth_token'] = $_GET['oauth_token'];
			$options['oauth_token_secret'] = $_GET['oauth_token_secret'];
		} elseif ($_GET['denied'] != '') {
			echo '<div class="error fade" style="margin: 2em 290px 2em 0;"><p>' . __('You denied the access.', 'jtts') . '</p></div>';
			$options['consumer_key'] = $_GET['consumer_key'];
			$options['consumer_secret'] = $_GET['consumer_secret'];
			$options['oauth_token'] = '';
			$options['oauth_token_secret'] = '';
		} else {
			echo '<div class="error fade" style="margin: 2em 290px 2em 0;"><p>' . __('Connection failed. Please check the "Consumer key" and "Consumer secret".', 'jtts') . '</p></div>';
			$options['consumer_key'] = $_GET['consumer_key'];
			$options['consumer_secret'] = $_GET['consumer_secret'];
			$options['oauth_token'] = '';
			$options['oauth_token_secret'] = '';
		}
	}
	if ($_POST['submit'] == __('Save Changes', 'jtts')) {
		echo '<div class="updated fade" style="margin: 2em 290px 2em 0;"><p><strong>' . __('Settings saved.', 'jtts') . '</strong></p></div>';
		$options['tweet'] = $_POST['tweet'];
		$options['shortener'] = $_POST['shortener'];
		$options['bitly_usr'] = $_POST['bitly_usr'];
		$options['bitly_key'] = $_POST['bitly_key'];
	}
	if ($_POST['submit'] == __('Delete connection', 'jtts')) {
		echo '<div class="updated fade" style="margin: 2em 290px 2em 0;"><p><strong>' . __('Connection deleted.', 'jtts') . '</strong></p></div>';
		$options['consumer_key'] = '';
		$options['consumer_secret'] = '';
		$options['oauth_token'] = '';
		$options['oauth_token_secret'] = '';
	}
	if ($_GET['error'] == 'curl') echo '<div class="error fade" style="margin: 2em 290px 2em 0;"><p>' . __('Your Server doesn\'t comply with the requirements: it doesn\'t support cURL.', 'jtts') . '</p></div>';
	if ($_GET['error'] == 'ossl') echo '<div class="error fade" style="margin: 2em 290px 2em 0;"><p>' . __('Your Server doesn\'t comply with the requirements: it doesn\'t support OpenSSL.', 'jtts') . '</p></div>';
	if ($_GET['error'] == 'both') echo '<div class="error fade" style="margin: 2em 290px 2em 0;"><p>' . __('Your Server doesn\'t comply with the requirements: it doesn\'t support cURL and OpenSSL.', 'jtts') . '</p></div>';
	update_option('jtts', $options);
?>
<form method="post" action="options-general.php?page=just-tweet-that-shit/jtts.php" style="margin: 2em 290px 2em 0; border: 1px solid #ddd; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; padding: 0 1em; background: #fff;">
<h3><?php _e('General Settings', 'jtts'); ?></h3>
<table class="form-table" style="width: auto; clear: none;">
<tr>
<th><label for="tweet"><?php _e('Automatic Tweet', 'jtts'); ?></label></th>
<td><input name="tweet" type="text" value="<?php echo $options['tweet']; ?>" id="tweet" class="regular-text" /><br /><span class="description"><?php _e('[%title%] = title of the article, [%url%] = URL of the article, [%tag%] = Tags of the article transformed to Twitter Hashtags,  [%cat%] = Categories of the article transformed to Twitter Hashtags', 'jtts'); ?></span></td>
</tr>
<tr>
<th><label for="shortener"><?php _e('URL shortening', 'jtts'); ?></label></th>
<td>
<select name="shortener" id="shortener" onchange="if (document.getElementById('shortener').options[document.getElementById('shortener').selectedIndex].value == 'bitly' || document.getElementById('shortener').options[document.getElementById('shortener').selectedIndex].value == 'jmp') document.getElementById('bitly_data').style.display = 'block'; else document.getElementById('bitly_data').style.display = 'none';">
<option value="none"<?php if ($options['shortener'] == 'none') echo ' selected="selected"'; ?>><?php _e('don\'t shorten URLs', 'jtts'); ?></option>
<option value="bitly"<?php if ($options['shortener'] == 'bitly') echo ' selected="selected"'; ?>>bit.ly (<?php _e('Login', 'jtts'); ?>)</option>
<option value="jmp"<?php if ($options['shortener'] == 'jmp') echo ' selected="selected"'; ?>>j.mp (<?php _e('bit.ly Login', 'jtts'); ?>)</option>
<option value="tinyurl"<?php if ($options['shortener'] == 'tinyurl') echo ' selected="selected"'; ?>>tinyurl.com</option>
<option value="twiturl"<?php if ($options['shortener'] == 'twiturl') echo ' selected="selected"'; ?>>twiturl.de</option>
<option value="isgd"<?php if ($options['shortener'] == 'isgd') echo ' selected="selected"'; ?>>is.gd</option>
</select>
<div id="bitly_data"<?php if ($options['shortener'] != 'bitly' && $options['shortener'] != 'jmp') echo ' style="display: none;"'; ?>>
<p><label for="bitly_usr"><?php _e('<a href="http://bit.ly/" target="_blank">bit.ly</a> username:', 'jtts'); ?></label><br />
<input name="bitly_usr" type="text" value="<?php echo $options['bitly_usr']; ?>" id="bitly_usr" class="regular-text" /></p>
<p><label for="bitly_key"><?php _e('bit.ly API Key:', 'jtts'); ?></label><br />
<input name="bitly_key" type="text" value="<?php echo $options['bitly_key']; ?>" id="bitly_key" class="regular-text" /></p>
</div>
</td>
</tr>
</table>
<p class="submit"><input name="submit" type="submit" value="<?php _e('Save Changes', 'jtts'); ?>" class="button-primary" /></p>
</form>
<?php if ($options['oauth_token'] == '' || $options['oauth_token_secret'] == '') { ?>
<form name="twitter" method="post" action="<?php echo get_bloginfo('url') . '/'; if (!defined('PLUGINDIR')) echo 'wp-content/plugins'; else echo PLUGINDIR; ?>/just-tweet-that-shit/jtts.php" style="margin: 2em 290px 2em 0; border: 1px solid #ddd; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; padding: 0 1em; background: #fff;">
<input type="hidden" name="url_wp" value="<?php echo get_bloginfo('url'); ?>/" />
<input type="hidden" name="url_plugin" value="<?php echo get_bloginfo('url') . '/'; if (!defined('PLUGINDIR')) echo 'wp-content/plugins'; else echo PLUGINDIR; ?>/just-tweet-that-shit/jtts.php" />
<h3><?php _e('Twitter Connection', 'jtts'); ?></h3>
<p><?php _e('To connect your blog to Twitter, three steps are necessary:', 'jtts'); ?></p>
<p><strong><?php _e('1. Twitter Application', 'jtts'); ?></strong><br />
<?php _e('First of all you have to register an own Twitter Application for your blog:', 'jtts'); ?></p>
<ul style="padding-left: 2em; list-style: square;">
<li><?php _e('Visit <a href="http://twitter.com/apps/new" target="_blank">this page</a>, if reqired log in with your Twitter credentials.', 'jtts'); ?></li>
<li><?php _e('Application Name: type in any name to identify your application; e.g. the title of your blog.', 'jtts'); ?></li>
<li><?php _e('Description: excogitate a brief description of your application. Make sure the field\'s not empty.', 'jtts'); ?></li>
<li><?php echo str_replace('[%url%]', get_bloginfo('url'), __('Application Website: enter <code>[%url%]/</code>.', 'jtts')); ?></li>
<li><?php _e('Application Type: choose "Browser".', 'jtts'); ?></li>
<li><?php echo str_replace('[%url%]', get_bloginfo('url'), __('Callback URL: enter <code>[%url%]/</code> again.', 'jtts')); ?></li>
<li><?php _e('Default Access type: choose &quot;Read &amp; Write&quot;.', 'jtts'); ?></li>
<li><?php _e('Save.', 'jtts'); ?></li>
</ul>
<p><strong><?php _e('2. Enter &quot;Consumer key&quot; and &quot;Consumer secret&quot;', 'jtts'); ?></strong><br />
<?php _e('Your Twitter Application provides &quot;Consumer key&quot; and &quot;Consumer secret&quot;. Enter them in this fields:', 'jtts'); ?></p>
<table class="form-table">
<tr>
<th><label for="consumer_key"><?php _e('Consumer key', 'jtts'); ?></label></th>
<td><input name="consumer_key" type="text" value="<?php echo $options['consumer_key']; ?>" id="consumer_key" class="regular-text" /></td>
</tr>
<tr>
<th><label for="consumer_secret"><?php _e('Consumer secret', 'jtts'); ?></label></th>
<td><input name="consumer_secret" type="text" value="<?php echo $options['consumer_secret']; ?>" id="consumer_secret" class="regular-text" /></td>
</tr>
</table>
<p><strong><?php _e('3. Allow connection', 'jtts'); ?></strong><br />
<?php _e('Click on the following button. Twitter will ask you to allow the access. After confirmation you\'ll return to this page and the connection is established.', 'jtts'); ?></p>
<p class="submit"><a href="javascript:twitterSignIn();" title="<?php _e('Connect to Twitter', 'jtts'); ?>"><img src="../<?php if (!defined('PLUGINDIR')) echo 'wp-content/plugins'; else echo PLUGINDIR; ?>/just-tweet-that-shit/signin.png" width="151" height="24" alt="Sign in with Twitter" /></a></p>
</form>
<script type="text/javascript">
function twitterSignIn() {
	if (document.getElementById('consumer_key').value == '' || document.getElementById('consumer_secret').value == '') {
		alert('<?php _e('Please type in your "Consumer key" and "Consumer secret".', 'jtts'); ?>');
	} else {
		document.twitter.submit();
	}
}
</script>
<?php } else { ?>
<form method="post" action="options-general.php?page=just-tweet-that-shit/jtts.php" style="margin: 2em 290px 2em 0; border: 1px solid #ddd; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; -khtml-border-radius: 5px; padding: 0 1em; background: #fff;">
<h3><?php _e('Twitter Connection', 'jtts'); ?></h3>
<p><?php _e('Connection established.', 'jtts'); ?></p>
<p class="submit"><input name="submit" type="submit" value="<?php _e('Delete connection', 'jtts'); ?>" class="button-primary" /></p>
</form>
<?php } ?>
</div>
<?php }

function jtts_menu() {
	add_options_page('Just Tweet That Shit', 'Just Tweet That Shit', 9, __FILE__, 'jtts_optionpage');
}

function jtts_install() {
	add_option('jtts', array(
		'tweet' => __('Just blogged: [%title%] [%url%]', 'jtts'),
		'shortener' => 'none',
		'bitly_usr' => '',
		'bitly_key' => '',
		'consumer_key' => '',
		'consumer_secret' => '',
		'oauth_token' => '',
		'oauth_token_secret' => ''
	));
}

function jtts_deinstall() {
    delete_option('jtts');
}

if (function_exists('add_action')) {
	add_action('admin_menu', 'jtts_menu');
	add_action('draft_to_publish', 'jtts_tweet');
	add_action('private_to_publish', 'jtts_tweet');
	add_action('future_to_publish', 'jtts_tweet');
	add_action('pending_to_publish', 'jtts_tweet');
	add_action('new_to_publish', 'jtts_tweet');
	register_activation_hook(__FILE__, 'jtts_install');
	register_deactivation_hook(__FILE__, 'jtts_deinstall');
	if (function_exists('load_plugin_textdomain')) {
		if (!defined('PLUGINDIR')) load_plugin_textdomain('jtts', '/wp-content/plugins/just-tweet-that-shit/');
		else load_plugin_textdomain('jtts', '/' . PLUGINDIR . '/just-tweet-that-shit/');
	}
} else {
	if (!function_exists('openssl_sign') || !function_exists('curl_init')) {
		if (!function_exists('openssl_sign') && !function_exists('curl_init')) $error = 'both';
		else {
			if (!function_exists('curl_init')) $error = 'curl';
			else $error = 'ossl';
		}
		header('Location: ' . $_POST['url_wp'] . 'wp-admin/options-general.php?page=just-tweet-that-shit/jtts.php&error=' . $error);
		break;
	}
	if ($_POST['consumer_key'] != '' && $_POST['consumer_secret'] != '' && $_POST['url_wp'] != '' && $_POST['url_plugin'] != '') {
		session_start();
		require_once('twitteroauth.php');
		define('CONSUMER_KEY', $_POST['consumer_key']);
		define('CONSUMER_SECRET', $_POST['consumer_secret']);
		define('OAUTH_CALLBACK', $_POST['url_plugin'] . '?consumer_key=' . CONSUMER_KEY . '&consumer_secret=' . CONSUMER_SECRET . '&url_wp=' . $_POST['url_wp']);
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
		$request_token = $connection -> getRequestToken(OAUTH_CALLBACK);
		$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		switch ($connection->http_code) {
			case 200:
				$url = $connection -> getAuthorizeURL($token);
				header('Location: ' . $url);
				break;
			default:
				header('Location: ' . $_POST['url_wp'] . 'wp-admin/options-general.php?page=just-tweet-that-shit/jtts.php&consumer_key=' . CONSUMER_KEY . '&consumer_secret=' . CONSUMER_SECRET);
		}
	} if ($_GET['consumer_key'] != '' || $_GET['consumer_secret'] != '') {
		if ($_GET['oauth_token'] != '' && $_GET['oauth_verifier'] != '') {
			session_start();
			require_once('twitteroauth.php');
			$consumer_key = $_GET['consumer_key'];
			$consumer_secret = $_GET['consumer_secret'];
			$oauth_token = $_GET['oauth_token'];
			$oauth_verifier = $_GET['oauth_verifier'];
			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_verifier);
			$access_token = $connection->getAccessToken($oauth_verifier);
			header('Location: ' . $_GET['url_wp'] . 'wp-admin/options-general.php?page=just-tweet-that-shit/jtts.php&consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret . '&oauth_token=' . $access_token['oauth_token'] . '&oauth_token_secret=' . $access_token['oauth_token_secret']);
		} else {
			header('Location: ' . $_GET['url_wp'] . 'wp-admin/options-general.php?page=just-tweet-that-shit/jtts.php&consumer_key=' . $_GET['consumer_key'] . '&consumer_secret=' . $_GET['consumer_secret'] . '&denied=' . $_GET['denied']);
		}
	}
}

?>