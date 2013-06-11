<?php

class ExtRowAction extends ExtClass
{
	/**
	 * creates delgated function to the given callback
	 */
	public function callback($callback)
	{
		if ($callback instanceof ExtFunction) {
			if ($callback->isDefined() && $callback->getContext() instanceof ExtModule) {
				return parent::callback(new ExtCodeFragment(sprintf("Ext.Function.bind(%s, this)", $callback->ref())));
			}
		}
		
		return parent::callback($callback);
	}
}