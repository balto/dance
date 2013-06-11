<?php

class ExtTurnSearch extends ExtCustomWidget
{

    private $search;
	protected function init() {}

    private function listHolidaysFieldDefinitions()
    {

        $fields = array();
        $fields[] = array(
            'header' =>'Üdülési időpont azonosító',
            'name' => 'id',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'right',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(
            ),
        );


        $fields[] = array(
            'header' =>'Kiosztás azonosító',
            'name' => 'allocation_id',
            'mapping' => '',
            'method' => '',
            'type' => '',
            'sortType' => '',
            'sortDir' => 'ASC',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'right',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 0,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' =>'Turnus típus azonosító',
            'name' => 'turn_type_id',
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
            'header' => 'Törzsszám',
            'name' => 'member_identifier',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            'width' => 60,
            'values' => array(
            ),
        );


        $fields[] = array(
            'header' => 'Tag neve',
            'name' => 'member_name',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => false,
            //'width' => 90,
            'flex' => 1,
            'values' => array(
            ),
        );


        $fields[] = array(
            'header' => 'Jogaz.',
            'name' => 'right_id',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'values' => array(
            ),
        );



        $fields[] = array(
            'header' => 'Turnus típus',
            'name' => 'turn_type_name',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 210,
            'values' => array(
            ),
        );


        $fields[] = array(
            'header' => 'Hotel',
            'name' => 'hotel_name',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 90,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Hotel ID',
            'name' => 'hotel_id',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
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
            'header' => 'Szobasz.',
            'name' => 'apart_no',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'values' => array(
            ),
        );


        $fields[] = array(
            'header' => 'Méret',
            'name' => 'apart_size_name',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 60,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Méret ID',
            'name' => 'apart_size_id',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
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
            'header' => 'Minőség',
            'name' => 'apart_category_name',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Minőség ID',
            'name' => 'apart_category_id',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
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
            'header' => 'Állapot',
            'name' => 'apart_quality_name',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 50,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Állapot ID',
            'name' => 'apart_quality_id',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
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
            'header' => 'Éj',
            'name' => 'nights',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 25,
            'values' => array(
            ),
        );


        $fields[] = array(
            'header' => 'Kiosztás',
            'name' => 'allocation_from',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 70,
            'values' => array(
            ),
        );
        $fields[] = array(
            'header' => '',
            'name' => 'allocation_to',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
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
            'header' => 'Üdülési időpont',
            'name' => 'holiday',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => 'ASC',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => '',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 90,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Üdülési időpont ID',
            'name' => 'holiday_id',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
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

        return $fields;
    }

    public function create($form, $serial='', $config = array('client' => array('width' => 590), 'grid' => array('height' => 300)))
	{
        $controller = Yii::app()->getController();
        $dlg = $this->_context;

        $dlg->createModel("TurnSearchBasic".$serial, $controller->getBasicSelectFieldDefinitions());
        $dlg->createModel("TurnSearchHoliday".$serial, $this->listHolidaysFieldDefinitions());

        $dlg->createStore("TurnSearchClient".$serial)
            ->model($dlg->model("TurnSearchBasic".$serial))
            ->autoLoad(false)
            ->proxy(Ext::Proxy()
                ->url("getClientComboList", $controller)
                ->reader(Ext::JsonReader())
            )
        ;

        $dlg->createStore("TurnSearchHoliday".$serial)
            ->model($dlg->model("TurnSearchHoliday".$serial))
            ->remoteSort(true)
            ->autoLoad(false)
            ->pageSize(10)
            ->proxy(Ext::Proxy()->url("searchTurn", $controller)
                ->reader(Ext::JsonReader())
        )
        ;

        $this->layout("form");

        $this->add(Ext::FieldContainer()
            ->layout(array('type' => 'hbox','align' => 'stretch'))
            ->fieldLabel($form->getLabel('year'.$serial))
            ->allowBlank(false)
            ->labelWidth(60)
            ->add(Ext::NumberField($form->generateName('year'.$serial))
                ->allowBlank(false)
                ->value(date('Y'))
                ->width(80)
                ->minLength(4)
                ->maxLength(4)
                //->onChange($this->search, new ExtCodeFragment('this'))
            )

            ->add(Ext::DisplayField()->value('&nbsp;')->flex(1))

            ->add(Ext::ComboBox($form->generateName('member_id'.$serial))
                ->fieldLabel($form->getLabel('member_id'.$serial))
                ->store($dlg->store('TurnSearchClient'.$serial))
                ->labelWidth(40)
                ->allowBlank(false)
                ->anchor('100%')
                ->width($config['client']['width'])
                ->hideTrigger(true)
                ->forceSelection(true)
                ->triggerAction('all')
                ->pageSize(Yii::app()->params['extjs_combo_pager_max_per_page'])
                ->minChars(3)
                ->typeAhead(true)
                ->displayField('name')
                ->valueField('id')
                //->onSelect($this->search, new ExtCodeFragment('this'))
            )
        )

        ->add(Ext::GridPanel($form->generateName('turns'.$serial))
            ->store($dlg->store("TurnSearchHoliday".$serial))
            ->height($config['grid']['height'])
            ->pageSize(10)
            ->margin('10 0 10 0')
            ->title('Turnusok')
            ->bbar(Ext::PagingToolbar())
        );

        $this->createHandlers($form, $serial);

        Ext::w($form->generateName('year'.$serial))->onChange($this->search, new ExtCodeFragment('this'));
        Ext::w($form->generateName('member_id'.$serial))->onSelect($this->search, new ExtCodeFragment('this'));

        return $this;
	}

    protected function createHandlers($form,$serial)
    {
        $this->search = new ExtFunction();
        $this->search->begin() ?>
            var me = this,
            year = Ext.getCmp('<?php echo Ext::w($form->generateName('year'.$serial))->id; ?>').getValue(),
            member_id = Ext.getCmp('<?php echo Ext::w($form->generateName('member_id'.$serial))->id; ?>').getValue(),
            holidayStore = this.stores.get('TurnSearchHoliday<?php echo $serial; ?>');

            if (year && member_id) {
            holidayStore.proxy.extraParams = {by_member: 1, by_member_year: year, client_id: member_id};
            holidayStore.load();
            } else {
            holidayStore.removeAll();
            }

    <?php $this->search->end();

    }
}