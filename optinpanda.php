<?php
	/**
	 * Plugin Name: {comp:optinpanda}
	 * Plugin URI: {comp:pluginUrl}
	 * Description: {comp:description}
	 * Author: OnePress
	 * Version: 2.0.1
	 * Author URI: http://byonepress.com
	 */

	if( defined('OPTINPANDA_PLUGIN_ACTIVE') ) {
		return;
	}
	define('OPTINPANDA_PLUGIN_ACTIVE', true);

	#comp remove
	// the following constants are used to debug features of diffrent builds
	// on developer machines before compiling the plugin

	// build: free, premium, ultimate
	if( !defined('BUILD_TYPE') ) {
		define('BUILD_TYPE', 'free');
	}
	// language: en_US, ru_RU
	if( !defined('LANG_TYPE') ) {
		define('LANG_TYPE', 'ru_RU');
	}
	// license: free, paid
	if( !defined('LICENSE_TYPE') ) {
		define('LICENSE_TYPE', 'free');
	}

	if( !defined('ONP_DEBUG_NETWORK_DISABLED') ) {

		define('ONP_DEBUG_NETWORK_DISABLED', false);
		define('ONP_DEBUG_CHECK_UPDATES', false);
	}

	if( !defined('ONP_DEBUG_TRIAL_EXPIRES') ) {

		define('ONP_DEBUG_TRIAL_EXPIRES', false);
		define('ONP_DEBUG_SHOW_BINDING_MESSAGE', false);
		define('ONP_DEBUG_SHOW_STYLEROLLER_MESSAGE', false);
		define('ONP_DEBUG_SL_OFFER_PREMIUM', false);
	}
	#endcomp

	define('OPTINPANDA_DIR', dirname(__FILE__));
	define('OPTINPANDA_URL', plugins_url(null, __FILE__));

	#comp remove
	// the compiler library provides a set of functions like onp_build and onp_license
	// to check how the plugin work for diffrent builds on developer machines

	require('bizpanda/libs/onepress/compiler/boot.php');
	#endcomp

	load_plugin_textdomain('optinpanda', false, dirname(plugin_basename(__FILE__)) . '/langs');

	// ---
	// BizPanda Framework
	//

	// inits bizpanda and its items
	require(OPTINPANDA_DIR . '/bizpanda/connect.php');
	define('OPTINPANDA_BIZPANDA_VERSION', 126);

	/**
	 * Fires when the BizPanda connected.
	 */
	function onp_op_init_bizpanda($activationHook = false)
	{

		/**
		 * Displays a note about that it's requited to update other plugins.
		 */
		if( !$activationHook && !bizpanda_validate(OPTINPANDA_BIZPANDA_VERSION, 'Opt-In Panda') ) {
			return;
		}

		// enabling features the plugin requires

		BizPanda::enableFeature('lockers');
		BizPanda::enableFeature('subscription');
		BizPanda::enableFeature('terms');
		BizPanda::enableFeature('social');

		if( !onp_build('free') ) {
			BizPanda::enableFeature('linkedin');
		}

		// creating the plugin object

		global $optinpanda;

		if( onp_lang('ru_RU') ) {
			$optinpanda = new Factory000_Plugin(__FILE__, array(
				'name' => 'optinpanda-rus',
				'title' => 'Opt-In Panda',
				'version' => '2.0.1',
				'assembly' => BUILD_TYPE,
				'lang' => LANG_TYPE,
				'api' => 'http://api.byonepress.com/1.1/',
				'premium' => 'https://sociallocker.ru/download/#optinpanda-purchase-anchor',
				'styleroller' => 'https://sociallocker.ru/styleroller',
				'account' => 'http://accounts.byonepress.com/',
				'updates' => OPTINPANDA_DIR . '/plugin/updates/',
				'tracker' => /*@var:tracker*/
					'0900124461779baebd4e030b813535ac'/*@*/,
				'childPlugins' => array('bizpanda')
			));
		} else {
			$optinpanda = new Factory000_Plugin(__FILE__, array(
				'name' => 'optinpanda',
				'title' => 'Opt-In Panda',
				'version' => '2.0.1',
				'assembly' => BUILD_TYPE,
				'lang' => LANG_TYPE,
				'api' => 'http://api.byonepress.com/1.1/',
				'premium' => 'http://api.byonepress.com/public/1.0/get/?product=optinpanda',
				'styleroller' => 'http://api.byonepress.com/public/1.0/get/?product=optinpanda',
				'account' => 'http://accounts.byonepress.com/',
				'updates' => OPTINPANDA_DIR . '/plugin/updates/',
				'tracker' => /*@var:tracker*/
					'0900124461779baebd4e030b813535ac'/*@*/,
				'childPlugins' => array('bizpanda')
			));
		}

		if( onp_build('free') ) {
			BizPanda::registerPlugin($optinpanda, 'optinpanda', 'free');
		} else {
			BizPanda::registerPlugin($optinpanda, 'optinpanda', 'premium');
		}

		if( onp_build('free') ) {
			$optinpanda->options['host'] = 'wordpress.org';
		}

		// requires factory modules
		$optinpanda->load(array(
			array('bizpanda/libs/factory/bootstrap', 'factory_bootstrap_000', 'admin'),
			array('bizpanda/libs/factory/notices', 'factory_notices_000', 'admin'),
			array('bizpanda/libs/onepress/api', 'onp_api_000'),
			array('bizpanda/libs/onepress/licensing', 'onp_licensing_000'),
			array('bizpanda/libs/onepress/updates', 'onp_updates_000')
		));

		if( onp_build('free') ) {
			require(OPTINPANDA_DIR . '/panda-items/email-locker/boot.php');
		} else {
			require(OPTINPANDA_DIR . '/panda-items/email-locker/boot.php');
			require(OPTINPANDA_DIR . '/panda-items/signin-locker/boot.php');
		}

		require(OPTINPANDA_DIR . '/plugin/boot.php');
	}

	add_action('bizpanda_init', 'onp_op_init_bizpanda');

	/**
	 * Activates the plugin.
	 *
	 * TThe activation hook has to be registered before loading the plugin.
	 * The deactivateion hook can be registered in any place (currently in the file plugin.class.php).
	 */
	function onp_op_activation()
	{

		// if the old version of the bizpanda which doesn't contain the function bizpanda_connect has been loaded,
		// ignores activation, the message suggesting to upgrade the plugin will be appear instead
		if( !function_exists('bizpanda_connect') ) {
			return;
		}

		// if the bizpanda has been already connected, inits the plugin manually
		if( defined('OPANDA_ACTIVE') ) {
			onp_op_init_bizpanda(true);
		} else bizpanda_connect();

		global $optinpanda;
		$optinpanda->activate();
	}

	register_activation_hook(__FILE__, 'onp_op_activation');

	/**
	 * Displays a note about that it's requited to update other plugins.
	 */
	if( is_admin() && defined('OPANDA_ACTIVE') ) {
		bizpanda_validate(OPTINPANDA_BIZPANDA_VERSION, 'Opt-In Panda');
	}