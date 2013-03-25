<?php

/**
 * Servers
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Servers extends BaseServers {
    
	/**
	 * Override of the relations between the 
	 * Servers and Custom_Attributes_Values table
	 * 
	 * Setup a new external relationship 
	 * between the Servers table class 
	 * and the CustomAttributesValues class
	 * 
	 * (non-PHPdoc)
	 * @see BaseServers::setUp()
	 */
    public function setUp()
    {
        parent::setUp();
        $this->hasOne('CustomAttributesValues', array(
             'local' => 'server_id',
             'foreign' => 'external_id'));
    }
	
	/**
	 * grid
	 * create the configuration of the grid
	 */	
	public static function grid($rowNum = 10) {
		
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 's.server_id', 'alias' => 'server_id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'ID' ), 'field' => 's.server_id', 'alias' => 'server_id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Name' ), 'field' => 'r.subject', 'alias' => 'servername', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'IP' ), 'field' => 's.ip', 'alias' => 'ip', 'sortable' => true, 'searchable' => true);
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Status' ), 'field' => 'stat.status', 'alias' => 'status', 'sortable' => true, 'searchable' => true );
		
		$config ['datagrid'] ['fields'] = "s.server_id, s.name as servername, s.ip as ip, stat.status as status";
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )
																		->from ( 'Servers s' )
																        ->leftJoin ( 's.Isp i' )
																        ->leftJoin ( 's.Servers_Types st' )
																        ->leftJoin ( 's.Statuses stat' );
		
		$config ['datagrid'] ['rownum'] = $rowNum;
		
		$config ['datagrid'] ['basepath'] = "/admin/servers/";
		$config ['datagrid'] ['index'] = "server_id";
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
		
		$config ['datagrid'] ['buttons'] ['edit'] ['label'] = $translator->translate ( 'Edit' );
		$config ['datagrid'] ['buttons'] ['edit'] ['cssicon'] = "edit";
		$config ['datagrid'] ['buttons'] ['edit'] ['action'] = "/admin/servers/edit/id/%d";
		
		$config ['datagrid'] ['buttons'] ['delete'] ['label'] = $translator->translate ( 'Delete' );
		$config ['datagrid'] ['buttons'] ['delete'] ['cssicon'] = "delete";
		$config ['datagrid'] ['buttons'] ['delete'] ['action'] = "/admin/servers/delete/id/%d";
		
		$config ['datagrid'] ['massactions'] = array ('massdelete'=>'Mass Delete');
		
		return $config;
	}	
	
	
	/**
	 * Save all the data
	 * @param array $params
	 */
	public static function saveAll(array $params) {
		
		if(!empty($params['server_id']) && is_numeric($params['server_id'])){
			$server = Doctrine::getTable ( 'Servers' )->find ( $params['server_id'] );
		}else{
			$server = new Servers();
		}
		
		$server->name = $params ['name'];
		$server->ip = $params ['ip'];
		$server->netmask = $params ['netmask'];
		$server->host = $params ['host'];
		$server->domain = $params ['domain'];
		$server->description = $params ['description'];
		$server->status_id = $params ['status_id'];
		$server->isp_id = $params ['isp_id'];
		$server->type_id = $params ['type_id'];
		$server->save ();
		
		return $server['server_id'];
	}
	
	/**
     * Get a record 
     * @param $id
     * @return Doctrine_Record
     */
    public static function find($id) {
        return Doctrine::getTable ( 'Servers' )->find ( $id );
    }
	
	/**
     * Get all the information about the server 
     * @param $id
     * @return ArrayObject
     */
    public static function getAll($id) {
    	
        $record = Doctrine_Query::create ()
        			->from ( 'Servers s' )
        			->leftJoin ( 's.Servers_Types' )
        			->leftJoin ( 's.CustomAttributesValues' )
        			->where ( "s.server_id = ?", $id )
        			->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        			
    	return !empty($record[0]) ? $record[0] : array();
    }
	
	/**
     * setStatus
     * Set a record with a status
     * @param $id, $status
     * @return Void
     */
    public static function setStatus($id, $status) {
        $dq = Doctrine::getTable ( 'Servers' )->find ( $id );
        $dq->status_id = $status;
        return $dq->save ();
    }
    
	/**
	 * findAllbyIsp
	 * Get a record by the ISP ID
	 * @param $isp_id
	 * @return Doctrine Record
	 */
	public static function findAllbyIsp($isp_id = "", $fields = "*", $retarray = false) {
		$isp_id = is_numeric($isp_id) ? $isp_id : Isp::getActiveISPID();
		$dq = Doctrine_Query::create ()->select ( $fields )->from ( 'Servers i' )->leftJoin ( 'i.Servers_Types st ON st.type_id = i.type_id' )->andWhere ( "isp_id = $isp_id" );
		return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/*
	 * Get Webserver information 
	 */
	public static function getWebserver(){
		$dq = Doctrine_Query::create ()->from ( 'Servers i' )
										->leftJoin ( 'i.Servers_Types st ON st.type_id = i.type_id' )
										->where ( "type_id = ?", 1 )
										->andWhere ( "isp_id = ?", Isp::getActiveISPID());
         $record = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
         return !empty($record[0]) ? $record[0] : false; 
	}
	
	/*
	 * Get DB Server information 
	 */
	public static function getDbserver(){
		$dq = Doctrine_Query::create ()->from ( 'Servers i' )
										->leftJoin ( 'i.Servers_Types st ON st.type_id = i.type_id' )
										->where ( "type_id = ?", 3 )
										->andWhere ( "isp_id = ?", Isp::getActiveISPID());
         $record = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
         return !empty($record[0]) ? $record[0] : false; 
	}
	
	/*
	 * Get Mail Server information 
	 */
	public static function getMailserver(){
		$dq = Doctrine_Query::create ()->from ( 'Servers i' )
										->leftJoin ( 'i.Servers_Types st ON st.type_id = i.type_id' )
										->where ( "type_id = ?", 2 )
										->andWhere ( "isp_id = ?", Isp::getActiveISPID());
        $record = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        return !empty($record[0]) ? $record[0] : false; 
	}
	
	/*
	 * Get DNS Server information 
	 */
	public static function getDnsserver(){
		$dq = Doctrine_Query::create ()->from ( 'Servers i' )
										->leftJoin ( 'i.Servers_Types st ON st.type_id = i.type_id' )
										->where ( "type_id = ?", 4 )
										->andWhere ( "isp_id = ?", Isp::getActiveISPID());
         return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
		
	/**
	 * massdelete
	 * delete the attributes selected 
	 * @param array
	 * @return Boolean
	 */
	public static function massdelete($items) {
		$retval = Doctrine_Query::create ()->delete ()->from ( 'Servers' )->whereIn ( 'server_id', $items )->execute ();
		return $retval;
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