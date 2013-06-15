<?php

return array(
/*	array(
        'module' => true,
        'text' => Yii::t('msg', 'Teszt'),
        'iconCls' => 'icon-user',
        'url' => 'campaign/campaign/test',
    ),
*/
    array(
        'module' => true,
        'text' => Yii::t('msg', 'Jelenléti ív'),
        'iconCls' => 'icon-history',
        'url' => 'interface/attendanceSheet/index',
    ),
    
	array(
        'module' => true,
        'text' => Yii::t('msg', 'Tanítványok'),
        'iconCls' => 'icon-user',
        'url' => 'member/member/index',
    ),
    
	array(
        'module' => true,
        'text' => Yii::t('msg', 'Kampányok'),
        'iconCls' => 'icon-extension',
        'url' => 'campaign/campaign/index',
    ),

    array(
        'module' => false,
        'text' => Yii::t('msg', 'Felhasználók'),
        'iconCls' => 'icon-group',
        'menu' => array(
            array(
                'module' => true,
                'text' => Yii::t('msg', 'Felhasználók'),
                'iconCls' => 'icon-user',
                'url' => 'user/user/index',
            ),
            array(
                'module' => true,
                'text' => Yii::t('msg', 'Felhasználó csoportok'),
                'iconCls' => 'icon-group',
                'url' => 'user/userGroup/index',
            ),
            array(
                'module' => true,
                'text' => Yii::t('msg', 'Jogosultságok'),
                'iconCls' => 'icon-shield',
                'url' => 'user/permission/index',
            ),
        ),
    ),

    array(
        'module' => false,
        'text' => Yii::t('msg', 'Törzsadatok'),
        'iconCls' => 'icon-table',
        'menu' => array(
        	array(
        			'module' => true,
        			'text' => Yii::t('msg', 'Kampány kategóriák'),
        			'iconCls' => 'icon-layout',
        			'url' => 'codetable/codetable/index?model_name=DanceType',
        			'params' => array('model_name' => 'DanceType', 'model_label' => 'Kampány kategóriák'),
        	),
        	array(
        			'module' => true,
        			'text' => Yii::t('msg', 'Kampány típusok'),
        			'iconCls' => 'icon-layout-content',
        			'url' => 'campaign/campaignType/index',
        	),
        	array(
        			'module' => true,
        			'text' => Yii::t('msg', 'Bérlet típusok'),
        			'iconCls' => 'icon-report',
        			'url' => 'ticket/ticketType/index',
        	),	
        	array(
        			'module' => true,
        			'text' => Yii::t('msg', 'Helyek'),
        			'iconCls' => 'icon-house',
        			'url' => 'location/location/index',
        	),
        		
        )
    ),
	
	array(
			'module' => false,
			'text' => Yii::t('msg', 'Develop'),
			'iconCls' => 'icon-history',
			'menu' => array(
					array(
							'module' => true,
							'text' => Yii::t('msg', 'Empty tickets'),
							'iconCls' => 'icon-history',
							'url' => 'interface/attendanceSheet/emptyTickets',
					),
					array(
							'module' => true,
							'text' => Yii::t('msg', 'Empty campaigns and tickets'),
							'iconCls' => 'icon-history',
							'url' => 'interface/attendanceSheet/emptyCampaignsAndTickets',
					),
					array(
							'module' => true,
							'text' => Yii::t('msg', 'Empty all'),
							'iconCls' => 'icon-history',
							'url' => 'interface/attendanceSheet/emptyAll',
					),
					
	
			)
	),
);
