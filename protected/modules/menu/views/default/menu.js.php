// <script type="text/javascript">

<?php 

$menu = new ExtModule($this, array(
	"cacheable" => false,
	"extend" => "Ext.toolbar.Toolbar"
));

$menu->beginMethod("onItemChoosen(item, e)") ?>
	var url_param_name = '<?php echo Yii::app()->params['last_visited_url_param_name'] ?>';
  var params_param_name = '<?php echo Yii::app()->params['last_visited_url_jx_params_name'] ?>';

	var params = item.params || {};

	delete params[params_param_name];
	delete params[url_param_name];


    if(item.openDialog){
        theApp.showDialog(item.url, params);
    }
    else{
        params.<?php echo Yii::app()->params['last_visited_url_jx_params_name'] ?> = Ext.encode(params);
        params.<?php echo Yii::app()->params['last_visited_url_param_name'] ?> = item.url;
        theApp.loadModule(item.url, params);
    }


	e.stopPropagation();
	e.preventDefault();
	e.stopEvent();
	return false; <?php
$menu->endMethod();

$menu->beginMethod("onStaticItemChoosen(item)") ?>
	var url = 'http://'+this.app.getSystemBaseUrl()+"/"+item.url;
	if (item.window_open) {
		window.open(url);
	}
	else {
		document.location.href = url;
	}
	return false; <?php 
$menu->endMethod();

$menu->beginMethod("onExternalItemChoosen(item)") ?>
	if (item.window_open) {
		window.open(item.url);
	} else {
		document.location.href = item.url;
	} <?php $menu->endMethod();

$menu->beginMethod("initComponent()") ?>
	var menuitems = <?php echo ExtJsonBuilder::build($menuitems, ExtJsonBuilder::BUILD_AS_ARRAY); ?>;
	Ext.Array.each(menuitems, function(menu, i) {
		theApp.viewport.items.get('menubar').add(menu);
	});
	this.callParent(); <?php 
$menu->endMethod();

$menu->render();

