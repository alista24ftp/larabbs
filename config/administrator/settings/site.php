<?php

return [
    'title' => '站点设置',

    // Check permissions
    'permission' => function()
    {
        // Only allow Founder to manage settings
        return Auth::user()->hasRole('Founder');
    },

    // Form inputs for settings
    'edit_fields' => [
        'site_name' => [
            // Input title
            'title' => '站点名称',

            // Input type
            'type' => 'text',

            // Input max length chars
            'limit' => 50,
        ],

        'contact_email' => [
            'title' => '联系人邮箱',
            'type' => 'text',
            'limit' => 50,
        ],

        'seo_description' => [
            'title' => 'SEO - Description',
            'type' => 'textarea',
            'limit' => 250,
        ],

        'seo_keyword' => [
            'title' => 'SEO - Keywords',
            'type' => 'textarea',
            'limit' => 250,
        ],
    ],

    // Validation rules for form inputs
    'rules' => [
        'site_name' => 'required|max:50',
        'contact_email' => 'email',
    ],

    'messages' => [
        'site_name.required' => '请填写站点名称。',
        'contact_email.email' => '请填写正确的联系人邮箱格式。',
    ],

    // Hook used right before data is saved, allows editing data before submitting
    'before_save' => function(&$data)
    {
        // Add suffix to site name to prevent multiple submissions
        if(strpos($data['site_name'], 'Powered by LaraBBS') === false){
            $data['site_name'] .= ' - Powered by LaraBBS';
        }
    },

    // can have multiple custom actions,
    // each of which is inside 'other actions' block at bottom of page
    'actions' => [
        // clear cache
        'clear_cache' => [
            'title' => '更新系统缓存',

            // messages shown based on result
            'messages' => [
                'active' => '正在清空缓存...',
                'success' => '缓存已清空！',
                'error' => '清空缓存时出错！',
            ],

            // action to execute code
            // can use $data param to edit settings data
            'action' => function(&$data)
            {
                \Artisan::call('cache:clear');
                return true;
            }
        ],
    ],
];
