<?php

	class OPanda_PechkinmailSubscriptionService extends OPanda_Subscription {

		private $username;
		private $password;

		public function initPechkinMailLibs()
		{

			$this->username = get_option('opanda_pechkinmail_username');
			$this->password = get_option('opanda_pechkinmail_password');

			require_once 'libs/PHPechkin.php';

			return new PHPechkin($this->username, $this->password);
		}

		/**
		 * Returns lists available to subscribe.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getLists()
		{

			$PechkinMail = $this->initPechkinMailLibs();
			$response = $PechkinMail->lists_get();

			if( empty($response) ) {
				throw new OPanda_SubscriptionException('Request to the api was failed.');
			}

			$lists = array();
			foreach($response['row'] as $value) {
				$lists[] = array(
					'title' => $value['name'],
					'value' => $value['id']
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

			$email = $identityData['email'];

			$PechkinMail = $this->initPechkinMailLibs();

			$mapData = array(
				'merge_1' => isset($identityData['merge_1'])
					? $identityData['merge_1']
					: $identityData['name'],
				'merge_2' => isset($identityData['merge_2'])
					? $identityData['merge_2']
					: $identityData['family'],
				'merge_3' => isset($identityData['merge_3'])
					? $identityData['merge_3']
					: null,
				'merge_4' => isset($identityData['merge_4'])
					? $identityData['merge_4']
					: null,
				'merge_5' => isset($identityData['merge_5'])
					? $identityData['merge_5']
					: null,
				'update' => true,
				'no_check' => false
			);

			$response = $PechkinMail->lists_add_member($listId, $email, $mapData);

			if( !is_array($response) || !sizeof($response) ) {
				throw new OPanda_SubscriptionException('[subscribe]: ' . (!empty($response)
						? $response
						: 'Unexpected error.'));
			}

			if( !isset($response['member_id']) || empty($response['member_id']) ) {
				throw new OPanda_SubscriptionException('[subscribe]: Unexpected error.');
			}

			return array('status' => 'subscribed');
		}

		/**
		 * Checks if the user subscribed.
		 */
		public function check($identityData, $listId, $contextData)
		{

			$PechkinMail = $this->initPechkinMailLibs();

			$response = $PechkinMail->lists_get_members($listId, array(
					'email' => $identityData['email']
				));

			if( !is_array($response) || !sizeof($response) ) {
				throw new OPanda_SubscriptionException('[check]: ' . (!empty($response)
						? $response
						: 'Unexpected error occurred.'));
			}

			if( !isset($response['row']['state']) || empty($response['row']['state']) ) {
				return array('status' => 'false');
			}

			return array(
				'status' => ($response['row']['state'] === 'active'
					? 'subscribed'
					: 'pending')
			);
		}

		/**
		 * Returns custom fields.
		 */
		public function getCustomFields($listId)
		{
			$PechkinMail = $this->initPechkinMailLibs();
			$response = $PechkinMail->lists_get($listId);

			if( empty($response) ) {
				throw new OPanda_SubscriptionException(__('Request to the api was failed.', 'bizpanda'));
			}

			$customFields = array();
			$mappingRules = array(
				'choice' => 'dropdown',
				'text' => array('text', 'checkbox', 'hidden'),
				'number' => array('integer', 'checkbox')
			);

			for($i = 0; $i <= 5; $i++) {
				if( !sizeof($response['row']['merge_' . $i]) ) {
					continue;
				}

				$field = unserialize($response['row']['merge_' . $i]);

				$fieldType = $field['type'];

				$pluginFieldType = isset($mappingRules[$fieldType])
					? $mappingRules[$fieldType]
					: strtolower($fieldType);

				if( in_array($pluginFieldType, array('email')) )
					continue;

				$can = array(
					'changeType' => true,
					'changeReq' => false,
					'changeDropdown' => false,
					'changeMask' => true
				);

				$fieldOptions = array();
				if( 'dropdown' === $pluginFieldType ) {
					foreach($field['title']['choices'] as $choice) {
						$fieldOptions['choices'][] = $choice;
					}
					$field['title'] = $field['title']['name'];
				}

				$fieldOptions['req'] = $field['req'] === "on"
					? true
					: false;

				$customFields[] = array(

					'fieldOptions' => $fieldOptions,
					'mapOptions' => array(
						'req' => $field['req'] === "on"
							? true
							: false,
						'id' => 'merge_' . $i,
						'name' => 'merge_' . $i,
						'title' => $field['title'],
						'labelTitle' => $field['title'],
						'mapTo' => is_array($pluginFieldType)
							? $pluginFieldType
							: array($pluginFieldType),
						'service' => $field
					),
					'premissions' => array(

						'can' => $can,
						'notices' => array(
							'changeReq' => 'You can change this checkbox in your Pechkinmail account.',
							'changeDropdown' => 'Please visit your Pechkinmail account to modify the choices.'
						),
					)
				);
			}

			return $customFields;
		}
	}