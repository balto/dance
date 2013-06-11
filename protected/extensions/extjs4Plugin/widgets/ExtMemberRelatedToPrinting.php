<?php

class ExtMemberRelatedToPrinting extends ExtCustomWidget
{
    private $selectMember;
    private $emptyGrid;
	protected function init() {}

    public function listMemberNotificationFieldDefinitions() {
        $fields = array();

        $fields[] = array(
            'header' =>'Személy azonosító',
            'name' => 'id',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' =>'Név',
            'name' => 'notify_name',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'flex' => 1,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' =>'Cím',
            'name' => 'notify_address',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 200,
            'values' => array(
            ),
        );

        return $fields;
    }

    public function create($serial='', $config = array('client' => array('width' => 590, 'required' => true), 'grid' => array('required' => true,'height' => 300)))
	{
        $controller = Yii::app()->getController();
        $dlg = $this->_context;

        $dlg->createModel("MemberRelatedToPrintingBasic".$serial, $controller->getBasicSelectFieldDefinitions());
        $dlg->createModel("MemberNotification".$serial, $this->listMemberNotificationFieldDefinitions());

        $dlg->createStore("MemberRelatedToPrintingClient".$serial)
            ->model($dlg->model("MemberRelatedToPrintingBasic".$serial))
            ->autoLoad(false)
            ->proxy(Ext::Proxy()
                ->extraParams(array('members_only' => 1))
                ->url("getClientComboList", $controller)
                ->reader(Ext::JsonReader())
            )
        ;

        $dlg->createStore("EnvelopeType".$serial)
            ->model($dlg->model("MemberRelatedToPrintingBasic".$serial))
            ->autoLoad(false)
            ->remoteSort(false)
            ->data(array(
            array('id' => 0, 'name' => 'A/4-es méretű'),
            array('id' => 1, 'name' => 'Közepes'),
            array('id' => 2, 'name' => 'Csekk méretű'),
        ))
        ;

        $dlg->createStore("MemberNotification".$serial)
            ->model($dlg->model("MemberNotification".$serial))
            ->autoLoad(false)
            ->remoteSort(true)
            ->pageSize(Yii::app()->params['extjs_combo_pager_max_per_page']) // nem lehet megadni, hogy ne küldje a store a page paramétereket
            ->proxy(Ext::Proxy()->url("letter/member/getMemberNotificationList", $controller)
                ->reader(Ext::JsonReader())
        )
        ;

        $this->layout("form");

        $this->add(
            Ext::ComboBox('member_id'.$serial)
                ->fieldLabel('Tag')
                ->store($dlg->store('MemberRelatedToPrintingClient'.$serial))
                ->allowBlank(!$config['client']['required'])
                ->anchor('100%')
                ->width($config['client']['width'])
                ->hideTrigger(true)
                ->forceSelection(($config['client']['required'])?true:false)
                ->triggerAction('all')
                ->pageSize(Yii::app()->params['extjs_combo_pager_max_per_page'])
                ->minChars(3)
                ->emptyText(($config['client']['required'])?'':'összes tag')
                ->typeAhead(true)
                ->displayField('name')
                ->valueField('id')
        )
        ->add(Ext::GridPanel('memberNotification'.$serial)
            ->store($dlg->store("MemberNotification".$serial))
            ->height(150)
            ->title(($config['grid']['required'])?'Címzett<span class="required-flag" data-qtip="Kötelező választani">*</span>':'Címzett')
            ->bbar(Ext::PagingToolbar())
        )
        ->add(Ext::RadioGroup()
            ->columns(2)
            ->fieldLabel('Formátum')
            ->vertical(true)
            ->allowBlank(false)
            ->width(400)
            ->add(Ext::Radio()
                ->name('format'.$serial)
                ->boxLabel('Levél/nyomtatvány')
                ->inputValue('Letter')
                ->height(20)
                ->checked(true)
            )
            ->add(Ext::Radio()
                ->name('format'.$serial)
                ->boxLabel('Boríték')
                ->inputValue('Envelope')
                ->height(20)
            )
            ->add(Ext::Radio()
                ->name('format'.$serial)
                ->boxLabel('Etikett címke')
                ->inputValue('Etiquette')
                ->height(20)
            )
            ->add(Ext::DisplayField()->value('&nbsp;'))
            ->add(Ext::ComboBox('envelopeType'.$serial)
                ->store($dlg->store("EnvelopeType".$serial))
                ->allowBlank(true)
                ->forceSelection(true)
                ->queryMode('local')
                ->displayField('name')
                ->valueField('id')
                ->emptyText('Boríték típusa')
            )
            ->add(Ext::FieldContainer()
                ->layout('hbox')
                ->width('100%')
                ->add(Ext::NumberField('etiquetteRow')
                    ->emptyText('sor')
                    ->width(60)
                )
                ->add(Ext::DisplayField()
                    ->value('&nbsp;x&nbsp;')
                )
                ->add(Ext::NumberField('etiquetteColumn')
                    ->emptyText('oszlop')
                    ->width(60)
                )
            )
        );

        $this->createHandlers($serial);

        if(!$config['client']['required']){
            Ext::w('member_id'.$serial)->onBlur($this->emptyGrid, new ExtCodeFragment('this'));
        }

        Ext::w('member_id'.$serial)->onSelect($this->selectMember, new ExtCodeFragment('this'));

        return $this;
	}

    protected function createHandlers($serial)
    {

        $this->selectMember = new ExtFunction();
        $this->selectMember->begin() ?>
            var member_id = Ext.getCmp('<?php echo Ext::w('member_id'.$serial)->id;?>').getValue(),
            member_notification_store = Ext.getCmp('<?php echo Ext::w('memberNotification'.$serial)->id;?>').getStore();

            member_notification_store.proxy.extraParams = {member_id: member_id};
            member_notification_store.load({
            params:{
            start: 0,
            limit: <? echo Yii::app()->params['extjs_pager_max_per_page'] ?>
            },
            scope: this,
            callback: function(records, operation, success) {
            // the operation object
            // contains all of the details of the load operation
            if(!success) {
            Ext.getCmp('<?php echo Ext::w('memberNotification'.$serial)->id;?>').getStore().removeAll();
            }
            }
            });


        <?php $this->selectMember->end();


        $this->emptyGrid = new ExtFunction();
        $this->emptyGrid->begin() ?>
            var member_field = Ext.getCmp('<?php echo Ext::w('member_id'.$serial)->id;?>');

            if(member_field.getRawValue() == ''){
                Ext.getCmp('<?php echo Ext::w('memberNotification'.$serial)->id;?>').getStore().removeAll();

            }
        <?php $this->emptyGrid->end();
    }
}