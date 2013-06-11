// <script type="text/javascript">

<?php

// module definition ______________________________________

$statusbar = new ExtModule($this, array(
	"cacheable" => false,
	"extend" => "Ext.toolbar.Toolbar"
));


// function declarations __________________________________

//$onClickButtonLogout = $statusbar->createMethod("logout()");
$statusbar->createMethod("onClickButtonLogout()");


// view ___________________________________________________

$statusbar->add(Ext::ToolbarFill());
$statusbar->add(Ext::ToolbarTextItem()->text(Yii::t('msg','Bejelentkezve, mint')));
$statusbar->add(Ext::Button('buttonProfile')
	->iconCls('icon-user-go')
	->text($user_name)
	->componentCls('bold-button')
	->pressed(false)
	->enableToggle(false)
	->handler(new ExtFunction("theApp.showDialog('/user/profile', {userId: ".Yii::app()->user->id."});"))
);
$statusbar->add(Ext::ToolbarSeparator());
$statusbar->add(Ext::ToolbarTextItem()->text($user_groups));
$statusbar->add(Ext::ToolbarSeparator());
$statusbar->add(Ext::Button('buttonLogout')
	->iconCls('icon-door-in')
	->text(Yii::t('msg','Kijelentkezés'))
	->pressed(false)
	->enableToggle(false)
	->handler($statusbar->onClickButtonLogout)
);


// function implementation ________________________________

$statusbar->onClickButtonLogout->begin()?>
	if (theApp.currentModule != null && theApp.currentModule.changed == true) {
			Ext.Msg.show({
				title: '<?php echo Yii::t('msg', "Kilépés megerősítése") ?>',
				msg: '<?php Yii::t('msg', "Nem mentett adatok vannak, biztosan kilép?") ?>',
				buttons: Ext.Msg.YESNO,
				fn: function(buttonId) {
					if (buttonId == 'yes') {
						Ext.getCmp('<?php echo Ext::w('buttonLogout')->id ?>').setDisabled(true);
						theApp.doLogout();
					}
				},
				animateTarget: Ext.getBody(),
				icon: Ext.window.MessageBox.QUESTION
			});
		} 
		else {
			Ext.getCmp('<?php echo Ext::w('buttonLogout')->id ?>').setDisabled(true);
			theApp.doLogout();
		} <?php 
$statusbar->onClickButtonLogout->end();


// template methods _______________________________________

$statusbar->beginMethod("initComponent()") ?>
	this.callParent();
	theApp.viewport.items.get('statusbar').items = this.items;
	theApp.viewport.doLayout();
	
<?php $statusbar->endMethod();


$statusbar->render();
