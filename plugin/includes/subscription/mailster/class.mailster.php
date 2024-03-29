<?php

class OPanda_MailsterSubscriptionService extends OPanda_Subscription {

    public function init( $options ) {
        parent::init( $options );
    }

    /**
     * Returns available Opt-In modes.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getOptInModes() {
        return array( 'double-optin', 'quick-double-optin', 'quick' );
    }
        
    /**
     * Returns a Mailster Lists Manager
     * 
     * @since 1.0.7
     * @return mailster_lists
     */
    public function getListsManager() {
        
        if ( !defined('MAILSTER_VERSION') ) {
            throw new OPanda_SubscriptionException( __( 'The Mailster plugin is not found on your website.', 'optinpanda' ) ); 
        }
        
        $path = MAILSTER_DIR . '/classes/lists.class.php';
        if ( !file_exists( $path ) ) {
            throw new OPanda_SubscriptionException( __( 'Unable to connect with the Mailster Lists Manager. Your version of Mailster plugin is not supported. Please contact OnePress support.', 'optinpanda' ) ); 
        }
        
        require_once $path;
        
        if ( !class_exists( 'mailster_lists' ) && !class_exists( 'MailsterLists' ) ) {
            throw new OPanda_SubscriptionException( __( 'Unable to connect with the Mailster Lists Manager. Your version of Mailster plugin is not supported. Please contact OnePress support.', 'optinpanda' ) ); 
        }  
        
        return mailster('lists');
    }
    
    /**
     * Returns a Mailster Subscribers Manager
     * 
     * @since 1.0.7
     * @return mailster_lists
     */
    public function getSubscribersManager() {
        
        if ( !defined('MAILSTER_VERSION') ) {
            throw new OPanda_SubscriptionException( __( 'The Mailster plugin is not found on your website.', 'optinpanda' ) ); 
        }
        
        $path = MAILSTER_DIR . '/classes/subscribers.class.php';
        if ( !file_exists( $path ) ) {
            throw new OPanda_SubscriptionException( __( 'Unable to connect with the Mailster Subscribers Manager. Your version of Mailster plugin is not supported. Please contact OnePress support.', 'optinpanda' ) ); 
        }
        
        require_once $path;
        
        if ( !class_exists( 'mailster_subscribers' ) && !class_exists('MailsterSubscribers') ) {
            throw new OPanda_SubscriptionException( __( 'Unable to connect with the Mailster Subscribers Manager. Your version of Mailster plugin is not supported. Please contact OnePress support.', 'optinpanda' ) ); 
        }  
        
        return mailster('subscribers');
    }    
    
    /**
     * Returns lists available to subscribe.
     * 
     * @since 1.0.0
     * @return mixed[]
     */
    public function getLists() {
        
        $manager = $this->getListsManager();

        $lists = array();
        foreach( $manager->get() as $item ) {
            $lists[] = array(
                'title' => $item->name,
                'value' => $item->ID
            );
        }

        return array(
            'items' => $lists
        ); 
    }

    /**
     * Subscribes the person.
     */
    public function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified ) {

        $manager = $this->getSubscribersManager();
        $userData = $identityData;

        if ( !empty( $identityData['name'] ) )
            $userData['firstname'] = $identityData['name'];

        if ( !empty( $identityData['family'] ) )
            $userData['lastname'] = $identityData['family'];

        if ( empty( $identityData['name'] ) && empty( $identityData['family'] ) && !empty( $identityData['displayName'] ) )
            $userData['firstname'] = $identityData['displayName'];
        
        // if already subscribed

        $subscriber = $manager->get_by_mail( $identityData['email'] );

        if ( !empty( $subscriber ) ) {
            
            $lists = $manager->get_lists( $subscriber->ID, true );
            if ( !in_array( $listId, $lists ) ) {
                $manager->assign_lists( $subscriber->ID, $listId, false );
            }
            
            $manager->update($userData);
            
            if ( !$doubleOptin ) return array('status' =>  'subscribed');

            $status = intval($subscriber->status );
            if ( 0 === $status ) $this->_sendConfirmation( $subscriber->ID, $contextData );
            
            return array('status' => 1 === $status ? 'subscribed' : 'pending');
        }

        // if it's a new subscriber
        
        $ip = mailster_option('track_users') ? mailster_get_ip() : NULL;
            
        $userData['status'] = $verified ? 1 : ( !$doubleOptin ? 1 : 0 );
        $userData['ip_signup'] = $ip;
        $userData['ip'] = $ip;
        $userData['referer'] = isset( $contextData['postUrl'] ) ? $contextData['postUrl'] : null;
                
        // the method 'add' sends the confirmation email if the status = 0,
        // we need to replace the original confirmation link with our own link,
        // then we turn on the constant MYMAIL_DO_BULKIMPORT to prevent sending the confirmation email
        // in the methid 'add'
                
        define('MYMAIL_DO_BULKIMPORT', true);
        
        $result = $manager->add( $userData, false ); 
        if ( is_wp_error( $result ) ) {
           throw new OPanda_SubscriptionException ( '[subscribe]: ' . $result->get_error_message() ); 
        }
        
        $subscriberId = $result;
        $manager->assign_lists( $result, $listId, true );

        if ( !$verified && $doubleOptin ) $this->_sendConfirmation( $subscriberId, $contextData );
        return array('status' => $verified ? 'subscribed' : ( $doubleOptin ? 'pending' : 'subscribed' ));
    }
    
    /**
     * Send the confirmation email replacing the original confirmation link generated via Mailster.
     * 
     * @since 1.0.7
     * @return void
     */
    private function _sendConfirmation( $subscriberId, $contextData ) {
            
        if ( !get_option('opanda_mailster_redirect', false ) ) {
            mailster('subscribers')->send_confirmations( $subscriberId, true, true );  
        } else {
            
            $this->_beforeSendingConfirmation( $contextData );
            mailster('subscribers')->send_confirmations( $subscriberId, true, true );  
            $this->_afterSendingConfirmation( $contextData );     
        }
    }
    
    /**
     * A helper method to prepare hooks before sending the confirmation email.
     * 
     * @since 1.0.7
     * @return void
     */
    private function _beforeSendingConfirmation( $contextData ) {
        
        if ( isset( $contextData['postUrl'] ) && !empty( $contextData['postUrl'] ) ) {
            $this->_returnPage = $contextData['postUrl'];
        } else {
            $this->_returnPage = get_home_url();
        }
        
        if ( !isset( $contextData['itemId'] ) ) {
            throw new OPanda_SubscriptionException( __( 'Invalid request. Item ID is not set.', 'optinpanda' ) ); 
        }

        $itemId = intval( $contextData['itemId'] );
        $GLOBALS['post'] = get_post( $itemId );
        
        if ( class_exists('ReflectionObject') ) {
            
            global $wp_rewrite;
            $refObject   = new ReflectionObject( $wp_rewrite );
            $refProperty = $refObject->getProperty( 'permalink_structure' );
            $refProperty->setAccessible( true );
            $this->_permalink_structure = $refProperty->getValue( $wp_rewrite );
            $refProperty->setValue($wp_rewrite, false);
        }

        add_filter('mailster_notification_replace', array( $this, '_replace_confirmation_link'), 10, 4 );   
        add_filter('pre_option_permalink_structure', array( $this, '_disablePermalinksForConfirmationUrl'), 99 );   
        add_filter('post_type_link', array( $this, '_fixEmptyConfirmationLink'), 99, 2 );
        add_filter('page_link', array( $this, '_fixConfirmationLink'), 99, 2 );        
    }
    
    /**
     * Replaces the confirmation URL of Mailster.
     */
    public function _replace_confirmation_link ($content, $template, $subscriber, $options) {

        if ( !get_option('opanda_mailster_redirect', false ) ) return $content; 
        if ( $template !== 'confirmation' ) return $content;

        $parts = explode('?', $content['linkaddress']);
        if ( !isset($parts[1])) return $content;
        
        $args = wp_parse_args($parts[1]);

        unset($args['confirm']);
        $args['opanda_confirm'] = '';

        $url = add_query_arg($args, $this->_returnPage); 
        $content['linkaddress'] = $url;
        $content['link'] = preg_replace('/\<a\s+href=\"[^<>]*\"\>/i', '<a href="' . htmlentities($url) . '">', $content['link']);

        return $content;
    }

    /**
     * A helper method to prepare hooks after sending the confirmation email.
     * 
     * @since 1.0.7
     * @return void
     */
    private function _afterSendingConfirmation() {
        
        if ( class_exists('ReflectionObject') ) {
            
            global $wp_rewrite;
            $refObject   = new ReflectionObject( $wp_rewrite );
            $refProperty = $refObject->getProperty( 'permalink_structure' );
            $refProperty->setAccessible( true );
            $refProperty->setValue($wp_rewrite, $this->_permalink_structure);
  
        }
        
        remove_filter('pre_option_permalink_structure', array( $this, '_disablePermalinksForConfirmationUrl'), 99 );   
        remove_filter('post_type_link', array( $this, '_fixEmptyConfirmationLink'), 99, 2 );
        remove_filter('page_link', array( $this, '_fixConfirmationLink'), 99, 2 );        
    }
    
    /**
     * Mailster contains the following code which use permalinks to generate the confirmation link:
     * 
     * $link = (get_option('permalink_structure'))
     * ? trailingslashit($baselink).trailingslashit( $slug.'/'.$subscriber->hash.'/'.$form_id )
     * : add_query_arg(array(
     *      'confirm' => '',
     *      'k' => $subscriber->hash,
     *      'f' => $form_id,
     * 
     * But when we want to redirect the user back to the page where the locker is,
     * we cannot use this permalink structer (the page will return the code 404 not found), 
     * so we need to trick the Mailster and disable temporarily the permalink option.
     */
    public function _disablePermalinksForConfirmationUrl() {
         if ( !get_option('opanda_mailster_redirect', false ) ) return false; // the value will be get from the database
         return 0;         
    }

    /**
     * Replaces the confirmation link generated by Mailster if the option 'Newsletter Homepage' empty.
     * 
     * If the user is not set the option 'Newsletter Homepage', then the confirmation links leads to
     * the page admin-ajax.php, we need to fix it and return the user to the page where the locker is located.
     * 
     * @since 1.0.7
     * @return string
     */
    public function _fixEmptyConfirmationLink( $post_link, $post ) {
        if ( OPANDA_POST_TYPE === $post->post_type ) return $this->_returnPage;
        return $post_link;
    }
    
    /**
     * Replaces the confirmation link generated by Mailster if the option 'Newsletter Homepage' is not empty.
     * 
     * @since 1.0.7
     * @return string
     */
    public function _fixConfirmationLink( $post_link, $postId ) {
        if ( intval( $postId ) !== intval( mailster_option('homepage') ) ) return $post_link;
        if ( get_option('opanda_mailster_redirect', false ) ) return $this->_returnPage;
        return $post_link;
    }
    
    /**
     * Checks if the user subscribed.
     */  
    public function check( $identityData, $listId, $contextData ) { 
        
        $manager = $this->getSubscribersManager();

        $subscriber = $manager->get_by_mail( $identityData['email'] );
        if ( empty($subscriber ) ) {
            throw new OPanda_SubscriptionException( __( 'The operation is canceled because the website administrator deleted your email from the list.', 'optinpanda' ) ); 
        }
        
        $lists = $manager->get_lists( $subscriber->ID, true );
        if ( !in_array( $listId, $lists ) ) {
            $manager->assign_lists( $subscriber->ID, $listId, false );
        }
        
        $status = intval( $subscriber->status );
        if ( 1 === $status ) return array('status' => 'subscribed');
        
        if ( isset( $contextData['postUrl'] ) ) {
            $query = parse_url( $contextData['postUrl'], PHP_URL_QUERY );
            $args = wp_parse_args( $query );
            
            if ( !isset( $args['opanda_confirm'] ) ) return array('status' => 'pending');
            if ( !isset( $args['k'] ) ) return array('status' => 'pending');     
            if ( $subscriber->hash !== $args['k']  ) return array('status' => 'pending');
            
            // this code copied from frontpage.class.php (lines 203-233) of the plugin Mailster

            $ip = mailster_option('track_users') ? mailster_get_ip() : NULL;
            $user_meta = array(
                'ID' => $subscriber->ID,
                'confirm' => time(),
                'status' => 1,
                'ip_confirm' => $ip,
                'ip' => $ip,
                'lang' => mailster_get_lang(),
            );

            if( 'unknown' !== ($geo = mailster_ip2City())){
                $user_meta['geo'] = $geo->country_code.'|'.$geo->city;
                if($geo->city) $user_meta['coords'] = floatval($geo->latitude).','.floatval($geo->longitude);
            }

            if( $manager->update($user_meta) ){

                    do_action('mailster_subscriber_subscribed', $subscriber->ID);
                    do_action('mailster_subscriber_insert', $subscriber->ID);

                    return array('status' => 'subscribed');

            } else{
                throw new OPanda_SubscriptionException( __( 'Unexpected error occured.', 'optinpanda' ) );
            }
        }
        
        return array('status' => 'pending');
    }
    
    /**
     * Returns custom fields.
     */
    public function getCustomFields( $listId ) {

        try {
            
            $mappingRules = array(
                'textfield' => 'text',
                'radio' => 'dropdown'
            );
            
            $result = mailster()->get_custom_fields();

            $customFields = array();
            foreach($result as $id => $field ) {
                
                $pluginFieldType = isset( $mappingRules[$field['type']] ) 
                        ? $mappingRules[$field['type']] 
                        : strtolower( $field['type'] );

                $fieldOptions = array();
                if ( 'dropdown' === $pluginFieldType ) {

                    foreach ( $field['values'] as $choice ) {
                        $fieldOptions['choices'][] = $choice;
                    }
                    
                } elseif ( 'checkbox' === $pluginFieldType ) {
                    
                    if ( isset( $field['default'] ) && !empty(  $field['default'] ) )
                        $fieldOptions['markedByDefault'] = true;
                }
                
                $can = array(
                    'changeType' => true,
                    'changeReq' => true,
                    'changeDropdown' => false,
                    'changeMask' => true
                );
                
                $customFieldsUrl = admin_url('/options-general.php?page=newsletter-settings#subscribers');
            
                $customFields[] = array(

                    'fieldOptions' => $fieldOptions,
                    
                    'mapOptions' => array(
                        'id' => $id,
                        'name' => $id,
                        'title' => $field['name'],
                        'labelTitle' => $field['name'],
                        'mapTo' => $pluginFieldType == 'any' 
                                    ? 'any' 
                                    : is_array($pluginFieldType) ? $pluginFieldType : array( $pluginFieldType ),
                        'service' => $field
                    ),
                    
                    'premissions' => array(

                        'can' => $can,
                        'notices' => array(
                            'changeDropdown' => sprintf( __('Please visit  <a href="%s" target="_blank">this page</a> to modify the choices. After that, re-save the locker.', 'bizpanda'), $customFieldsUrl )
                        ), 
                    )
                );
            }
            
            return $customFields;
            
        } catch(Exception $ext) {
            throw new OPanda_SubscriptionException ('[custom-fields]: ' . $ext->getMessage());   
       }
    }
}
