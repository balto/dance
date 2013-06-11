// <script type="text/javascript">
<?php

// module definition ______________________________________

$dlg = new ExtDialog($this, array(
    "cacheable" => false,
    "title" => 'Kiléptetés',
    'layout' => 'fit',
));

    $csrf_token = $form->generateCsrfToken();
// functions, event handlers (declarations) ________________

    $dlg->createMethod("logout()");
// models and stores ______________________________________

$dlg->createModel("Basic", $this->getBasicSelectFieldDefinitions());

// store name => array(model name, autoload)

$dlg->createStore('BasicStore')
    ->model($dlg->model('Basic'))
    ->autoLoad(true)
    ->remoteSort(false)
    ->pageSize($combo_max_per_page) // nem lehet megadni, hogy ne küldje a store a page paramétereket
    ->proxy(Ext::Proxy()->url('member/member/getComboList', $this)
        ->reader(Ext::JsonReader())
        ->extraParams(array('model' => 'MemberLeaveReason'))
);


// view  __________________________________________________




$dlg->window->width(300)->height(200);
$dlg->add(Ext::Form("MemberLeaveForm")
        ->bodyPadding(10)
        ->add(Ext::Hidden($form->generateName($form->getCSRFFieldname()))
            ->value($csrf_token)
        )
        ->add(Ext::Hidden($form->generateName('id'))
            ->name($form->generateName('id'))
            ->value('')
        )
        ->add(Ext::DisplayField('customerServiceOk')
            ->fieldLabel('Ügyfélszolgálat')
            ->value(($customerServiceIsOk?'rendben':'nyitott feladatok'))
        )
        ->add(Ext::DisplayField('financeOk')
            ->fieldLabel('Pénzügy')
            ->value(($financeOk?'rendben':'rendezetlen pénzügy'))
        )
        ->add(Ext::ComboBox($form->generateName('member_leave_reason_id'))
            ->fieldLabel($form->getLabel('member_leave_reason_id'))
            ->store($dlg->store('BasicStore'))
            ->displayField('name')
            ->hidden(true)
            ->valueField('id')
            ->allowBlank(false)
            ->forceSelection(true)
            ->anchor("100%")
        )
        ->add(Ext::DateField($form->generateName('member_leave_at'))
            ->fieldLabel($form->getLabel('member_leave_at'))
            ->allowBlank(false)
            ->value('')
            ->anchor("100%")
        )
)
;
$dlg->window->buttons(array(
    Ext::Button('logout_button')->hidden(true)->text('Kiléptetés')->handler($dlg->logout),
    Ext::Button()->text('Bezár')->handler(new ExtFunction("this.window.close()"))->scope(new ExtCodeFragment("this")),
));

// template methods _______________________________________
    $dlg->logout->begin()?>
    var me = this;
    var form = Ext.getCmp('<?php echo $dlg->MemberLeaveForm->id; ?>');

    form.getForm().submit({
        clientValidation: true,
        submitEmptyText: false,
        url: '<?php echo ExtProxy::createUrl('doLogout', $this) ?>',
        success: function(form, action) {
            me.changed = false;
            me.window.close();

            // ertesites az adatok megvaltozasarol
            //me.parentWindow.fireEvent('memberdatachanged', me.params.id);
        },
        failure: theApp.handleFormSubmitFailure,
        waitTitle: MESSAGES.SAVE_WAIT_TITLE,
        waitMsg: MESSAGES.SAVE_WAIT_MESSAGE
    });
    <?php $dlg->logout->end();

$dlg->beginMethod("initDialog()") ?>

if (this.params.id) {
    var customerServiceIsOk = <?php echo ($customerServiceIsOk)?1:0;?>;
    var financeOk = <?php echo ($financeOk)?1:0;?>;

    Ext.getCmp('<?php echo Ext::w($form->generateName('id'))->id; ?>').setValue(this.params.id);

    if(customerServiceIsOk && financeOk){
        var reason_combo = Ext.getCmp('<?php echo Ext::w($form->generateName('member_leave_reason_id'))->id; ?>');
        reason_combo.setVisible(true);
        Ext.getCmp('logout_button').setVisible(true);
    }
}

this.callParent(arguments);
return true;
<?php $dlg->endMethod();

$dlg->render();
