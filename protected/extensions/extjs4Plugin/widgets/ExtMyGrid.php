<?php

class ExtMyGrid extends ExtGrid
{
	/**
	 * if bbar is instance of ExtPagingToolbar, sets the grid store
	 * 
	 * @param ExtCodeFragment $bbar
	 */
	public function bbar(ExtCodeFragment $bbar)
	{
		if ($bbar instanceof ExtPagingToolbar) {
			if ($this->store !== null) {
				$bbar->store($this->store);
			}
		}
		
		return parent::bbar($bbar);
	}
}