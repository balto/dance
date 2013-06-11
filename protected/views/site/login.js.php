// <script type="text/javascript">
<?php

$dlg = new ExtDialog($this, array(
	"title" => "Felhasználó azonosítás",
	"cacheable" => false,
));

$dlg->createMethod("doLogin()");

$dlg->window->width(300)->height(130)->bodyStyle('padding:5px');
$dlg->window->buttons(array(
	Ext::Button()->text('Belépés')->handler($dlg->doLogin)->scope(new ExtCodeFragment("this"))
));

$dlg->add(Ext::Form("LoginForm")
	->labelWidth(100)
	->url(ExtProxy::createUrl("login", $this))
	->baseCls('x-plain')
	->frame(false)
	->plain(true)
	->defaults(array(
		'labelWidth'=> 120,
		'width' => '50px',
	))
);

$dlg->LoginForm
	->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
		->value($form->generateCsrfToken())
	)
	->add(Ext::TextField($form->generateName('loginname'))
		->fieldLabel('Felhasználónév')
		->validateOnBlur(false)
		->allowBlank(false)
		->blankText('A felhasználónév mező kitöltése kötelező')
	)
	->add(Ext::TextField($form->generateName('password'))
		->fieldLabel('Jelszó')
		->allowBlank(false)
		->inputType('password')
		->validateOnBlur(false)
		->blankText('A jelszó mező kitöltése kötelező')
	)
	->add(Ext::DisplayField('new_password_instruction')
		->value('Jelszó módosítás')
		->hideLabel(true)
		->hidden(true)
		->fieldStyle('margin:10px 0 0; color:#15428B; font-size:12px; font-weight:bold; border-bottom:1px solid gray;')
	)
	->add(Ext::TextField($form->generateName('new_password'))
		->fieldLabel($form->getLabel('new_password')) //'Új jelszó',
		->inputType('password')
		->value('')
		->hidden(true)
	)
	->add(Ext::TextField($form->generateName('new_password_again')) //'LoginForm[new_password_again]',
		->fieldLabel($form->getLabel('new_password_again')) //'Új jelszó ismét',
		->inputType('password')
		->value('')
		->hidden(true)
	)
;

$dlg->beginMethod("initDialog(onRender)") ?>
	
	if (onRender) {
		var loginname_field = Ext.getCmp('<?php echo Ext::w($form->generateName('loginname'))->id ?>');
		var password_field = Ext.getCmp('<?php echo Ext::w($form->generateName('password'))->id ?>');
		var new_password_field = Ext.getCmp('<?php echo Ext::w($form->generateName('new_password'))->id ?>');
		var new_password_again_field = Ext.getCmp('<?php echo Ext::w($form->generateName('new_password_again'))->id ?>');


		new Ext.KeyMap(loginname_field.getEl(), {
			key: Ext.EventObject.ENTER,
			fn: function(){password_field.focus();}
		});

		new Ext.KeyMap(password_field.getEl(), {
			key: Ext.EventObject.ENTER,
			fn: this.doLogin,
			scope: this
		});

		new Ext.KeyMap(new_password_field.getEl(), {
			key: Ext.EventObject.ENTER,
			fn: function(){new_password_again_field.focus();}
		});

		new Ext.KeyMap(new_password_again_field.getEl(), {
			key: Ext.EventObject.ENTER,
			fn: this.doLogin,
			scope: this
		});
	}

	this.setNewPasswordFieldsVisible(false);
	this.focusUsername();
	
	this.callParent(arguments);	<?php $dlg->endMethod();


$dlg->beginMethod("success(f, a)") ?>
	if (a && a.result.success==true) {
		if (typeof a.result.need_new_password == 'undefined') {
			this.window.close();
			theApp.doLogin();
		}
		else {
			this.setNewPasswordFieldsVisible(true);
		}
	} else {
		// ebbe az ágba tán be sem jövünk, de biztos, ami biztos
		this.resetForm();
		Ext.Msg.alert(
			'Felhasználó azonosítás',
			'Sikertelen bejelentkezés. '+a.result.error.message,
			function() {
				this.window.close();
				theApp.showLoginWindow();
			}
		);
	} <?php $dlg->endMethod();


$dlg->beginMethod("failure(f, a)") ?>
	var me = this;
	
	switch (a.failureType) {
		case Ext.form.Action.CLIENT_INVALID:
			Ext.Msg.alert(
				'Felhasználó azonosítás',
				'Az űrlap kitöltése hibás, kérem ellenőrizze!',
				function() {
					me.window.close()
					theApp.showLoginDialog();
				}
			);
			break;
			
		case Ext.form.Action.CONNECT_FAILURE:
			if (typeof a.response != 'undefined') {
				theApp.handleFailure(a.response, null);
			} else {
				Ext.Msg.alert(
					'Felhasználó azonosítás',
					'Hiba a kapcsolatban, kérjük próbálja meg ismét.',
					function() {
						me.window.close();
						theApp.showLoginDialog();
					}
				);
			}
			break;
			
		case Ext.form.Action.SERVER_INVALID:
			// új jelszó megadásakor keletkező validálási hiba esetén jövünk ebbe az ágba
			theApp.handleFormFailure(f,a, function() {
				me.window.close();
				theApp.showLoginDialog();
			});
			break;
			
	default:
	// ha nem jó a username vagy jelszó, akkor jövünk ebbe az ágba
		this.resetForm();
		Ext.Msg.alert(
			'Felhasználó azonosítás',
			'Sikertelen bejelentkezés. Felhasználó név vagy jelszó nem megfelelő.',
			function() {
				me.window.close();
				theApp.showLoginWindow();
			}
		);
	} <?php $dlg->endMethod();
	

$dlg->beginMethod("setNewPasswordFieldsVisible(visible)") ?>

	var pwd = Ext.getCmp('<?php echo Ext::w($form->generateName('new_password'))->id ?>');
	var pwdAgain = Ext.getCmp('<?php echo Ext::w($form->generateName('new_password_again'))->id ?>');
	
	pwd.setVisible(visible);
	pwdAgain.setVisible(visible);

	if (visible) {
		this.window.setHeight(260);
		pwd.focus();
		pwd.validator = this.passwordValidator;
		pwdAgain.validator = this.passwordValidator;
	} <?php $dlg->endMethod();

$dlg->createMethod('focusUsername()', "Ext.getCmp('".Ext::w($form->generateName('loginname'))->id."').focus(false, 100);");

$dlg->createMethod("resetForm()", "Ext.getCmp('".$dlg->LoginForm->id."').getForm().reset();");

$dlg->beginMethod("passwordValidator(value)") ?>
	if (/^<?php echo Yii::app()->params['password_validator_pattern'] ?>$/.test(value)) {
		return true
	}
	else {
		return 'A megadott jelszó nem megfelelő. Kérem használjon erős jelszavakat!<br /><?php echo Yii::app()->params['password_validator_msg'] ?>';
	} <?php $dlg->endMethod();
	
$dlg->beginMethod("doLogin()") ?>
	Ext.getCmp('<?php echo $dlg->LoginForm->id ?>').getForm().submit({
		waitTitle: 'Kérjük váron...',
		waitMsg: 'Azonosítás folyamatban...',
		method: 'POST',
		clientValidation: true,
		success: this.success,
		failure: this.failure,
		scope: this
	}); <?php $dlg->endMethod();
				
	
$dlg->render();