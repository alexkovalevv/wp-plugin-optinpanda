<?php

	/**
	 * License page is a place where a user can check updated and manage the license.
	 */
	class OPanda_LicenseManagerPage extends OnpLicensing000_LicenseManagerPage {

		public $purchasePrice = '$23';

		public function configure()
		{

			if( !current_user_can('administrator') ) {
				$this->capabilitiy = "manage_opanda_licensing";
			}

			if( get_locale() == 'ru_RU' ) {
				$this->faq = false;
				$this->trial = false;
				$this->premium = false;
				$this->purchasePrice = '590р';
			} else {
				if( onp_build('ultimate') ) {
					$this->trial = false;
					$this->faq = false;
					$this->premium = false;
					$this->purchasePrice = '$59';
				} else {
					$this->purchasePrice = '$23';
				}
			}

			if( onp_build('free') ) {
				$this->menuPostType = OPANDA_POST_TYPE;
			} else {
				if( onp_license('free') ) {
					$this->menuTitle = __('Opt-In Panda', 'optinpanda');
					$this->menuIcon = '~/bizpanda/assets/admin/img/menu-icon.png';
				} else {
					$this->menuPostType = OPANDA_POST_TYPE;
				}
			}
		}
	}

	FactoryPages000::register($optinpanda, 'OPanda_LicenseManagerPage');
	/*@mix:place*/