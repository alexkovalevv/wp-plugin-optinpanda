<?php

/**
 * Returns an URL where we should redirect a user after success activation of the plugin.
 * 
 * @since 3.1.0
 * @return string
 */
function optinpanda_license_manager_success_button() {
    return __('Learn how to use the plugin <i class="fa fa-lightbulb-o"></i>', 'optinpanda');
}
add_action('onp_license_manager_success_button_' . $optinpanda->pluginName, 'optinpanda_license_manager_success_button');


/**
 * Returns an URL where we should redirect a user after success activation of the plugin.
 * 
 * @since 3.1.0
 * @return string
 */
function optinpanda_license_manager_success_redirect() {
    return opanda_get_admin_url('how-to-use', array('onp_sl_page' => 'optinpanda'));
}
add_action('onp_license_manager_success_redirect_' . $optinpanda->pluginName,  'optinpanda_license_manager_success_redirect');


/**
 * The activator class performing all the required actions on activation.
 * 
 * @see Factory000_Activator
 * @since 1.0.0
 */
class OptinPanda_Activation extends Factory000_Activator {
    
    /**
     * Runs activation actions.
     * 
     * @since 1.0.0
     */
    public function activate() {   

        $this->setupLicense();
        
        if ( onp_build('free') ) {
            factory_000_set_lazy_redirect( opanda_get_admin_url('how-to-use', array('onp_sl_page' => 'usage-example-email-locker') ));
        }
    }
    
    /**
     * Setups the license.
     * 
     * @since 1.0.0
     */
    protected function setupLicense() {
        
        // sets the default licence
        // the default license is a license that is used when a license key is not activated
        
        if ( onp_build('premium') ) {
 
            $this->plugin->license->setDefaultLicense( array(
                'Category'      => 'free',
                'Build'         => 'premium',
                'Title'         => 'OnePress Zero License',
                'Description'   => __('Please, activate the plugin to get started. Enter a key 
                                    you received with the plugin into the form below.', 'optinpanda')
            ));
        }
        
        if ( onp_build('ultimate') ) {
 
            $this->plugin->license->setDefaultLicense( array(
                'Category'      => 'free',
                'Build'         => 'ultimate',
                'Title'         => 'OnePress Zero License',
                'Description'   => __('Please, activate the plugin to get started. Enter a key 
                                    you received with the plugin into the form below.', 'optinpanda')
            ));
        }
        
        if ( onp_build('free') ) {

            $this->plugin->license->setDefaultLicense( array(
                'Category'      => 'free',
                'Build'         => 'free',
                'Title'         => 'OnePress Public License',
                'Description'   => __('Public License is a GPLv2 compatible license. 
                                    It allows you to change this version of the plugin and to
                                    use the plugin free. Please remember this license 
                                    covers only free edition of the plugin. Premium versions are 
                                    distributed with other type of a license.', 'optinpanda')
            ));
        }
    }
}

$optinpanda->registerActivation('OptinPanda_Activation');