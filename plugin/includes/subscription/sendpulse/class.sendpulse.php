<?php

	/**
	 * Интеграция с сервисом Sendpulse
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright Alex Kovalev 03.12.2016
	 * @version 1.0
	 */
	class OPanda_SendPulseSubscriptionService extends OPanda_Subscription {

		public function initSendpulseLibs()
		{
			require_once('api/sendpulseInterface.php');
			require_once('api/sendpulse.php');

			$userId = get_option('opanda_sendpulse_user_id', null);
			$privateKey = get_option('opanda_sendpulse_secret_key', null);

			return new SendpulseApi($userId, $privateKey, 'file');
		}

		/**
		 * Returns lists available to subscribe.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getLists()
		{
			$sendpulse = $this->initSendpulseLibs();
			$address_books = $sendpulse->listAddressBooks();

			$lists = array();

			foreach($address_books as $key => $address_book) {
				$lists[] = array(
					'title' => $address_book->name,
					'value' => $address_book->id
				);
			}

			return array(
				'items' => $lists
			);
		}

		/**
		 * Subscribes the person.
		 */
		public function subscribe($identityData, $listId, $doubleOptin, $contextData, $verified)
		{

			$sendpulse = $this->initSendpulseLibs();

			$vars = $this->refine($identityData);

			if( isset($vars['email']) ) {
				unset($vars['email']);
			}

			$email = $identityData['email'];

			if( empty($vars['name']) && !empty($identityData['name']) ) {
				$vars['name'] = $identityData['name'];
			}
			if( empty($vars['last_name']) && !empty($identityData['family']) ) {
				$vars['last_name'] = $identityData['family'];
			}

			$responce = $sendpulse->addEmails($listId, array(
				array(
					'email' => $email,
					'variables' => $vars
				)
			));

			if( $responce->is_error ) {
				if( !empty($responce->message) ) {
					throw new OPanda_SubscriptionException ('Error adding subscriber: [' . $responce->message . ']');
				}

				return array('status' => 'error');
			}

			return array('status' => 'subscribed');
		}

		/**
		 * Checks if the user subscribed.
		 */
		public function check($identityData, $listId, $contextData)
		{
			return array('status' => 'subscribed');
		}

		/**
		 * Returns custom fields.
		 */
		public function getCustomFields($listId)
		{

			$defaultFields = array(
				array(
					'name' => 'phone',
					'title' => 'Телефон',
					'type' => 'phone'
				),
				array(
					'name' => 'city',
					'title' => 'Город',
					'type' => 'text'
				),
				array(
					'name' => 'birthday',
					'title' => 'День рожденья',
					'type' => 'birthday'
				)

			);

			$customFields = array();
			$mappingRules = array(
				'text' => array('text', 'checkbox', 'hidden')
			);

			foreach($defaultFields as $mergeVars) {
				$fieldType = $mergeVars['type'];

				$pluginFieldType = isset($mappingRules[$fieldType])
					? $mappingRules[$fieldType]
					: strtolower($fieldType);

				$fieldOptions = array();
				$can = array(
					'changeType' => true,
					'changeReq' => true,
					'changeDropdown' => true,
					'changeMask' => true
				);

				if( 'phone' === $pluginFieldType ) {
					$fieldOptions['mask'] = '9(999) 999-9999';
					$fieldOptions['maskPlaceholder'] = '_(___) ___-____';
				}

				if( 'birthday' === $pluginFieldType ) {
					$fieldOptions['mask'] = '99/99/9999';
					$fieldOptions['maskPlaceholder'] = 'дд/мм/годд';
				}

				$customFields[] = array(

					'fieldOptions' => $fieldOptions,
					'mapOptions' => array(
						'req' => false,
						'id' => $mergeVars['name'],
						'name' => $mergeVars['name'],
						'title' => $mergeVars['title'],
						'labelTitle' => $mergeVars['title'],
						'mapTo' => is_array($pluginFieldType)
							? $pluginFieldType
							: array($pluginFieldType),
						'service' => $mergeVars
					),
					'premissions' => array(

						'can' => $can
						/*'notices' => array(
							'changeReq' => __('You can change this checkbox in your Mailerlite account.', 'bizpanda'),
							'changeDropdown' => __('Please visit your Mailerlite account to modify the choices.', 'bizpanda')
						),*/
					)
				);
			}

			return $customFields;
		}
	}