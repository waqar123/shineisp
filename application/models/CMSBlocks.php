<?php

/**
 * CmsBlocks
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class CmsBlocks extends BaseCmsBlocks
{
	
	/**
	 * grid
	 * create the configuration of the grid
	 */	
	public static function grid($rowNum = 10) {
		
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'cms.block_id', 'alias' => 'block_id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Id' ), 'field' => 'block_id', 'alias' => 'block_id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Variable' ), 'field' => 'cms.var', 'alias' => 'var', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Date' ), 'field' => 'cms.publishedat', 'alias' => 'creationdate', 'sortable' => true, 'searchable' => true, 'type' => 'date' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Title' ), 'field' => 'cms.title', 'alias' => 'title', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Language' ), 'alias' => 'language', 'type' => 'arraydata', 'index' => 'block_id', 'run' => array('CmsBlocksData'=>'getTranslations') );
		
		$config ['datagrid'] ['fields'] = "cms.block_id, cms.var as var, DATE_FORMAT(cms.publishedat, '%d/%m/%Y %H:%i:%s') as creationdate, cms.title";
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )->from ( 'CmsBlocks cms' );
		
		$config ['datagrid'] ['index'] = "block_id";
		
		return $config;
	}	
	
/**
     * find
     * Get a record by ID
     * @param $id
     * @return Doctrine Record
     */
    public static function find($id) {
        return Doctrine::getTable ( 'CmsBlocks' )->findOneBy ( 'block_id', $id );
    }
    
    /**
     * findbyvar
     * Get a record by ID
     * @param $var
     * @return Doctrine Record
     */
    public static function findbyvar($var, $language_id=null) {
		if ( $language_id === null ) {
			$Session = new Zend_Session_Namespace ( 'Default' );
			$language_id = $Session->langid;
		}
    	
        return Doctrine_Query::create ()->from ( 'CmsBlocks cmsb' )
        								->leftJoin ( 'cmsb.CmsBlocksData cpd' )
									    ->leftJoin ( 'cpd.Languages l' )
        								->where ( 'var = ?', $var)
        								->addWhere('cpd.language_id = ?', $language_id)
        								->execute(array(), Doctrine::HYDRATE_ARRAY);
    }
    
    /**
     * delete
     * Delete a record by ID
     * @param $id
     */
    public static function deleteItem($id) {
        Doctrine::getTable ( 'CmsBlocks' )->findOneBy ( 'block_id', $id )->delete();
    }
    
    /**
     * getAllInfo
     * Get all data starting from the wikiID 
     * @param $id
     * @return Doctrine Record / Array
     */
    public static function getAllInfo($id) {
    	
        $dq = Doctrine_Query::create ()->select ( "block_id, 
        										   cpd.language_id,
        										   title, 
        										   DATE_FORMAT(publishedat, '%d/%m/%Y') as publishedat, 
        										   var, 
        										   title, 
        										   body" )
        							   ->from ( 'CmsBlocks cms' )
                                       ->leftJoin ( 'cms.CmsBlocksData cpd' )
									   ->leftJoin ( 'cpd.Languages l' )
                                       ->where ( "block_id = ?", $id )
                                       ->limit ( 1 );
        
        $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        
        // Get the languages
		if(!empty($records[0]['CmsBlocksData'])){
			foreach ($records[0]['CmsBlocksData'] as $record) {
				$records[0]['language_id'][] = $record['language_id'];
			}
			unset($records[0]['CmsBlocksData']);
		}
		
		return !empty($records[0]) ? $records[0] : array();
        
    }
    
	/**
	 * massdelete
	 * delete the cmsblock selected 
	 * @param array
	 * @return Boolean
	 */
	public static function massdelete($items) {
		$retval = Doctrine_Query::create ()->delete ()->from ( 'CmsBlocks' )->whereIn ( 'block_id', $items )->execute ();
		return $retval;
	}
    
	/**
	 * 
	 * Save the Cms Block data
	 */
	public static function saveAll($id, $params, $locale = 1) {
		$i = 0;
		
		// Set the new values
		if (is_numeric ( $id )) {
			$cmsblock = Doctrine::getTable ( 'CmsBlocks' )->find ( $id );
		} else {
			$cmsblock = new CmsBlocks();
			$cmsblock->publishedat = date('Y-m-d H:i:s');
		}
	
		$cmsblock->title = $params['title'];
		$cmsblock->body = $params['body'];
		$cmsblock->var = Shineisp_Commons_UrlRewrites::format($params ['var']);
		
		if($cmsblock->trySave ()){
			if(is_numeric($cmsblock['block_id'])){
				
				// Clear old reference records by page_id
				CmsBlocksData::deleteItems($cmsblock['block_id']);
				
				// Save the page translation references
				$PageData = new Doctrine_Collection('CmsBlocksData');
				foreach ($params['language_id'] as $idlang) {
					$PageData[$i]->block_id = $cmsblock['block_id'];
					$PageData[$i]->language_id = $idlang;
					$i++;
				}
				$PageData->save();
			}
		}
	}	

	######################################### BULK ACTIONS ############################################
	
	
	/**
	 * massdelete
	 * delete the tickets selected 
	 * @param array
	 * @return Boolean
	 */
	public static function bulk_delete($items) {
		if(!empty($items)){
			return self::massdelete($items);
		}
		return false;
	}	
}