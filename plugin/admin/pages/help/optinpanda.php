<div class="onp-help-section">
    <h1><?php _e('Opt-In Panda', 'optinpanda'); ?></h1>

    <p><?php _e('Opt-In Panda is a lead-generation plugin created to help you effectively attract new subscribers and easily build huge email lists.', 'optinpanda') ?></p>
    
    <p><?php _e('The plugin provides you with the following tools:', 'optinpanda') ?></p>
    
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Name', 'optinpanda') ?></th>
                <th style="padding-left: 20px;"><?php _e('Description', 'optinpanda') ?></th>
            </tr>
        </thead>
        <tr>
            <td style="white-space: nowrap">
                <a href="<?php $manager->actionUrl('index', array('onp_sl_page' => 'what-is-email-locker')) ?>" class="onp-bold-link"><?php _e('Email Locker', 'optinpanda') ?></a>
            </td>
            <td style="padding-left: 20px;">
                <?php _e('Designed to gather opt-ins. The user has to opt-in to unlock some value content (recommended to use for new users).', 'optinpanda') ?>
            </td>
        </tr>
        
        <?php if ( !onp_build('free') ) { ?>
        
        <tr>
            <td style="white-space: nowrap">
                <a href="<?php $manager->actionUrl('index', array('onp_sl_page' => 'what-is-signin-locker')) ?>" class="onp-bold-link"><?php _e('Sign-In Locker', 'optinpanda') ?></a>
            </td>
            <td style="padding-left: 20px;">
                <?php _e('More advanced version of the Email Locker. Uses social buttons to retrieve emails and at the same time perform some extra social actions (e.g, tweeting).', 'optinpanda') ?>
            </td>
        </tr>
        
        <?php } ?>
    </table>
    
    <?php if ( !onp_build('free') ) { ?>
    
    <p><?php _e('The Email Locker usually has a better conversion than the Sign-In Locker.', 'optinpanda') ?></p>
    
    <?php } else { ?>
    
    <p><?php _e('Click on the tool title to learn more.', 'optinpanda') ?></p>
    
    <?php } ?>
</div>