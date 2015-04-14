<?php
/**
 * Catalog Search Controller
 */
class SkyMazon_RestConnect_SearchController extends Mage_Core_Controller_Front_Action {
	/**
	 * Retrieve catalog session
	 *
	 * @return Mage_Catalog_Model_Session
	 */
	protected function _getSession() {
		return Mage::getSingleton ( 'catalog/session' );
	}
	/**
	 * Display search result
	 */
	public function indexAction() {
		$query = Mage::helper ( 'catalogsearch' )->getQuery ();
		/* @var $query Mage_CatalogSearch_Model_Query */
		$query->setStoreId ( Mage::app ()->getStore ()->getId () );
		// $query->getQueryText()打印搜索关键词
		// var_dump($query->getQueryText());
		if ($query->getQueryText () != '') {
			if (Mage::helper ( 'catalogsearch' )->isMinQueryLength ()) {
				$query->setId ( 0 )->setIsActive ( 1 )->setIsProcessed ( 1 );
			} else {
				if ($query->getId ()) {
					$query->setPopularity ( $query->getPopularity () + 1 );
				} else {
					$query->setPopularity ( 1 );
				}
				
				if ($query->getRedirect ()) {
					$query->save ();
					$this->getResponse ()->setRedirect ( $query->getRedirect () );
					return;
				} else {
					$query->prepare ();
				}
			}
			
			Mage::helper ( 'catalogsearch' )->checkNotes ();
			
			
			//$collection = Mage::getModel ( "catalogsearch/query" )->getResultCollection ();
			$collection = $query->getResultCollection ();
			$i = 1;
			foreach ( $collection as $o ) {
				echo "<strong>Product Order:" . $i . "</strong><br/>";
				echo "Product Entity_Id: " . $o->getId () . "<br/>";
				echo "Product Price: " . $o->getPrice () . "<br/>";
				$i ++;
				echo "----------------------------------<br/>";
			}
			// $this->loadLayout ();
			// $this->_initLayoutMessages ( 'catalog/session' );
			// $this->_initLayoutMessages ( 'checkout/session' );
			// $this->renderLayout ();
			
			if (! Mage::helper ( 'catalogsearch' )->isMinQueryLength ()) {
				$query->save ();
			}
		} else {
			// $this->_redirectReferer ();
		}
	}
}