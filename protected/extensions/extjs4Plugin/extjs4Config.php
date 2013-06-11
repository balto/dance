<?php

class extjs4Config
{
    private static $config =
        array(
        	'extjs4_version' => 'v1.0',
        	'extjs4_comment' => true,
        #
        # adapters
        #
        	'extjs4_default_adapter' => 'ext',
        	'extjs4_adapters' =>
                  array(
                    'jquery' => array(
                      'adapter/jquery/jquery.js',
                      'adapter/jquery/jquery-plugins.js',
                      'adapter/jquery/ext-jquery-adapter.js'
                    ),
                    'prototype' => array(
                      'adapter/prototype/prototype.js',
                      'adapter/prototype/scriptaculous.js?load=effects.js',
                      'adapter/prototype/ext-prototype-adapter.js'
                    ),
                    'yui' => array(
                      'adapter/yui/yui-utilities.js',
                      'adapter/yui/ext-yui-adapter.js'
                    ),
                    'ext' => array(
                      //'adapter/ext/ext-base.js' //-debug
                    )
                  ),

        #
        # themes
        #
        	'extjs4_default_theme' => 'aero',
        	'extjs4_themes' =>
                  array(
                    'aero' => array( ),
                    'gray' => array( 'xtheme-gray.css' ),
                  ),
        #
        # base directories
        #
        	'extjs4_plugin_dir' => '/js/extjs4/plugins/',
        	'extjs4_js_dir' => '/js/extjs4/',
        	'extjs4_css_dir' => '/css/extjs4/',
        	'extjs4_images_dir' => '/images/extjs4/',
        #
        # spacer gif
        #
        	'extjs4_spacer' => '/resources/themes/images/default/tree/s.gif',
        #
        # attributes which must handled as array
        #
        	'extjs4_list_attributes' => array('items', 'tbar', 'bbar', 'buttons', 'plugins', 'view', 'fields', 'tools', 'actions'),
        #
        # array values that don't need quotes
        #
        	'extjs4_quote_except' =>
                  array(
                    'key'   => array('renderer', 'store', 'defaults', 'plugins', 'cm', 'ds', 'view', 'tbar', 'bbar,scope', 'key', 'parentPanel', 'handler'),
                    'value' => array('true', 'false', 'new Ext.', 'function', 'Ext.', '__(', '{', 'this.'),
                    //'value' => array('true', 'false', 'new', 'function', 'Ext.', '__(', '{', 'this.')
                  ),

        #
        # mapping plugin method against class
        #
        	'extjs4_classes' =>
                  array(
                    // data
                    'Model'			=>  'Ext.data.Model',
                    'JsonReader'    => 'Ext.data.reader.Json',
                    'JsonWriter'    => 'Ext.data.writer.Json',
                  	'ArrayReader'   => 'Ext.data.ArrayReader',
                  	'Store'         => 'Ext.data.Store',
                    'TreeStore'     => 'Ext.data.TreeStore',
                    'SimpleStore'   => 'Ext.data.SimpleStore',
                    'JsonStore'     => 'Ext.data.JsonStore',
                    'GroupingStore' => 'Ext.data.GroupingStore',
                    'HttpProxy'     => 'Ext.data.proxy.Ajax',
                    'Template'      => 'Ext.Template',
                    'TemplateColumn' => 'Ext.grid.column.Template',
                    'XTemplate'     => 'Ext.XTemplate',
                    // widgets
                    'BoxComponent'            => 'Ext.BoxComponent',
                    'Button'                  => 'Ext.button.Button',
                    'GridPanel'               => 'Ext.grid.Panel',
                    'ColumnModel'             => 'Ext.grid.ColumnModel',
                    'CheckColumn'			  => 'Ext.ux.CheckColumn',
                    'GridView'                => 'Ext.grid.GridView',
                    'GroupingView'            => 'Ext.grid.GroupingView',
                    'Grouping'  			  => 'Ext.grid.feature.Grouping',
                    //'EditorGridPanel'         => 'Ext.grid.EditorGridPanel',
                    'RowSelectionModel'       => 'Ext.grid.RowSelectionModel',
                    'CheckboxSelectionModel'  => 'Ext.grid.CheckboxSelectionModel',
                    'Container'				  => 'Ext.container.Container',
                    'Panel'                   => 'Ext.panel.Panel',
                    'TabPanel'                => 'Ext.tab.Panel',
                    'FormPanel'               => 'Ext.form.Panel',
                    'Viewport'                => 'Ext.Viewport',
                    'Window'                  => 'Ext.window.Window',
                    'FieldSet'                => 'Ext.form.FieldSet',
                    'Hidden'                  => 'Ext.form.Hidden',
                    'DisplayField'            => 'Ext.form.field.Display',
                    'NumberField'             => 'Ext.form.field.Number',
                    'DateField'               => 'Ext.form.field.Date',
                    'TextField'               => 'Ext.form.field.Text',
                    'TimeField'               => 'Ext.form.field.Time',
                    'HtmlEditor'              => 'Ext.form.HtmlEditor',
                    'ComboBox'                => 'Ext.form.field.ComboBox',
                    'Label'                   => 'Ext.form.Label',
                    'Menu'                    => 'Ext.menu.Menu',
                    'Item'	  		          => 'Ext.menu.Item',
                    'TextItem'                => 'Ext.menu.TextItem',
                    'CheckItem' 	          => 'Ext.menu.CheckItem',
                    'Toolbar'                 => 'Ext.toolbar.Toolbar',
                    'MenuButton'              => 'Ext.toolbar.MenuButton',
                    'Fill'                    => 'Ext.toolbar.Fill',
                    'Separator'               => 'Ext.toolbar.Separator',
                    'Spacer'                  => 'Ext.toolbar.Spacer',
                    'PagingToolbar'           => 'Ext.toolbar.Paging',
                    'MessageBox'              => 'Ext.MessageBox',
                    'KeyMap'                  => 'Ext.KeyMap',
                    // tree stuff
                    'TreePanel'               => 'Ext.tree.Panel',
                    'TreeLoader'              => 'Ext.tree.TreeLoader',
                    'Node'                    => 'Ext.data.Node',
                    'TreeNode'                => 'Ext.tree.TreeNode',
                    'AsyncTreeNode'           => 'Ext.tree.AsyncTreeNode',
                    // base
                    'Observable'              => 'Ext.util.Observable',
                  ),
        #
        # default setting for classes
        #

        #
        # data
        #
        	'Ext.data.Model' =>
                  array(
                    'class'       => 'Ext.data.Model',
                    'attributes'  => array()
                  ),

        	'Ext.data.reader.Json' =>
                  array(
                    'class'       => 'Ext.data.reader.Json',
                    'attributes'  => array()
                  ),

        	'Ext.data.writer.Json' =>
                  array(
                    'class'       => 'Ext.data.writer.Json',
                    'attributes'  => array()
                  ),

            'Ext.data.ArrayReader' =>
                  array(
                    'class'       => 'Ext.data.ArrayReader',
                    'attributes'  => array()
                  ),

        	'Ext.data.Store' =>
                  array(
                    'class'       => 'Ext.data.Store',
                    'attributes'  => array()
                  ),

            'Ext.data.TreeStore' =>
                  array(
                    'class'       => 'Ext.data.TreeStore',
                    'attributes'  => array()
                  ),

        	'Ext.data.SimpleStore' =>
                  array(
                    'class'       => 'Ext.data.SimpleStore',
                    'attributes'  => array()
                  ),

        	'Ext.data.JsonStore' =>
                  array(
                    'class'       => 'Ext.data.JsonStore',
                    'attributes'  => array()
                  ),

        	'Ext.data.GroupingStore' =>
                  array(
                    'class'       => 'Ext.data.GroupingStore',
                    'attributes'  => array()
                  ),

        	'Ext.grid.feature.Grouping' =>
                  array(
                    'class'       => 'Ext.grid.feature.Grouping',
                    'attributes'  => array()
                  ),

            'Ext.data.proxy.Ajax' =>
                  array(
                    'class'       => 'Ext.data.proxy.Ajax',
                    'attributes'  => array()
                  ),

        	'Ext.Template' =>
                  array(
                    'class'       => 'Ext.Template',
                    'attributes'  => array()
                  ),

        	'Ext.grid.column.Template' =>
                  array(
                    'class'       => 'Ext.grid.column.Template',
                    'attributes'  => array()
                  ),

            'Ext.XTemplate' =>
                  array(
                    'class'       => 'Ext.XTemplate',
                    'attributes'  => array()
                  ),

        #
        # widgets
        #
        	'Ext.BoxComponent' =>
                  array(
                    'class'       => 'Ext.BoxComponent',
                    'attributes'  => array()
                  ),

        	'Ext.button.Button' =>
                  array(
                    'class'       => 'Ext.button.Button',
                    'attributes'  => array()
                  ),

        	'Ext.container.Container' =>
                  array(
                    'class'       => 'Ext.container.Container',
                    'attributes'  => array()
                  ),

            'Ext.grid.Panel' =>
                  array(
                    'class'       => 'Ext.grid.Panel',
                    'attributes'  => array()
                  ),

        	'Ext.grid.ColumnModel' =>
                  array(
                    'class'       => 'Ext.grid.ColumnModel',
                    'attributes'  => array()
                  ),

        	'Ext.grid.GridView' =>
                  array(
                    'class'       => 'Ext.grid.GridView',
                    'attributes'  => array()
                  ),

        	'Ext.grid.GroupingView' =>
                  array(
                    'class'       => 'Ext.grid.GroupingView',
                    'attributes'  => array()
                  ),

        	'Ext.grid.EditorGridPanel' =>
                  array(
                    'class'       => 'Ext.grid.EditorGridPanel',
                    'attributes'  => array()
                  ),

        	'Ext.grid.RowSelectionModel' =>
                  array(
                    'class'       => 'Ext.grid.RowSelectionModel',
                    'attributes'  => array()
                  ),

        	'Ext.grid.CheckboxSelectionModel' =>
                  array(
                    'class'       => 'Ext.grid.CheckboxSelectionModel',
                    'attributes'  => array()
                  ),

        	'Ext.panel.Panel' =>
                  array(
                    'class'       => 'Ext.panel.Panel',
                    'attributes'  => array()
                  ),

        	'Ext.tab.Panel' =>
                  array(
                    'class'       => 'Ext.tab.Panel',
                    'attributes'  => array(
                      'resizeTabs'      => true,
                      'minTabWidth'     => 100,
                      'tabWidth'        => 150,
                      'activeTab'       => 0,
                      'enableTabScroll' => true,
                      'defaults'        => '{ autoScroll: true }'
                    )
                  ),

        	'Ext.form.Panel' =>
                  array(
                    'class'       => 'Ext.form.Panel',
                    'attributes'  => array()
                  ),

        	'Ext.Viewport' =>
                  array(
                    'class'       => 'Ext.Viewport',
                    'attributes'  => array('layout' => 'border')
                  ),

        	'Ext.window.Window' =>
                  array(
                    'class'       => 'Ext.window.Window',
                    'attributes'  => array(
                      'constrain'   => true,
                      'layout'      => 'fit',
                      'width'       => 500,
                      'height'      => 300,
                      'closeAction' => 'hide',
                      'plain'       => true
                    )
                  ),

        	'Ext.form.FieldSet' =>
                  array(
                    'class'       => 'Ext.form.FieldSet',
                    'attributes'  => array()
                  ),

        	'Ext.form.Hidden' =>
                  array(
                    'class'       => 'Ext.form.Hidden',
                    'attributes'  => array()
                  ),

            'Ext.form.field.Display' =>
                  array(
                    'class'       => 'Ext.form.field.Display',
                    'attributes'  => array()
                  ),

            'Ext.form.field.Number' =>
                  array(
                    'class'       => 'Ext.form.field.Number',
                    'attributes'  => array()
                  ),

        	'Ext.form.field.Date' =>
                  array(
                    'class'       => 'Ext.form.field.Date',
                    'attributes'  => array()
                  ),

        	'Ext.form.field.Text' =>
                  array(
                    'class'       => 'Ext.form.field.Text',
                    'attributes'  => array()
                  ),

        	'Ext.form.field.Time' =>
                  array(
                    'class'       => 'Ext.form.field.Time',
                    'attributes'  => array()
                  ),

        	'Ext.form.HtmlEditor' =>
                  array(
                    'class'       => 'Ext.form.HtmlEditor',
                    'attributes'  => array()
                  ),

        	'Ext.form.field.ComboBox' =>
                  array(
                    'class'       => 'Ext.form.field.ComboBox',
                    'attributes'  => array()
                  ),

            'Ext.form.Label' =>
                  array(
                      'class'       => 'Ext.form.Label',
                      'attributes'  => array()
                  ),

        	'Ext.menu.Menu' =>
                  array(
                    'class'       => 'Ext.menu.Menu',
                    'attributes'  => array()
                  ),

        	'Ext.menu.Item' =>
                  array(
                    'class'       => 'Ext.menu.Item',
                    'attributes'  => array()
                  ),

        	'Ext.menu.TextItem' =>
                  array(
                    'class'       => 'Ext.menu.TextItem',
                    'attributes'  => array()
                  ),

        	'Ext.menu.CheckItem' =>
                  array(
                    'class'       => 'Ext.menu.CheckItem',
                    'attributes'  => array()
                  ),

        	'Ext.toolbar.Toolbar' =>
                  array(
                    'class'       => 'Ext.toolbar.Toolbar',
                    'attributes'  => array()
                  ),

        	'Ext.toolbar.MenuButton' =>
                  array(
                    'class'       => 'Ext.toolbar.MenuButton',
                    'attributes'  => array()
                  ),

        	'Ext.toolbar.Fill' =>
                  array(
                    'class'       => 'Ext.toolbar.Fill',
                    'attributes'  => array()
                  ),

        	'Ext.toolbar.Separator' =>
                  array(
                    'class'       => 'Ext.toolbar.Separator',
                    'attributes'  => array()
                  ),

        	'Ext.toolbar.Spacer' =>
                  array(
                    'class'       => 'Ext.toolbar.Spacer',
                    'attributes'  => array()
                  ),

        	'Ext.toolbar.Paging' =>
                  array(
                    'class'       => 'Ext.toolbar.Paging',
                    'attributes'  => array()
                  ),

        	'Ext.MessageBox' =>
                  array(
                    'class'       => 'Ext.MessageBox',
                    'attributes'  => array()
                  ),


        	'Ext.KeyMap' =>
                  array(
                    'class'       => 'Ext.KeyMap',
                    'attributes'  => array()
                  ),


            'Ext.tree.Panel' =>
                  array(
                    'class'       => 'Ext.tree.Panel',
                    'attributes'  => array()
                  ),


        	'anonymousClass' =>
                  array(
                    'class'       => 'anonymousClass',
                    'attributes'  => array()
                  ),
        );

   public static function get($key) {
       if (!isset(self::$config[$key])) throw new Exception('Ismeretlen extjs4 config kulcs: '.$key);
       return self::$config[$key];
   }
}
?>
