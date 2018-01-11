<?php

	/**
	 * Интеграция с сервисом Sendpulse
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright Alex Kovalev 03.12.2016
	 * @version 1.0
	 */
	class OPanda_MailerLiteSubscriptionService extends OPanda_Subscription {

		public function request($endPoint, $method = 'GET', $data = array())
		{

			$apiKey = get_option('opanda_mailerlite_api_key', false);
			if( empty($apiKey) ) {
				throw new OPanda_SubscriptionException ('The API Key not set.');
			}

			$isPost = $method == 'POST';
			$url = 'http://api.mailerlite.com/api/v2/' . $endPoint;

			$caller = $isPost
				? 'wp_remote_post'
				: 'wp_remote_get';

			$args = array(
				'headers' => array(
					'X-MailerLite-ApiKey' => $apiKey,
					'Content-Type' => 'application/json'
				)
			);

			if( !empty($data) ) {
				$args['body'] = json_encode($data);
			}

			$result = $caller($url, $args);

			if( is_wp_error($result) ) {
				throw new OPanda_SubscriptionException(sprintf('Unexpected error occurred during connection to Mailerlite. %s', $result->get_error_message()));
			}

			if( empty($result['body']) ) {
				return array();
			}

			$data = json_decode($result['body'], true);
			if( $data === false ) {
				throw new OPanda_SubscriptionException(sprintf('Unexpected error occurred during connection to Mailerlite. %s', $result['body']));
			}

			return $data;
		}

		/**
		 * Returns lists available to subscribe.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getLists()
		{

			$result = $this->request('groups', 'GET');

			$lists = array();

			foreach($result as $value) {
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

			$vars = $this->refine($identityData);
			$email = $identityData['email'];

			if( empty($vars['name']) && !empty($identityData['name']) ) {
				$vars['name'] = $identityData['name'];
			}
			if( empty($vars['last_name']) && !empty($identityData['family']) ) {
				$vars['last_name'] = $identityData['family'];
			}

			$response = $this->request('groups/' . $listId . '/subscribers', 'POST', array(
				'email' => $email,
				'fields' => $vars,
				'type' => $verified
					? 'active'
					: ($doubleOptin
						? 'unconfirmed'
						: 'active')
			));

			if( isset($response['error']) || !isset($response['type']) ) {
				throw new OPanda_SubscriptionException ('[subscribe]: Unexpected error occurred [' . (isset($response['error'])
						? $response['error']['message']
						: '') . ']');
			}

			if( $response['type'] == 'active' ) {
				return array('status' => 'subscribed');
			}

			if( $response['type'] == 'unconfirmed' ) {
				return array(
					'status' => (!$verified && $doubleOptin)
						? 'pending'
						: 'subscribed'
				);
			}

			return array('status' => 'error');
		}

		/**
		 * Checks if the user subscribed.
		 */
		public function check($identityData, $listId, $contextData)
		{
			
			$response = $this->request('subscribers/' . $identityData['email'], 'GET');

			if( isset($response['error']) || !isset($response['type']) ) {
				throw new OPanda_SubscriptionException('[check]: Unexpected error occurred.');
			}

			if( $response['type'] == 'active' || $response['type'] == 'unconfirmed' ) {
				return array(
					'status' => ($response['type'] === 'active'
						? 'subscribed'
						: 'pending')
				);
			}

			return array('status' => 'error');
		}

		/**
		 * Returns custom fields.
		 */
		public function getCustomFields($listId)
		{
			$result = $this->request('fields', 'GET');

			if( empty($result) ) {
				return array();
			}

			$customFields = array();

			$mappingRules = array(
				'text' => array('text', 'checkbox', 'hidden'),
				'number' => array('integer', 'checkbox')
			);

			foreach($result as $mergeVars) {
				$can = array(
					'changeType' => true,
					'changeReq' => true,
					'changeDropdown' => true,
					'changeMask' => true
				);

				$fieldType = strtolower($mergeVars['type']);

				$pluginFieldType = isset($mappingRules[$fieldType])
					? $mappingRules[$fieldType]
					: $fieldType;

				if( in_array($mergeVars['key'], array('email')) ) {
					continue;
				}

				$fieldOptions = array();

				if( 'date' === $pluginFieldType ) {
					$can['changeMask'] = false;
				}

				if( 'birthday' == $mergeVars['key'] && $pluginFieldType == 'text' ) {
					$pluginFieldType = 'birthday';
					$can['changeMask'] = true;
				}

				if( 'phone' == $mergeVars['key'] ) {
					$pluginFieldType = 'phone';
				}

				$customFields[] = array(

					'fieldOptions' => $fieldOptions,
					'mapOptions' => array(
						'req' => false,
						'id' => $mergeVars['key'],
						'name' => $mergeVars['key'],
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
							'changeReq' => 'You can change this checkbox in your Mailerlite account.',
							'changeDropdown' => 'Please visit your Mailerlite account to modify the choices.'
						),*/
					)
				);
			}

			return $customFields;
		}
	}