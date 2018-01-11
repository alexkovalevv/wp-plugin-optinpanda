<?php
	
	/**
	 * Интеграция с сервисом JustClick
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright Alex Kovalev 03.12.2016
	 * @version 1.0
	 */
	class OPanda_JustClickSubscriptionService extends OPanda_Subscription {
		
		public function request($endpoint, array $data = array())
		{
			$userName = get_option('opanda_justclick_user_name', false);
			$apiKey = get_option('opanda_justclick_api_key', false);
			
			if( empty($userName) || empty($apiKey) ) {
				throw new OPanda_SubscriptionException('Required server settings are not set', 'optinpanda');
			}
			
			$data['hash'] = $this->getHash($data, $userName, $apiKey);
			$url = 'http://' . $userName . '.justclick.ru/api/' . $endpoint;
			
			$send_data['body'] = $data;
			
			$result = wp_remote_post($url, $send_data);
			
			if( is_wp_error($result) ) {
				throw new OPanda_SubscriptionException(sprintf('An unknown error occurred while connecting to the Justclick service. %s', 'optinpanda'), $result->get_error_message());
			}
			
			if( empty($result['body']) ) {
				return array();
			}
			
			$data = json_decode($result['body'], true);
			
			if( $data === false ) {
				throw new OPanda_SubscriptionException(sprintf('An unknown error occurred while connecting to the Justclick service. %s', 'optinpanda'), $result['body']);
			}
			
			if( isset($data['error_code']) && $data['error_code'] !== 0 ) {
				if( $data['error_code'] === 4 ) {
					throw new OPanda_SubscriptionException('There was an error connecting to the JustClick service: [Invalid request signature]', 'optinpanda');
				}
				if( $data['error_code'] === 5 ) {
					throw new OPanda_SubscriptionException('There was an error connecting to the JustClick service: [The login in the JustClick system is not passed or found]', 'optinpanda');
				}
				if( $data['error_code'] === 6 ) {
					throw new OPanda_SubscriptionException('There was an error connecting to the JustClick service: [For the specified IP access is denied]', 'optinpanda');
				}
				if( $data['error_code'] === 7 ) {
					throw new OPanda_SubscriptionException('There was an error connecting to the JustClick service: [Account disabled]', 'optinpanda');
				}
			}
			
			return $data;
		}
		
		// Формируем подпись к передаваемым в API данным
		public function getHash($params, $userName, $apiKey)
		{
			$params = http_build_query($params);
			$params = "$params::$userName::$apiKey";
			
			return md5($params);
		}
		
		/**
		 * Returns lists available to subscribe.
		 *
		 * @since 1.0.0
		 * @return mixed[]
		 */
		public function getLists()
		{
			$response = $this->request('GetAllGroups');
			
			if( !isset($response['result']) ) {
				return array(
					'items' => array()
				);
			}
			
			$lists = array();
			
			foreach($response['result'] as $value) {
				$lists[] = array(
					'title' => $value['rass_title'],
					'value' => $value['rass_name']
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
			$vars['lead_email'] = $identityData['email'];
			
			if( empty($vars['lead_name']) ) {
				if( !empty($identityData['displayName']) ) {
					$vars['lead_name'] = $identityData['displayName'];
				} else if( !empty($identityData['name']) ) {
					$vars['lead_name'] = $identityData['name'];
					
					if( !empty($identityData['family']) ) {
						$vars['lead_name'] .= ' ' . $identityData['family'];
					}
				}
			}
			
			$response = $this->request('AddLeadToGroup', array_merge(array(
				'rid[0]' => $listId
			), $vars));
			
			if( isset($response['error_code']) && $response['error_code'] !== 0 && isset($response['error_text']) ) {
				throw new OPanda_SubscriptionException(sprintf('There was an error adding the subscriber: %s', 'optinpanda'), $response['error_text']);
			}
			
			if( isset($response['error_text']) && $response['error_text'] == 'The subscriber is already registered' ) {
				$response = $this->request('UpdateSubscriberData', $vars);
				
				if( isset($response['error_code']) && $response['error_code'] !== 0 && isset($response['error_text']) ) {
					throw new OPanda_SubscriptionException(sprintf('There was an error updating the subscriber data: %s', 'optinpanda'), $response['error_text']);
				}
				
				return array('status' => 'subscribed');
			}
			
			if( isset($response['error_text']) && $response['error_text'] == 'OK' ) {
				return array('status' => 'subscribed');
			}
			
			return array('status' => 'pending');
		}
		
		/**
		 * Checks if the user subscribed.
		 */
		public function check($identityData, $listId, $contextData)
		{
			$vars['lead_email'] = $identityData['email'];
			
			$response = $this->request('AddLeadToGroup', array_merge(array(
				'rid[0]' => $listId
			), $vars));
			
			if( isset($response['error_text']) && $response['error_text'] == 'The subscriber is already registered' ) {
				return array('status' => 'subscribed');
			}
			
			return array('status' => 'pending');
		}
		
		/**
		 * Returns custom fields.
		 */
		public function getCustomFields($listId)
		{
			
			$defaultFields = array(
				array(
					'name' => 'lead_phone',
					'title' => 'Телефон',
					'type' => 'phone'
				),
				array(
					'name' => 'lead_city',
					'title' => 'Город',
					'type' => 'text'
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
							'changeReq' => 'You can change this checkbox in your Mailerlite account.', 'optinpanda'),
							'changeDropdown' => 'Please visit your Mailerlite account to modify the choices.', 'optinpanda')
						),*/
					)
				);
			}
			
			return $customFields;
		}
	}