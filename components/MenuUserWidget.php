<?php namespace Kellerman\UserProfile\Components;

use RainLab\User\Components\Account as UserAccountComponent;
use Exception;

class MenuUserWidget extends UserAccountComponent
{
    public function componentDetails()
    {
        return [
            'name'        => 'kellerman.userprofile::lang.menu_user_widget.menu_user_widget',
            'description' => 'kellerman.userprofile::lang.menu_user_widget.menu_user_widget_desc'
        ];
    }

    public function defineProperties()
    {
        return array_merge(
            parent::defineProperties(), [
            'accountPage' => [
                'title'       => 'kellerman.userprofile::lang.menu_user_widget.account_page',
                'description' => 'kellerman.userprofile::lang.menu_user_widget.account_page_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'forgotPasswordPage' => [
                'title'       => 'kellerman.userprofile::lang.menu_user_widget.forgot_password_page',
                'description' => 'kellerman.userprofile::lang.menu_user_widget.forgot_password_page_desc',
                'type'        => 'dropdown',
                'default'     => ''
            ]
        ]);
    }
 
    public function getAccountPageOptions()
    {
        return parent::getRedirectOptions();
    }
    
    public function getForgotPasswordPageOptions()
    {
        return parent::getRedirectOptions();
    }
    
    /**
     * Executed before AJAX handlers and before the page execution life cycle.
     */
    public function init()
    {
        $this->addComponent('RainLab\User\Components\Session', 'session', []);

        return parent::init();
    }

    public function onRun()
    {
        // expose properties to be used in the page
        foreach ($this->defineProperties() as $propName => $prop) {
            $this->page[$propName] = $this->property($propName);
        }

        return parent::onRun();
    }
}
