<?php 

namespace Kellerman\UserProfile;

use October\Rain\Parse\Yaml;
use October\Rain\Support\Facades\Schema;
use Illuminate\Support\Facades\Lang;
use October\Rain\Support\Facades\File;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;
use Kellerman\UserProfile\Models\Settings;

/**
 * UserProfile Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['RainLab.User'];

    static private $inputTypeMapping = [
        'number' => 'integer',
        'text' => 'string',
        'password' => 'string',
        'textarea' => 'text'
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'kellerman.userprofile::lang.plugin.name',
            'description' => 'kellerman .userprofile::lang.plugin.description',
            'author'      => 'Milan Kubin',
            'icon'        => 'icon-user-plus',  
            'homepage'    => 'https://github.com/bureau-kellerma/oc-userprofile-plugin'
        ];
    }

    public function registerSettings()
    {
        
        return [
            'settings' => [
                'label'       => 'kellerman.userprofile::lang.settings.menu_label',
                'description' => 'kellerman.userprofile::lang.settings.menu_description',
                'category'    => SettingsManager::CATEGORY_USERS,
                'icon'        => 'icon-user-plus',
                'class'       => 'Kellerman\UserProfile\Models\Settings',
                'order'       => 500,
                'permissions' => ['rainlab.users.settings']
            ]
        ];
        
    }

    public function registerComponents()
    {
        return [
            'Kellerman\UserProfile\Components\Account'       => 'account',
            'Kellerman\UserProfile\Components\MenuUserWidget'       => 'menuUserWidget',
            'Kellerman\UserProfile\Components\UserList'       => 'userList',
        ];
    }

    /**
     * Register new Twig variables
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'functions' => [
                '_' => function($messageId, $domain = 'kellerman.userprofile::lang.messages') {
                    return Lang::get("$domain.$messageId");
                }
            ]
        ];
    }

    public function boot()
    {
        $profileFields = Settings::get('profile_field');

        if (!$profileFields) {
            return;
        }

        $profileFieldsNames = array_pluck($profileFields, 'name');
        $profileFields = array_combine($profileFieldsNames, $profileFields);

        UserModel::extend(function($model) use ($profileFieldsNames) {
            $model->addFillable($profileFieldsNames);
        });

        UsersController::extendFormFields(function($widget) use ($profileFields) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof UserModel) {
                return;
            }

            $widget->addTabFields($profileFields);
        });

        if (Schema::hasColumns('users', $profileFieldsNames)) {
            return;
        }

        Schema::table('users', function($table) use ($profileFields) {
            foreach ($profileFields as $profileField) {
                // Security check
                if (!preg_match('/^[a-zA-Z_]+$/', $profileField['name'])) {
                    continue;
                } 
                if (!Schema::hasColumn('users', $profileField['name'])) {
                    $method = static::$inputTypeMapping[$profileField['type']];
                    $table->$method($profileField['name'])->nullable();
                }
            }
        });

    }

}
