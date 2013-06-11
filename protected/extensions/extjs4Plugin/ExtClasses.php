<?php

class ExtClasses implements ArrayAccess
{
	const EXTCLASS       = 0;
	const XTYPE          = 1;
	const PHPCLASS       = 2;
	const DEFAULT_CONFIG = 3;

	private function getClasses() {
		return array(
			"widget" => array( // default PHP class is ExtWidget
				// if class has EXTCLASS property, PHP class must inherited from ExtClass!
				"Button"    => array("Ext.button.Button", "button", null, array(
				)),
				"Container" => array("Ext.container.Container", "container", null, array(
				)),
				"Component" => array("Ext.Component", "component", null, array(
				)),
				"Panel"     => array("Ext.panel.Panel", "panel", null, array(
				)),
				"TreePanel"     => array("Ext.tree.Panel", "treepanel", null, array(
				)),
				"Viewport"  => array("Ext.Viewport", "viewport", null, array(
				)),
				"Window"    => array("Ext.window.Window", "window", null, array(
				)),
				"GridPanel" => array("Ext.grid.GridPanel", "grid", "ExtMyGrid", array(
				)),

				// form components
				"Form" => array("Ext.form.Panel", "form", null, array(
				)),
				"FormPanel" => array("Ext.form.Panel", "form", null, array(
				)),
				"FieldContainer" => array("Ext.form.FieldContainer", "fieldcontainer", null, array(
				)),
				"FieldSet" => array("Ext.form.FieldSetr", "fieldset", null, array(
				)),
				"DateTimeField" => array("Ext.ux.form.DateTimeField", "datetimefield", null, array(
					'format' => Yii::app()->params['extjs_date_format'],
					'submitFormat' => Yii::app()->params['db_date_format'],
		        )),
				"TextArea" => array("Ext.form.TextArea", "textarea", null, array(
				)),
				"HtmlEditor" => array("Ext.form.HtmlEditor", "htmleditor", null, array(
				)),
				"ComboBox"  => array("Ext.form.field.ComboBox", "combo", null, array(
				)),
				"DateField" => array("Ext.form.field.Date", "datefield", null, array(
				    'format' => Yii::app()->params['extjs_date_format'],
					'submitFormat' => Yii::app()->params['db_date_format'],
		        )),
				"TimeField" => array("Ext.form.field.Time", "timefield", null, array(
				)),
				"DisplayField" => array("Ext.form.field.Display", "displayfield", null, array(
					"anchor" => "100%",
					"value" => "",
				)),
				"FieldSet" => array("Ext.form.FieldSet", "fieldset", null, array(
				)),
				"Hidden" => array("Ext.form.Hidden", "hidden", null, array(
				)),
				"Label" => array("Ext.form.Label", "label", null, array(
				)),
				"TextField" => array("Ext.form.field.Text", "textfield", null, array(
					"anchor" => "100%",
				)),
				"NumberField" => array("Ext.form.field.Number", "numberfield", null, array(
					"anchor" => "100%",
				)),
				"NumericField" => array("Ext.ux.form.NumericField", "numericfield", null, array(
					"anchor" => "100%",
				)),
				"Checkbox" => array("Ext.form.Checkbox", "checkbox", null, array(
				)),
				"CheckboxGroup" => array("Ext.form.CheckboxGroup", "checkboxgroup", null, array(
				)),
				"Radio" => array("Ext.form.field.Radio", "radiofield", null, array(
				)),
				"RadioGroup" => array("Ext.form.RadioGroup", "radiogroup", null, array(
				)),

				// toolbar components
				"Toolbar"     => array("Ext.toolbar.Toolbar", "toolbar", null, array(
				)),
				// '->'
				"ToolbarFill" => array("Ext.Toolbar.Fill", "tbfill", null, array(
				)),
				"ToolbarItem" => array("Ext.Toolbar.Item", "tbitem", null, array(
				)),
				// '-'
				"ToolbarSeparator" => array("Ext.Toolbar.Separator", "tbseparator", null, array(
				)),
				"ToolbarSpacer" => array("Ext.Toolbar.Spacer", "tbspacer", null, array(
				)),
				"ToolbarSplitButton" => array("Ext.Toolbar.SplitButton", "tbsplit", null, array(
				)),
				"ToolbarTextItem" => array("Ext.Toolbar.TextItem", "tbtext", null, array(
				)),
				"ItemSelector" => array("Ext.ux.form.ItemSelector", "itemselector", null, array(
				)),
				"Splitter" => array("Ext.resizer.Splitter", "splitter", null, array(
				)),

				// Tabbed dialog
				"TabPanel" => array("Ext.tab.Panel", "tabpanel", null, array(
					"activeTab" => 0
				)),

				// rowactions, Docs at http://extjs.eu/docs/?class=Ext.ux.grid.RowActions
				"RowActions" => array("Ext.ux.grid.RowActions", "rowactions", "ExtRowActions", array(
					"keepSelection" => true
				)),
				// represents an action in RowActions
				"RowAction" => array(null, null, "ExtRowAction", array(
				)),
				"Chart" => array("Ext.chart.Chart", "chart", null, array(
				)),

				// custom widgets
				"PagingToolbar" => array("Ext.toolbar.Paging", "pagingtoolbar", "ExtPagingToolbar", array(
				)),
				"TurnSearch" => array("Ext.Container", "container", "ExtTurnSearch", array(
				)),
				"RightSearch" => array("Ext.Container", "container", "ExtRightSearch", array(
				)),
				"CodetableForm" => array("Ext.form.Panel", "form", "ExtCodetableForm", array(
        )),
				"MemberRelatedToPrinting" => array("Ext.Container", "container", "ExtMemberRelatedToPrinting", array(
				)),
				// abstract widget, intended to hold configuration only
				"Widget" => array(null, null, "ExtWidget", array(
				)),
			),

			"store" => array(
				"Store" => array("Ext.ux.app.Store", null, null, array(
				)),
				"TreeStore" => array("Ext.data.TreeStore", null, null, array(
				)),
				"SimpleStore" => array("Ext.data.SimpleStore", null, null, array(
				)),
				"JsonStore" => array("Ext.data.JsonStore", null, null, array(
				)),
				"GroupingStore" => array("Ext.data.GroupingStore", null, null, array(
				)),
			),

			"reader" => array(
				"Reader" => array("Ext.data.Reader", null, "ExtMyReader", array(
					"totalProperty" => "totalCount",
					"id" => "id",
					"root" => "data"
				)),
				"JsonReader" => array("Ext.data.reader.Json", null, "ExtMyReader", array(
					"totalProperty" => "totalCount",
					"type" => "json",
					"id" => "id",
					"root" => "data"
				)),
			),

			"proxy" => array(
				"Proxy" => array("Ext.data.proxy.Proxy", null, "ExtProxy", array(
					"actionMethods" => array("create"=>"POST", "read"=>"POST", "update"=>"POST", "destroy"=>"POST"),
					"type" => "ajax",
				)),
			),
		);
    }

	public function offsetExists($offset)
	{
		return array_key_exists($offset, self::getClasses());
	}

	public function offsetGet($offset)
	{
		$_classes = self::getClasses();
        if (array_key_exists($offset, $_classes)) {
            return $_classes[$offset];
		}
		else {
			return null;
		}
	}

	public function offsetSet($offset, $value)
	{
		// Set operation not allowed!
	}

	public function offsetUnset($offset)
	{
		// Set operation not allowed!
	}
}