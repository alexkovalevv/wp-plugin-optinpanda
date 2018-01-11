<?php 

class OPanda_SendGridSubscriptionService extends OPanda_Subscription {
    
    public function getInstance() {
        
        $this->apiKey = get_option('opanda_sendgrid_apikey');

        require_once 'libs/SendGrid.php';    
        return new OPanda_SendGrid( $this->apiKey );
    }

    /**
     * Returns lists available to subscribe.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getLists() {
        
        $sg = $this->getInstance();
        $response = $sg->client->contactdb()->lists()->get();
        $data = $this->handleResponse( $response );
                
        $lists = array();
        foreach( $data->lists as $item ) {
            $lists[] = array(
                'title' => $item->name,
                'value' => $item->id
            );
        }
        
        return array(
            'items' => $lists
        ); 
    }
    
    /**
     * Sends an email.
     */
    public function send( $to, $subject, $body ) {
        
        $sg = $this->getInstance();
        
        $response = $sg->client->mail()->send()->post(array(
            'personalizations' => array(
                array(
                    'to' => array(
                        array('email' => $to)
                    ),
                    'subject' => $subject
                )
            ),
            'from' => array(
                'email' => get_option('opanda_sender_email', get_bloginfo('admin_email')),
                'name' => get_option('opanda_sender_name', get_bloginfo('name'))
            ),
            'content' => array(
                array(
                    'type' => 'text/html',
                    'value' => $body
                )
            )
        ));
        
        $this->handleResponse( $response, 202 );
    }

    /**
     * Subscribes the person.
     */
    public function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified ) {

        $vars = $this->refine( $identityData, true );
        $email = $identityData['email'];
        
        if ( empty( $vars['first_name'] ) && !empty( $identityData['name'] ) ) $vars['first_name'] = $identityData['name'];
        if ( empty( $vars['last_name'] ) && !empty( $identityData['family'] ) ) $vars['last_name'] = $identityData['family'];
        if ( empty( $vars['first_name'] ) && !empty( $identityData['displayName'] ) ) $vars['first_name'] = $identityData['displayName'];
        
        $sg = $this->getInstance();
        $response = $sg->client->contactdb()->recipients()->search()->get(null, array('email' => $email));
        $data = $this->handleResponse( $response );
        
        // aleary exists
        if ( !empty( $data->recipients ) ) {
            
            $subscriberId = isset( $data->recipients[0]->id ) 
                    ? $data->recipients[0]->id
                    : 0;

            // adding to a list
            
            if ( $subscriberId ) {
                $response = $sg->client->contactdb()->lists()->_($listId)->recipients()->_($subscriberId)->post();
                $data = $this->handleResponse( $response, 201 );
            }

            return array('status' => 'subscribed');
        } 
        
        // adding a new contact
        
        $response = $sg->client->contactdb()->recipients()->post(array($vars));
        $data = $this->handleResponse( $response, 201 );
 
        $subscriberId = isset( $data->persisted_recipients[0] ) 
                ? $data->persisted_recipients[0] 
                : 0;
        
        if ( !$subscriberId ) {
            throw new OPanda_SubscriptionException( __( 'Unable to add a new user. Please contact OnePress support.','optinpanda') );
        }

        // adding to a list

        $response = $sg->client->contactdb()->lists()->_($listId)->recipients()->_($subscriberId)->post();
        $data = $this->handleResponse( $response, 201 );
        
        return array('status' => 'subscribed');
        //return array('status' => (!$verified && $doubleOptin) ? 'pending' : 'subscribed');
    }
    
    /**
     * Checks if the user subscribed.
     */  
    public function check( $identityData, $listId, $contextData ) { 
        
        $MailChimp = $this->initMailChimpLibs();
        $response = $MailChimp->call('/lists/member-info', array( 
                       'id' => $listId,
                       'emails' => array( 
                           array('email' => $identityData['email'])           
                       )
                    ));

        if( !sizeof($response) || !isset($response['data'][0]['status']) ) {
            print_r($response);
            throw new OPanda_SubscriptionException('[check]: Unexpected error occurred.');
        }
         
        return array('status' => $response['data'][0]['status']);
    }
    
    /**
     * Prepares values enters by the user to save.
     */
    public function prepareFieldValueToSave( $mapOptions, $value ) {
        if ( empty( $value ) ) return $value;
        
        $fieldType = $mapOptions['service']['type'];

        if ( $fieldType == 'date' ) {
            return strtotime($value);
        }

        return $value;
    }
     
    /**
     * Returns custom fields.
     */
    public function getCustomFields( $listId ) {
        
        $sg = $this->getInstance();
        $response = $sg->client->contactdb()->custom_fields()->get();
        $data = $this->handleResponse( $response );
        
        array_unshift($data->custom_fields, (object)array('id' => 'last_name', 'name' => 'last_name', 'type' => 'text'));    
        array_unshift($data->custom_fields, (object)array('id' => 'first_name', 'name' => 'first_name', 'type' => 'text'));

        $customFields = array();
        $mappingRules = array(
            'text' => array('text', 'checkbox', 'hidden'),
            'number' => array('integer', 'checkbox')
        );

        foreach( $data->custom_fields as $customFieldItem ) {
            $fieldType = $customFieldItem->type;
                    
            $pluginFieldType = isset( $mappingRules[$fieldType] ) 
                    ? $mappingRules[$fieldType] 
                    : strtolower( $fieldType );
            
            if ( in_array($pluginFieldType, array('email'))) continue;            
            
            $can = array(
                'changeType' => true,
                'changeReq' => true,
                'changeDropdown' => false,
                'changeMask' => true
            );
            
            $fieldOptions = array();            
            $fieldOptions['req'] = false;

            $customFields[] = array(
                
                'fieldOptions' => $fieldOptions,
                
                'mapOptions' => array(
                    'req' => false,
                    'id' => $customFieldItem->name,
                    'name' => $customFieldItem->name,
                    'title' => $customFieldItem->name,
                    'labelTitle' => $customFieldItem->name,
                    'mapTo' => is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                    'service' => (array)$customFieldItem
                ),
                
                'premissions' => array(
                    
                    'can' => $can,
                    'notices' => array()
                )
            );
        }

        return $customFields;
    }
    
    public function getNameFieldIds() {
        return array( 'first_name' =>  'name', 'last_name' => 'family' );
    }
    
    protected function handleResponse( $response, $validCode = 200 ) {
        
        $code = $response->statusCode();
        $bodyJson = $response->body();

        $body = json_decode($bodyJson);

        if ( $code == 401 ) {
            throw new OPanda_SubscriptionException( __('Access denied. Please make sure that you set Full Access for Mail Send and Marketing Campaigns in settings of your API key in SendGrid.', 'optinpanda') );   
        }
        
        if ( $code !== $validCode ) {

            $error = isset( $body->errors[0]->message ) 
                    ? $body->errors[0]->message 
                    : sprintf( __( 'Unknown error. Please contact OnePress support [code %d]', 'optinpanda' ), $code );
            
            throw new OPanda_SubscriptionException( $error );   
        }
        
        
        return $body;
    }
}
