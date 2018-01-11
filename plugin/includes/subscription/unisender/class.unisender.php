<?php

	class OPanda_UnisenderSubscriptionService extends OPanda_Subscription {

		/**
		 * Returns lists available to subscribe.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getLists()
		{
			$response = $this->callApi('getLists');

			if( empty($response) ) {
				throw new OPanda_SubscriptionException('Request to the api was failed.');
			}

			$lists = array();
			foreach($response['result'] as $value) {
				$lists[] = array(
					'title' => $value['title'],
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
			$apikey = get_option('opanda_unisender_api_key');
			$vars = $this->refine($identityData);

			if( empty($vars['Name']) && !empty($identityData['name']) ) {
				$vars['Name'] = $identityData['name'] . (!empty($identityData['family'])
						? ' ' . $identityData['family']
						: '');
			}

			$response = $this->callApi('subscribe', array(
				'list_ids' => $listId,
				'fields' => $vars,
				'request_ip' => $this->getClientIp(),
				'double_optin' => $verified
					? 1
					: ($doubleOptin
						? 0
						: 1),
				'overwrite' => 0
			));

			if( isset($response['error']) ) {
				throw new OPanda_SubscriptionException('[subscribe]: ' . $response['error']);
			}

			return array(
				'status' => (!$verified && $doubleOptin)
					? 'pending'
					: 'subscribed'
			);
		}

		/**
		 * Checks if the user subscribed.
		 */
		public function check($identityData, $listId, $contextData)
		{

			$response = $this->callApi('exportContacts', array(
				'list_ids' => $listId,
				'email' => $identityData['email']
			));

			if( isset($response['error']) ) {
				throw new OPanda_SubscriptionException('[check]: ' . $response['error']);
			}

			$fieldIndexEmailStatus = null;
			foreach($response['result']['field_names'] as $key => $val) {
				if( $val === "email_status" ) {
					$fieldIndexEmailStatus = $key;
				}
			}

			if( $fieldIndexEmailStatus === null ) {
				return array('status' => 'false');
			}

			$status = 'false';
			switch( $response['result']['data'][0][$fieldIndexEmailStatus] ) {
				case 'active':
					$status = 'subscribed';
					break;
				case 'invited':
					$status = 'pending';
					break;
			}

			return array('status' => $status);
		}

		/**
		 * Returns custom fields.
		 */
		public function getCustomFields($listId)
		{

			$response = $this->callApi('getFields');

			if( isset($response['error']) ) {
				throw new OPanda_SubscriptionException('[subscribe]: ' . $response['error']);
			}

			$customFields = array();
			$mappingRules = array(
				'text' => array(
					'text',
					'checkbox',
					'hidden'
				),
				'number' => array(
					'integer',
					'checkbox'
				),
				'bool' => 'checkbox',
				'string' => array(
					'text',
					'checkbox',
					'hidden'
				)
			);

			$response['result'][] = array(
				'id' => -1,
				'name' => 'phone',
				'type' => 'string',
				'public_name' => 'Телефон',
				'is_visible' => 1,
				'view_pos' => 1
			);

			foreach($response['result'] as $mergeVars) {
				$fieldType = $mergeVars['type'];

				$pluginFieldType = isset($mappingRules[$fieldType])
					? $mappingRules[$fieldType]
					: strtolower($fieldType);

				if( in_array($pluginFieldType, array('email')) ) {
					continue;
				}
				$can = array(
					'changeType' => true,
					'changeReq' => true,
					'changeDropdown' => false,
					'changeMask' => true
				);

				$fieldOptions = array();

				$customFields[] = array(

					'fieldOptions' => $fieldOptions,
					'mapOptions' => array(
						'req' => false,
						'id' => $mergeVars['name'],
						'name' => $mergeVars['name'],
						'title' => $mergeVars['public_name'],
						'labelTitle' => $mergeVars['public_name'],
						'mapTo' => is_array($pluginFieldType)
							? $pluginFieldType
							: array($pluginFieldType),
						'service' => $mergeVars
					),
					'premissions' => array(

						'can' => $can,
						'notices' => array(
							'changeReq' => 'You can change this checkbox in your Unisender account.',
							'changeDropdown' => 'Please visit your Unisender account to modify the choices.'
						),
					)
				);
			}

			return $customFields;
		}

		/**
		 * @return string
		 */
		protected function getClientIp()
		{
			$result = '';

			if( !empty($_SERVER['REMOTE_ADDR']) ) {
				$result = $_SERVER['REMOTE_ADDR'];
			} elseif( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
				$result = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
				$result = $_SERVER['HTTP_CLIENT_IP'];
			}

			if( preg_match('/([0-9]|[0-9][0-9]|[01][0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[0-9][0-9]|[01][0-9][0-9]|2[0-4][0-9]|25[0-5])){3}/', $result, $match) ) {
				return $match[0];
			}

			return $result;
		}

		//get data by method
		public function callApi($method = null, $params = array())
		{

			$apikey = get_option('opanda_unisender_api_key');

			if( empty($apikey) ) {
				throw new OPanda_SubscriptionException('[callApi]: Api key does not exist or an empty parameter is passed.');
			}

			if( empty($method) ) {
				throw new OPanda_SubscriptionException('[callApi]: The method has not been transferred.');

				return;
			}

			if( !is_array($params) ) {
				throw new OPanda_SubscriptionException('[callApi]: Parameters queries must be passed in an array.');

				return;
			}

			$params = array_merge(array(
				'api_key' => $apikey
			), $params);

			$params = http_build_query($params);
			$url = "https://api.unisender.com/ru/api/" . trim($method) . "?format=json&" . $params;

			$options = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => 0,
			);

			$ch = curl_init();
			curl_setopt_array($ch, $options);
			$result = curl_exec($ch);

			if( $result == false ) {
				throw new Exception(curl_error($ch));
			}
			curl_close($ch);

			$final = json_decode($result, true);

			if( empty($final) ) {
				throw new Exception('[callApi]: The request did not succeed, an unknown error occurred.');
			}

			return $final;
		}
	}