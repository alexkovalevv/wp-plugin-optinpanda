<?php

	#comp merge
	require(OPTINPANDA_DIR . '/plugin/admin/activation.php');

	if( onp_build('free') ) {
		require(OPTINPANDA_DIR . '/plugin/admin/notices.php');
	}

	require(OPTINPANDA_DIR . '/plugin/admin/pages/license-manager.php');

	if( onp_build('free') ) {
		require(OPTINPANDA_DIR . '/plugin/admin/pages/premium.php');
	}
	#endcomp

	/**
	 * Registers options for the subscription services.
	 *
	 * @see the 'opanda_subscription_services_options' action
	 *
	 * @since 1.1.3
	 * @return mixed[]
	 */
	function optinpanda_subscription_services_options($options)
	{

		// Justclick

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-justclick-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'justclick_user_name',
					'title' => __('Username', 'optinpanda'),
					'hint' => __('Please, enter your login in JustClick', 'optinpanda'),
				),
				array(
					'type' => 'textbox',
					'name' => 'justclick_api_key',
					'title' => __('API key', 'optinpanda'),
					'after' => ((get_locale() == 'ru_RU')
						? sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), admin_url('admin.php?page=how-to-use-bizpanda&onp_sl_page=justclick-get-api-options'))
						: ''),
					'hint' => __('Please, enter your api key in JustClick', 'optinpanda'),
				)
			)
		);

		// Sendpulse

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-sendpulse-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'sendpulse_user_id',
					'title' => __('ID', 'optinpanda'),
					'hint' => __('Please, enter your REST API ID in Sendpulse', 'optinpanda'),
					'after' => ((get_locale() == 'ru_RU')
						? sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), admin_url('admin.php?page=how-to-use-bizpanda&onp_sl_page=sendpulse-get-api-options'))
						: ''),
				),
				array(
					'type' => 'textbox',
					'name' => 'sendpulse_secret_key',
					'title' => __('Secret:', 'optinpanda'),
					'after' => ((get_locale() == 'ru_RU')
						? sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), admin_url('admin.php?page=how-to-use-bizpanda&onp_sl_page=sendpulse-get-api-options'))
						: ''),
					'hint' => __('Please, enter your REST API Secret in Sendpulse', 'optinpanda'),
				)
			)
		);

		// Mailerlite

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-mailerlite-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'mailerlite_api_key',
					'title' => __('API key', 'optinpanda'),
					'after' => ((get_locale() == 'ru_RU')
						? sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), admin_url('admin.php?page=how-to-use-bizpanda&onp_sl_page=mailerlite-get-api-options'))
						: ''),
					'hint' => __('Please enter your api key in unisender', 'optinpanda'),
				)
			)
		);

		// Unisender

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-unisender-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'unisender_api_key',
					'title' => __('API key', 'optinpanda'),
					'after' => ((get_locale() == 'ru_RU')
						? sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), admin_url('admin.php?page=how-to-use-bizpanda&onp_sl_page=unisender-get-api-options'))
						: ''),
					'hint' => __('Please enter your api key in unisender', 'optinpanda'),
				)
			)
		);

		// pechkinmail

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-pechkinmail-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'pechkinmail_username',
					'title' => __('Username', 'optinpanda'),
					'hint' => __('Please, enter your username in PechkinMail Account', 'optinpanda'),
				),
				array(
					'type' => 'textbox',
					'name' => 'pechkinmail_password',
					'title' => __('Password', 'optinpanda'),
					'hint' => __('Please, enter your password PechkinMail Account', 'optinpanda'),
				)
			)
		);

		// mailchimp

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-mailchimp-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'mailchimp_apikey',
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), 'http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key'),
					'title' => __('API Key', 'optinpanda'),
					'hint' => __('The API key of your MailChimp account.', 'optinpanda'),
				),
				array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'mailchimp_welcome',
					'title' => __('Send "Welcome" Email', 'optinpanda'),
					'default' => true,
					'hint' => __('Sends the Welcome Email configured in your MailChimp account after subscription (works only if the Single Opt-In set).', 'optinpanda')
				)
			)
		);

		// aweber

		if( !get_option('opanda_aweber_consumer_key', false) ) {

			$options[] = array(
				'type' => 'div',
				'id' => 'opanda-aweber-options',
				'class' => 'opanda-mail-service-options opanda-hidden',
				'items' => array(

					array(
						'type' => 'separator'
					),
					array(
						'type' => 'html',
						'html' => 'opanda_aweber_html'
					),
					array(
						'type' => 'textarea',
						'name' => 'aweber_auth_code',
						'title' => __('Authorization Code', 'optinpanda'),
						'hint' => __('The authorization code you will see after log in to your Aweber account.', 'optinpanda')
					)
				)
			);
		} else {

			$options[] = array(
				'type' => 'div',
				'id' => 'opanda-aweber-options',
				'class' => 'opanda-mail-service-options opanda-hidden',
				'items' => array(
					array(
						'type' => 'separator'
					),
					array(
						'type' => 'html',
						'html' => 'opanda_aweber_html'
					)
				)
			);
		}

		// getresponse

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-getresponse-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'getresponse_apikey',
					'title' => __('API Key', 'optinpanda'),
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), 'http://support.getresponse.com/faq/where-i-find-api-key'),
					'hint' => __('The API key of your GetResponse account.', 'optinpanda'),
				)
			)
		);

		// mymail

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-mymail-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'html',
					'html' => 'opanda_show_mymail_html'
				),
				array(
					'type' => 'separator'
				),
				array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'mymail_redirect',
					'title' => __('Redirect To Locker', 'optinpanda'),
					'hint' => sprintf(__('Set On to redirect the user after the email confirmation to the page where the locker located.<br />If Off, the MyMail will redirect the user to the page specified in the option <a href="%s" target="_blank">Newsletter Homepage</a>.', 'optinpanda'), admin_url('options-general.php?page=newsletter-settings&settings-updated=true#frontend'))
				)
			)
		);

		// mailster

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-mailster-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'html',
					'html' => 'opanda_show_mailster_html'
				),
				array(
					'type' => 'separator'
				),
				array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'mailster_redirect',
					'title' => __('Redirect To Locker', 'optinpanda'),
					'hint' => sprintf(__('Set On to redirect the user after the email confirmation to the page where the locker located.<br />If Off, the Mailster will redirect the user to the page specified in the option <a href="%s" target="_blank">Newsletter Homepage</a>.', 'optinpanda'), admin_url('edit.php?post_type=newsletter&page=mailster_settings#frontend'))
				)
			)
		);

		// mailpoet

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-mailpoet-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'html',
					'html' => 'opnada_show_mailpoet_html'
				)
			)
		);

		// acumbamail

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-acumbamail-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'acumbamail_customer_id',
					'title' => __('Customer ID', 'optinpanda'),
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get ID & Token</a>', 'optinpanda'), 'https://acumbamail.com/apidoc/'),
					'hint' => __('The customer ID of your Acumbamail account.', 'optinpanda')
				),
				array(
					'type' => 'textbox',
					'name' => 'acumbamail_api_token',
					'title' => __('API Token', 'optinpanda'),
					'hint' => __('The API token of your Acumbamail account.', 'optinpanda')
				)
			)
		);

		// knews

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-knews-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'html',
					'html' => 'opanda_show_knews_html'
				),
				array(
					'type' => 'separator'
				),
				array(
					'type' => 'checkbox',
					'way' => 'buttons',
					'name' => 'knews_redirect',
					'title' => __('Redirect To Locker', 'optinpanda'),
					'hint' => __('Set On to redirect the user after the email confirmation to the page where the locker located.<br />If Off, the K-news will redirect the user to the home page.', 'optinpanda')
				)
			)
		);

		// freshmail

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-freshmail-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'freshmail_apikey',
					'title' => __('API Key', 'optinpanda'),
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Keys</a>', 'optinpanda'), 'https://app.freshmail.com/en/settings/integration/'),
					'hint' => __('The API Key of your FreshMail account.', 'optinpanda')
				),
				array(
					'type' => 'textbox',
					'name' => 'freshmail_apisecret',
					'title' => __('API Secret', 'optinpanda'),
					'hint' => __('The API Sercret of your FreshMail account.', 'optinpanda')
				)
			)
		);

		// sendy

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-sendy-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'sendy_apikey',
					'title' => __('API Key', 'optinpanda'),
					'hint' => __('The API key of your Sendy application, available in Settings.', 'optinpanda'),
				),
				array(
					'type' => 'textbox',
					'name' => 'sendy_url',
					'title' => __('Installation', 'optinpanda'),
					'hint' => __('An URL for your Sendy installation, <strong>http://your_sendy_installation</strong>', 'optinpanda'),
				)
			)
		);

		// smartemailing

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-smartemailing-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'smartemailing_username',
					'title' => __('Username', 'optinpanda'),
					'hint' => __('Enter your username on SmartEmailing. Usually it is a email.', 'optinpanda'),
				),
				array(
					'type' => 'textbox',
					'name' => 'smartemailing_apikey',
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), 'https://app.smartemailing.cz/userinfo/show/api'),
					'title' => __('API Key', 'optinpanda'),
					'hint' => __('The API key of your SmartEmailing account.', 'optinpanda'),
				)
			)
		);

		// sendinblue

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-sendinblue-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'sendinblue_apikey',
					'title' => __('API Key', 'optinpanda'),
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), 'https://my.sendinblue.com/advanced/apikey'),
					'hint' => __('The API Key (version 2.0) of your Sendinblue account.', 'optinpanda')
				)
			)
		);

		// activecampaign

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-activecampaign-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'activecampaign_apiurl',
					'title' => __('API Url', 'optinpanda'),
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Url</a>', 'optinpanda'), 'http://www.activecampaign.com/help/using-the-api/'),
					'hint' => __('The API Url of your ActiveCampaign account.', 'optinpanda')
				),
				array(
					'type' => 'textbox',
					'name' => 'activecampaign_apikey',
					'title' => __('API Key', 'optinpanda'),
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), 'http://www.activecampaign.com/help/using-the-api/'),
					'hint' => __('The API Key of your ActiveCampaign account.', 'optinpanda')
				)
			)
		);

		// sendgrid

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-sendgrid-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'sendgrid_apikey',
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get API Key</a>', 'optinpanda'), 'https://app.sendgrid.com/settings/api_keys'),
					'title' => __('API Key', 'optinpanda'),
					'hint' => __('Your SendGrid API key. Grant <strong>Full Access</strong> for <strong>Mail Send</strong> and <strong>Marketing Campaigns</strong> in settings of your API key.', 'optinpanda'),
				)
			)
		);

		// sg autorepondeur

		$options[] = array(
			'type' => 'div',
			'id' => 'opanda-sgautorepondeur-options',
			'class' => 'opanda-mail-service-options opanda-hidden',
			'items' => array(

				array(
					'type' => 'separator'
				),
				array(
					'type' => 'textbox',
					'name' => 'sg_apikey',
					'after' => sprintf(__('<a href="%s" class="btn btn-default" target="_blank">Get Code</a>', 'optinpanda'), 'http://sg-autorepondeur.com/membre_v2/compte-options.php'),
					'title' => __('Activation Code', 'optinpanda'),
					'hint' => __('The Activation Code from your SG Autorepondeur account (<i>Mon compte -> Autres Options -> Informations administratives</i>).', 'optinpanda'),
				),
				array(
					'type' => 'textbox',
					'name' => 'sg_memberid',
					'title' => __('Member ID', 'optinpanda'),
					'hint' => __('The Memeber ID of your SG Autorepondeur account (<i>available on the home page below the SG logo, for example, 9059</i>).', 'optinpanda'),
				)
			)
		);

		return $options;
	}

	add_action('bizpanda_subscription_services_options', 'optinpanda_subscription_services_options');

	/**
	 * Shows HTML for Aweber.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	function opanda_aweber_html()
	{

		if( !get_option('opanda_aweber_consumer_key', false) ) {
			?>

			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10 opanda-aweber-steps">
					<p><?php _e('To connect your Aweber account:', 'optinpanda') ?></p>
					<ul>
						<li><?php _e('<span>1.</span> <a href="https://auth.aweber.com/1.0/oauth/authorize_app/92c68137" class="button" target="_blank">Click here</a> <span>to open the authorization page and log in.</span>', 'optinpanda') ?></li>
						<li><?php _e('<span>2.</span> Copy and paste the authorization code in the field below.', 'optinpanda') ?></li>
					</ul>
				</div>
			</div>

		<?php } else { ?>

			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10 opanda-aweber-steps">
					<p><strong><?php _e('Your Aweber Account is connected.', 'optinpanda') ?></strong></p>
					<ul>
						<li><?php _e('<a href="' . admin_url('admin.php?page=settings-bizpanda&opanda_screen=subscription&opanda_action=disconnectAweber') . '" class="button onp-sl-aweber-oauth-logout">Click here</a> <span>to disconnect.</span>', 'optinpanda') ?></li>
					</ul>
				</div>
			</div>

		<?php
		}
	}

	/**
	 * Shows HTML for MyMail.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	function opanda_show_mymail_html()
	{

		if( !defined('MYMAIL_VERSION') ) {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p>
						<strong><?php _e('The MyMail plugin is not found on your website. Emails will not be saved.', 'optinpanda') ?></strong>
					</p>
				</div>
			</div>
		<?php
		} else {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'optinpanda') ?></p>
				</div>
			</div>
		<?php
		}
	}

	/**
	 * Shows HTML for Mailster.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	function opanda_show_mailster_html()
	{

		if( !defined('MAILSTER_VERSION') ) {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p>
						<strong><?php _e('The Mailster plugin is not found on your website. Emails will not be saved.', 'opanda') ?></strong>
					</p>
				</div>
			</div>
		<?php
		} else {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'opanda') ?></p>
				</div>
			</div>
		<?php
		}
	}

	/**
	 * Shows HTML for MailPoet.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	function opnada_show_mailpoet_html()
	{

		if( !defined('WYSIJA') && !defined('MAILPOET_VERSION') ) {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p>
						<strong><?php _e('The MailPoet plugin is not found on your website. Emails will not be saved.', 'optinpanda') ?></strong>
					</p>
				</div>
			</div>
		<?php
		} else {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'optinpanda') ?></p>
				</div>
			</div>
		<?php
		}
	}

	/**
	 * Shows HTML for Knews.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	function opanda_show_knews_html()
	{

		if( !class_exists("KnewsPlugin") ) {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p>
						<strong><?php _e('The K-news plugin is not found on your website. Emails will not be saved.', 'optinpanda') ?></strong>
					</p>
				</div>
			</div>
		<?php
		} else {
			?>
			<div class="form-group">
				<label class="col-sm-2 control-label"></label>

				<div class="control-group controls col-sm-10">
					<p><?php _e('You can set a list where the subscribers should be added in the settings of a particular locker.', 'optinpanda') ?></p>
				</div>
			</div>
		<?php
		}
	}

	function opanda_on_saving_aweber_options($caller)
	{

		$service = isset($_POST['opanda_subscription_service'])
			? $_POST['opanda_subscription_service']
			: null;

		$authCode = isset($_POST['opanda_aweber_auth_code'])
			? trim($_POST['opanda_aweber_auth_code'])
			: null;

		unset($_POST['opanda_aweber_auth_code']);

		if( 'aweber' !== $service || get_option('opanda_aweber_consumer_key', false) ) {
			return;
		}

		// if the auth code is empty, show the error

		if( empty($authCode) ) {
			return $caller->showError(__('Unable to connect to Aweber. The Authorization Code is empty.', 'optinpanda'));
		}

		// try to get credential via api, shows the error if the exception occurs

		require_once OPANDA_BIZPANDA_DIR . '/admin/includes/subscriptions.php';
		$aweber = OPanda_SubscriptionServices::getService('aweber');

		try {
			$credential = $aweber->getCredentialUsingAuthorizeKey($authCode);
		} catch( Exception $ex ) {
			return $caller->showError($ex->getMessage());
		}

		// saves the credential

		if( $credential && sizeof($credential) ) {
			foreach($credential as $key => $value) {
				update_option('opanda_aweber_' . $key, $value);
			}
		}
	}

	add_action('opanda_on_saving_subscription_settings', 'opanda_on_saving_aweber_options');

	/**
	 * Adds scripts and styles in the admin area.
	 *
	 * @see the 'admin_enqueue_scripts' action
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function optinpanda_icon_admin_assets($hook)
	{

		if( onp_build('free') ) {
			?>
			<style>
				#menu-posts-opanda-item a[href*="premium-optinpanda"] {
					color: #43b8e3 !important;
				}
			</style>
			<?php

			return;
		} else {

			if( !onp_license('free') ) {
				return;
			}

			?>
			<style>
				#toplevel_page_license-manager-optinpanda div.wp-menu-image,
				#toplevel_page_license-manager-optinpanda:hover div.wp-menu-image,
				#toplevel_page_license-manager-optinpanda.wp-has-current-submenu div.wp-menu-image {
					background-position: 8px -30px !important;
				}
			</style>
		<?php
		}
	}

	add_action('admin_enqueue_scripts', 'optinpanda_icon_admin_assets');

	// ---
	// Help
	//

	/**
	 * Registers a help section for the Connect Locker.
	 *
	 * @since 1.0.0
	 */
	function optinpanda_register_help($pages)
	{
		global $opanda_help_cats;
		if( !$opanda_help_cats ) {
			$opanda_help_cats = array();
		}

		$items = array(

			array(
				'name' => 'email-locker',
				'title' => __('Email Locker', 'optinpanda'),
				'hollow' => true,
				'items' => array(
					array(
						'name' => 'what-is-email-locker',
						'title' => __('What is it?', 'optinpanda')
					),
					array(
						'name' => 'usage-example-email-locker',
						'title' => __('Quick Start Guide', 'optinpanda')
					),
				)
			)
		);

		if( !onp_build('free') ) {

			$items[] = array(
				'name' => 'connect-locker',
				'title' => __('Sign-In Locker', 'optinpanda'),
				'hollow' => true,
				'items' => array(
					array(
						'name' => 'what-is-signin-locker',
						'title' => __('What is it?', 'optinpanda')
					),
					array(
						'name' => 'usage-example-signin-locker',
						'title' => __('Quick Start Guide', 'optinpanda')
					),
				)
			);
		}

		if( get_locale() == 'ru_RU' ) {
			$items[] = array(
				'name' => 'service-settings',
				'title' => __('Get service api settings', 'optinpanda'),
				'hollow' => true,
				'items' => array(
					array(
						'name' => 'justclick-get-api-options',
						'title' => __('JustClick', 'optinpanda')
					),
					array(
						'name' => 'sendpulse-get-api-options',
						'title' => __('Sendpulse', 'optinpanda')
					),
					array(
						'name' => 'unisender-get-api-options',
						'title' => __('Unisender', 'optinpanda')
					),
					array(
						'name' => 'mailerlite-get-api-options',
						'title' => __('Mailerlite', 'optinpanda')
					)
				)
			);
		}

		array_unshift($pages, array(
			'name' => 'optinpanda',
			'title' => __('Plugin: Opt-In Panda', 'optinpanda'),
			'items' => $items
		));

		if( onp_build('free') ) {
			unset($pages[1]);
		}

		return $pages;
	}

	add_filter('bizpanda_help_pages', 'optinpanda_register_help');

	function bizpanda_help_page_func_optinpanda($manager)
	{
		require OPTINPANDA_DIR . '/plugin/admin/pages/help/optinpanda.php';
	}

	add_action('bizpanda_help_page_optinpanda', 'bizpanda_help_page_func_optinpanda');

	// JustClick
	function bizpanda_page_justclick_get_api_options($manager)
	{
		require OPTINPANDA_DIR . '/plugin/admin/pages/help/justclick-get-api-options.php';
	}

	add_action('bizpanda_help_page_justclick-get-api-options', 'bizpanda_page_justclick_get_api_options');

	// Sendpulse
	function bizpanda_page_sendpulse_get_api_options($manager)
	{
		require OPTINPANDA_DIR . '/plugin/admin/pages/help/sendpulse-get-api-options.php';
	}

	add_action('bizpanda_help_page_sendpulse-get-api-options', 'bizpanda_page_sendpulse_get_api_options');

	// Mailerlite
	function bizpanda_page_mailerlite_get_api_options($manager)
	{
		require OPTINPANDA_DIR . '/plugin/admin/pages/help/mailerlite-get-api-options.php';
	}

	add_action('bizpanda_help_page_mailerlite-get-api-options', 'bizpanda_page_mailerlite_get_api_options');

	// Unisender
	function bizpanda_page_unisender_get_api_options($manager)
	{
		require OPTINPANDA_DIR . '/plugin/admin/pages/help/unisender-get-api-options.php';
	}

	add_action('bizpanda_help_page_unisender-get-api-options', 'bizpanda_page_unisender_get_api_options');

	/**
	 * Makes internal page "License Manager" for the Opt-in Panda
	 *
	 * @since 1.0.0
	 * @return bool true
	 */
	function optinpanda_make_internal_license_manager($internal)
	{

		if( onp_build('premium') ) {
			if( onp_license('free') ) {
				return $internal;
			}
		}

		if( BizPanda::isSinglePlugin() ) {
			return $internal;
		}

		return true;
	}

	add_filter('factory_page_is_internal_license-manager-optinpanda', 'optinpanda_make_internal_license_manager');

	// ---
	// Menu
	//

	/**
	 * Changes the menu title if the Opt-In Panda is onle the plugin installed from the BizPanda Collection.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	function opanda_change_menu_title($title)
	{
		if( !BizPanda::isSinglePlugin() ) {
			return $title;
		}

		return __('Opt-In Panda', 'opanda');
	}

	add_filter('bizpanda_menu_title', 'opanda_change_menu_title');

	/**
	 * Returns an URL of page "Go Premium".
	 */
	function onp_op_get_premium_page_url($url, $name, $campaign = 'na')
	{
		global $optinpanda;

		if( !empty($name) && !in_array($name, array('email-locker')) ) {
			return $url;
		}

		if( get_option('onp_op_skip_trial', false) ) {
			return onp_sl_get_premium_url($campaign);
		} else {
			return admin_url('edit.php?post_type=opanda-item&page=premium-' . $optinpanda->pluginName);
		}
	}

	add_filter('bizpanda_premium_url', 'onp_op_get_premium_page_url', 10, 3);

	/**
	 * Returns an URL where the user can purchaes the plugin.
	 */
	function onp_op_get_url_to_purchase($campaign = 'na')
	{
		global $optinpanda;

		return onp_licensing_000_get_purchase_url($optinpanda, $campaign);
	}

	/**
	 *
	 */
	function onp_op_plugins_suggestions($suggestions)
	{
		if( empty($suggestions) ) {
			return $suggestions;
		}

		foreach($suggestions as $index => $item) {

			if( 'optinpanda' === $item['name'] ) {
				$suggestions[$index]['description'] = __('<p>Get more email subscribers the most organic way without tiresome popups.</p><p>Custom fields, export in CSV, content blurring, advanced options and more.</p>', 'optinpanda');
				continue;
			}

			if( 'optinpanda' === $item['name'] ) {
				$suggestions[$index]['description'] = __('<p>Helps to attract social traffic and improve spreading your content in social networks.</p><p>Also extends the Sign-In Locker by adding social actions you can set up to be performed.</p>', 'optinpanda');
			}
		}

		return $suggestions;
	}

	add_filter('opanda_plugins_suggestions', 'onp_op_plugins_suggestions');

	/**
	 * Adds extra options after list selection.
	 */
	function onp_op_extra_list_subscription_options($options, $service)
	{
		if( $service['name'] !== 'mailchimp' ) {
			return $options;
		}

		$options[] = array(
			'type' => 'hidden',
			'name' => 'mailchimp_groups'
		);

		$options[] = array(
			'type' => 'html',
			'html' => 'onp_op_mailchimp_groups_select'
		);

		return $options;
	}

	add_filter('opanda_extra_list_subscription_options', 'onp_op_extra_list_subscription_options', 10, 2);

	/**
	 * Displays extra options for the MailChimp Lists.
	 */
	function onp_op_mailchimp_groups_select()
	{
		?>
		<div class='form-group form-group-dropdown factory-control-mailchimp-groups'>
			<label class='col-sm-2 control-label'><?php _e('Groups', 'optinpanda') ?></label>

			<div class='control-group col-sm-10 mailchimp_groups'>
				<div class="factory-ajax-loader"></div>
				<div id="opanda-mailchimp-groups-holder"></div>
				<script>
					jQuery(function($) {

						var groupSelectWidget = {

							init: function() {
								var self = this;

								$("#opanda_subscribe_list").on('factory-loaded change', function() {
									self.refresh();
								});
							},

							refresh: function() {
								var self = this;

								self.showLoader();

								$.ajax({
									url: '<?php echo admin_url('admin-ajax.php') ?>',
									data: {
										action: 'opanda_get_mailchimp_groups',
										opanda_list_id: $("#opanda_subscribe_list").val()
									},
									dataType: 'json',
									success: function(response) {
										if( response.error ) {
											return self.showError(response.error);
										}
										self.fill(response.items);
									},
									error: function(response) {

										if( console && console.log ) {
											console.log(response.responseText);
										}

										self.showError('Unexpected error occurred during the ajax request.');
									},
									complete: function() {
										self.hideLoader();
									}
								});
							},

							showLoader: function() {
								$("#opanda-mailchimp-groups-holder").html("");
								$(".mailchimp_groups .factory-ajax-loader").show();
							},

							hideLoader: function() {
								$(".mailchimp_groups .factory-ajax-loader").hide();
							},

							fill: function(items) {

								if( !items || items.length == 0 ) {
									this.showEmpty();
									return;
								}

								var values = $.parseJSON($("#opanda_mailchimp_groups").val());

								var self = this;
								var $holder = $("#opanda-mailchimp-groups-holder").html("");

								for( var i in items ) {
									var groupInfo = items[i];
									var $group = $("<div class='opanda-mailchimp-group-wrap'><strong>" + groupInfo.title + "</strong></div>").appendTo($holder);

									for( var k in groupInfo['items'] ) {
										var valueInfo = groupInfo['items'][k];
										var checked = ( $.inArray(valueInfo[0] + "", values) >= 0 )
											? 'checked="checked" '
											: '';
										$("<div class='opanda-mailchimp-group-checkbox'><label><input type='checkbox' " + checked + " value='" + valueInfo[0] + "'><span>" + valueInfo[1] + "</span></label></div>").appendTo($group);
									}
								}

								$holder.find("input").bind("changed click", function() {
									self.updateValue();
								});

								this.updateValue();
							},

							updateValue: function() {
								var $holder = $("#opanda-mailchimp-groups-holder");
								var values = [];

								$holder.find("input:checked").each(function() {
									values.push($(this).attr('value'));
								});

								$("#opanda_mailchimp_groups").val(JSON.stringify(values));
							},

							showEmpty: function() {
								this.updateValue();
								$("#opanda-mailchimp-groups-holder").html('<?php _e('<span>No groups available for the list selected. <a href="http://kb.mailchimp.com/lists/groups/getting-started-with-groups" target="_blank">You can create them</a> in your MailChimp account.<span>', 'optinpanda') ?>');
							},

							showError: function(text) {
								$("#opanda-mailchimp-groups-holder").html("");
								var $holder = $("#opanda-mailchimp-groups-holder");

								var $error = $("<div class='factory-control-error'></div>")
									.append($("<i class='fa fa-exclamation-triangle'></i>"))
									.append(text);

								$holder.append($error);
							}
						};

						groupSelectWidget.init();
					});
				</script>
				<div class="help-block"><?php _e('Optional. Select Groups to add subscribers.', 'optinpanda') ?></div>
			</div>
		</div>
	<?php
	}

	/**
	 * Returns MailChimp Groups.
	 */
	function onp_op_get_mailchimp_groups_json()
	{
		$result = array();

		$listId = isset($_REQUEST["opanda_list_id"])
			? trim($_REQUEST["opanda_list_id"])
			: false;

		if( $listId ) {
			require_once OPANDA_BIZPANDA_DIR . '/admin/includes/subscriptions.php';
			$service = OPanda_SubscriptionServices::getService();

			try {
				$result = $service->getGroups($listId);
			} catch( Exception $ex ) {

				echo json_encode(array(
					'success' => false,
					'error' => $ex->getMessage()
				));
			}
		}

		echo json_encode(array(
			'success' => true,
			'items' => $result
		));

		exit;
	}

	add_action("wp_ajax_opanda_get_mailchimp_groups", "onp_op_get_mailchimp_groups_json");