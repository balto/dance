<?php

class ExtRightSearch extends ExtCustomWidget
{

    private $search;
	protected function init() {}

    private function listRightsFieldDefinitions()
    {

        $fields = array();
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
            'header' => 'Jogazonosító',
            'name' => 'right_id',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'right',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 80,
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
            //'width' => 270,
            'flex' => 1,
            'values' => array(
            ),
        );


        $fields[] = array(
            'header' => 'Apartman méret',
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
            'width' => 100,
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
            'header' => 'Éjszakák száma',
            'name' => 'nights',
            'mapping' => '',
            'method' => '',
            'type' => 'string',
            'sortType' => '',
            'sortDir' => '',
            'dateFormat' => '',
            'defaultValue' => '',
            'resizable' => '',
            'align' => 'right',
            'renderer' => '',
            'groupable' => false,
            'gridColumn' => true,
            'width' => 90,
            'values' => array(
            ),
        );

        $fields[] = array(
            'header' => 'Üdülési idő',
            'name' => 'holiday',
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
            'width' => 140,
            'values' => array(
            ),
        );

        return $fields;
    }

    public function create($form, $config = array('client' => array('width' => 590), 'grid' => array('height' => 300), 'facultative_only' => false))
	{
        $controller = Yii::app()->getController();
        $dlg = $this->_context;

        $dlg->createModel("RightSearchBasic", $controller->getBasicSelectFieldDefinitions());
        $dlg->createModel("RightSearchRight", $this->listRightsFieldDefinitions());

        $dlg->createStore("RightSearchClient")
            ->model($dlg->model("RightSearchBasic"))
            ->autoLoad(false)
            ->proxy(Ext::Proxy()
                ->url("getClientComboList", $controller)
                ->reader(Ext::JsonReader())
            )
        ;

        $dlg->createStore("RightSearchRight")
            ->model($dlg->model("RightSearchRight"))
            ->remoteSort(true)
            ->autoLoad(false)
            ->pageSize(10)
            ->proxy(Ext::Proxy()
                ->url("searchRight", $controller)
                ->extraParams(array('facultative_only' => $config['facultative_only'], 'year' => 0, 'client_id' => 0))
                ->reader(Ext::JsonReader())
            )
        ;

        $this->layout("form");

        $this->add(Ext::FieldContainer()
            ->layout(array('type' => 'hbox','align' => 'stretch'))
            ->fieldLabel($form->getLabel('year'))
            ->allowBlank(false)
            ->labelWidth(60)
            ->add(Ext::NumberField($form->generateName('year'))
                ->allowBlank(false)
                ->value(date('Y'))
                ->width(80)
                ->minLength(4)
                ->maxLength(4)
                //->onChange($this->search, new ExtCodeFragment('this'))
            )

            ->add(Ext::DisplayField()->value('&nbsp;')->flex(1))

            ->add(Ext::ComboBox($form->generateName('member_id'))
                ->fieldLabel($form->getLabel('member_id'))
                ->store($dlg->store('RightSearchClient'))
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

        ->add(Ext::GridPanel($form->generateName('turns'))
            ->store($dlg->store("RightSearchRight"))
            ->height($config['grid']['height'])
            ->pageSize(10)
            ->title($config['facultative_only'] ? 'Fakultatív jogok' : 'Jogok')
            ->bbar(Ext::PagingToolbar())
        );

        $this->createHandlers($form);

        Ext::w($form->generateName('year'))->onChange($this->search, new ExtCodeFragment('this'));
        Ext::w($form->generateName('member_id'))->onSelect($this->search, new ExtCodeFragment('this'));
        if ($config['facultative_only']) {
            Ext::w($form->generateName('turns'))->onBeforeSelect($this->disableSelectOnHolidaySet, new ExtCodeFragment('this'));
        }

        return $this;
	}

    protected function createHandlers($form)
    {
        $this->search = new ExtFunction();
        $this->search->begin() ?>
            var me = this,
                year = Ext.getCmp('<?php echo Ext::w($form->generateName('year'))->id; ?>').getValue(),
                member_id = Ext.getCmp('<?php echo Ext::w($form->generateName('member_id'))->id; ?>').getValue(),
                rightStore = this.stores.get('RightSearchRight');

            if (year && member_id) {
                rightStore.proxy.extraParams.year = year;
                rightStore.proxy.extraParams.client_id = member_id;
                rightStore.load();
            } else {
                rightStore.removeAll();
            }

        <?php $this->search->end();


        $this->disableSelectOnHolidaySet = new ExtFunction(array('grid', 'record', 'index'));
        $this->disableSelectOnHolidaySet->begin() ?>
            if (record.get('holiday') != '') {
                Ext.example.msg('Hiba', 'Válasszon olyan jogot, amelyhez még nincs kiosztás!');
                return false;
            }
        <?php $this->disableSelectOnHolidaySet->end();
    }
}