// <script type="text/javascript">

<?php

include "_show.js.php";

$dlg->window->width(1000)->height(450)->resizable(true);

// textarea lecserelese html editorra:
$dlg->Form
->remove($form->generateName("text_en"))
->replace($form->generateName("text"), Ext::Container("Editors")
	->layout('hbox')
	->add(Ext::DisplayField()
		->fieldLabel($form->getActiveRecord()->getAttributeLabel("text"))
		->allowBlank(false)
		->labelWidth(170)
	)
	->add(Ext::TabPanel("EditorTabs")
		->flex(1)
		->layout('anchor')
		->defaults(array(
			"border" => false,
			"layout" => "anchor",
		))
		->add(Ext::Widget("Text")
			->title("Magyar")
			->add(createEditor($form->generateName("text")))
		)
		->add(Ext::Widget("TextEn")
			->title("Angol")
			->add(createEditor($form->generateName("text_en")))
		)
	)
);

function createEditor($name)
{
	return Ext::HtmlEditor($name)
		->allowBlank(false)
		->height(270)
		->anchor('100% 100%')
		->border(false)
		->enableFont(false)
		->enableFontSize(false)
		->enableColors(false)
		->enableLinks(false)
	;
}

// a htmleditor szovegeben vegzett modositasokat figyeljuk
$dlg->createVariable("originalText", '');
$dlg->createVariable("originalTextEn", '');
Ext::w($form->generateName("text"))->onChange(new ExtCodeFragment("Ext.bind(function(editor,newValue,oldValue,eOpts){
		if (oldValue != undefined && this.originalText == '') {
			this.originalText = oldValue;
		}
		
		if (this.originalText != '' && newValue != undefined) {		
			this.changed = this.changed || this.originalText != newValue;
		}
		
	}, this)"
));
Ext::w($form->generateName("text_en"))->onChange(new ExtCodeFragment("Ext.bind(function(editor,newValue,oldValue,eOpts){
		if (oldValue != undefined && this.originalTextEn == '') {
			this.originalTextEn = oldValue;
		}			
		
		if (this.originalTextEn != '' && newValue != undefined) {
			this.changed = this.changed || this.originalTextEn != newValue;
		}
		
	}, this)"
));


// on window resize, sets the editors height
$dlg->onResize(Ext::fn("window,width,height,eOpts", "
	if (eOpts != undefined) {
		".Ext::getCmp("EditorTabs", "$0.setHeight(height-120)").";
	}
"));

$dlg->render();

