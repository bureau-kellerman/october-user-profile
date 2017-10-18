<?php 

namespace Kellerman\UserProfile\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Lang;
use Kellerman\UserProfile\Models\Settings;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;

class UserList extends ComponentBase
{
  
    public $users;
    
    
    public function componentDetails()
    {
        return [
            'name'        => 'kellerman.userprofile::lang.user_list.name',
            'description' => 'rainlab.user::lang.account.account'
        ];
    }
    
    public function defineProperties()
    {
      return [
         
        'listGroups' => [
          'title'       => 'List Users ',
          'description' => 'Defines which groups will be listed on this page',
          'placeholder' => '*',
          'type'        => 'set',
          'default'     => [],
         ]
       ];
    }
    /**
     * Executed before AJAX handlers and before the page execution life cycle.
     */
    public function init()
    {
       // $this->addComponent('Kellerman\UserProfile\Components\UserList', 'userList', []);
    }
    
    public function getlistGroupsOptions()
    {
        return UserGroup::lists('name','code');
    }
 
    
    public function onRun()
    {
     
        $listGroups = $this->property('listGroups', []);
         
        $this->page['groups'] = UserGroup::with('users')->whereIn('code', $listGroups)->get();

     }

    private function getProfileFieldsByIndex($index = 'tab')
    { 
        $profileFields = Settings::get('profile_field');
        $default = $index === 'tab' ?
            Lang::get('kellerman.userprofile::lang.settings.field_tab_default') : 'undefined';

        if (!$profileFields || !array_key_exists($index, reset($profileFields))) {
            return [];
        }

        $profileFieldsByIndex = array_fill_keys(
            array_unique(array_filter(array_pluck($profileFields, $index))) + [$default],
            []
        );

        foreach ($profileFields as $profileField) {
            $fieldIndex = $profileField[$index] ? $profileField[$index] : $default;
            $profileFieldsByIndex[$fieldIndex][] = $profileField;
        }

        return $profileFieldsByIndex;
    }
}
