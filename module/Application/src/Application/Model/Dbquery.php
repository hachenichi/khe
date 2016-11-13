<?php

namespace Application\Model;
use Zend\Db\Adapter\Adapter;

use Zend\Db\Sql\Sql;
date_default_timezone_set('America/Mexico_City');


class Dbquery {

	private $db;//DB Adapter

	private $config;
	public $SITE_URL;
	public $BASE_PATH;
	public $PASSPHRASE_WORKER_TOKEN;
	public $FRONT_URL;
	public $PARTNERS_URL;

	private $now;

	private $tools;

	public function __construct($dbAdapter, $config, $tools) {
		$this->db                      = $dbAdapter;
		$this->config                  = $config;
		$this->tools                   = $tools;
		$this->now                     = date("Y-m-d H:i:s");
		$this->SITE_URL                = $this->config['layout']['SITE_URL'];
		$this->BASE_PATH               = $this->config['server']['BASE_PATH'];
		
		//$this->FRONT_URL               = $this->config['layout']['FRONT_URL'];
		//$this->PARTNERS_URL            = $this->config['layout']['PARTNERS_URL'];


	}
	
	
	public function login($email, $pass) {
		
		$result = $this->db->query('SELECT
									users_id
									FROM users
									WHERE users_email = ?
									AND users_password = ?
									AND users_status = 1',
			array($email, md5($pass.$this->config['pass']['concat'])));

		$user = $result->toArray();
		//Debug::dump($user);
		return $user;
	}
	
	public function getuser() {
		
		$result = $this->db->query('SELECT
									*
									FROM workers
									WHERE workers_status = 1 order by workers_rapiddo_id',array() );
		$workers = $result->toArray();
		//Debug::dump($user);
		return $workers;
	}
	public function getUsers() {
		
		$result = $this->db->query('SELECT
									*
									FROM users ',array() );
		$workers = $result->toArray();
		//Debug::dump($user);
		return $workers;
	}
	
	
	public function getWorkersByRapiddoId($RapiddoId) {
		
		$result = $this->db->query('SELECT
									*
									FROM workers
									WHERE workers_rapiddo_id = ? ',array($RapiddoId) );
		$workers = $result->toArray();
		//Debug::dump($user);
		return $workers;
	}
	
	public function getWorkersByWorkerId($workerId) {
		
		$result = $this->db->query('SELECT
									*
									FROM workers
									WHERE workers_id = ? ',array($workerId) );
		$workers = $result->toArray();
		//Debug::dump($user);
		return $workers;
	}
	
	public function setWorkers($data){
		
		$sql = new Sql($this->db);
		$insert = $sql->insert('workers');
		$newData = array(
			'workers_id'=> null,
			'workers_rapiddo_id'=> $data["id"],
			'workers_creation_date'=> $data["creationDate"],
			'workers_fullname'=> $data["name"],
			'workers_email'=> $data["email"],
			'workers_phone'=> $data["phoneNumber"],
			'workers_city'=> $data["city"],
			'workers_status'=> 1
		);
		$insert->values($newData);
		$selectString = $sql->getSqlStringForSqlObject($insert);
		$results = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$lastInsert = $this->db->getDriver()->getLastGeneratedValue();
		if($results){
			return $lastInsert;
		}else{
			return $results;
		}
	}
	
	public function updateWorkersFromRapiddo($data){
		$sql = new Sql($this->db);
		$update = $sql->update('workers');
		$newData = array(
			'workers_fullname'=> $data["name"],
			'workers_email'=> $data["email"],
			'workers_phone'=> $data["phoneNumber"],
			'workers_city'=> $data["city"],
		);
		$update->set($newData);
		$update->where('workers_rapiddo_id = '.$data["id"]);
		$selectString = $sql->getSqlStringForSqlObject($update);
		$resultUpdate = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		return $resultUpdate;
	}
	

	public function updateWorkersNomina($id_worker,$data){
		$sql = new Sql($this->db);
		$update = $sql->update('workers');
		$newData = array(
			'workers_company'=> $data["company"],
			'workers_name'=> $data["names"],
			'workers_lastname'=> $data["lastname"],
			'workers_secondlastname'=> $data["secondlastname"],
			'workers_nationality'=> $data["nationality"],
			'workers_birthdate'=> $data["birthdate"]." 00:00:00",
			'workers_placeofbirth'=> $data["placeofbirth"],
			'workers_civilstatus'=> $data["civilstatus"],
			'workers_occupation'=> $data["occupation"],
			'workers_street'=> $data["street"],
			'workers_number'=> $data["number"],
			'workers_colony'=> $data["colony"],
			'workers_zip'=> $data["zip"],
			'workers_municipality'=> $data["municipality"],
			'workers_entity'=> $data["entity"],
			'workers_rfchomoclave'=> $data["rfchomoclave"],
			'workers_curp'=> $data["curp"],
			'workers_activitiestobecarried'=> $data["activitiestobecarried"],
			'workers_determinablepayment'=> $data["determinablepayment"],
			'workers_frequencyofpayment'=> $data["frequencyofpayment"],
			'workers_quantity'=> $data["quantity"],
			'workers_contractsigningdate'=> $data["contractsigningdate"]." 00:00:00",
			'workers_bank'=> $data["bank"],
			'workers_accountnumber'=> $data["accountnumber"],
			'workers_numberclabe'=> $data["numberclabe"],
			'workers_status'=> 1,
		);
		$update->set($newData);
		$update->where('workers_id = '.$id_worker);
		$selectString = $sql->getSqlStringForSqlObject($update);
		$resultUpdate = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		return $resultUpdate;
	}
	
	public function getCustomers() {
		
		$result = $this->db->query('SELECT
									*
									FROM customers
									WHERE customers_status = 1 order by customers_rapiddo_id',array() );
		$customers = $result->toArray();
		//Debug::dump($user);
		return $customers;
	}
	
	public function getCustomersByRapiddoId($RapiddoId) {
		
		$result = $this->db->query('SELECT
									*
									FROM customers
									WHERE customers_rapiddo_id = ? ',array($RapiddoId) );
		$workers = $result->toArray();
		//Debug::dump($user);
		return $workers;
	}
	
	
	public function setCustomers($data){
		
		$sql = new Sql($this->db);
		$insert = $sql->insert('customers');
		$newData = array(
			'customers_id'=> null,
			'customers_rapiddo_id'=> $data["id"],
			'ratetype_id'=> 1,
			'customers_creation_date'=> $data["creationDate"],
			'customers_fullname'=> $data["name"],
			'customers_email'=> $data["email"],
			'customers_phone'=> $data["phoneNumber"],
			'customers_company_id'=> $data["company_id"],
			'customers_company_name'=> $data["company_name"],
			'customers_status'=> 1
		);
		$insert->values($newData);
		$selectString = $sql->getSqlStringForSqlObject($insert);
		$results = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$lastInsert = $this->db->getDriver()->getLastGeneratedValue();
		if($results){
			return $lastInsert;
		}else{
			return $results;
		}
	}
	
	public function updateCustomersFromRapiddo($data){
		$sql = new Sql($this->db);
		$update = $sql->update('customers');
		$newData = array(
			'customers_creation_date'=> $data["creationDate"],
			'customers_fullname'=> $data["name"],
			'customers_email'=> $data["email"],
			'customers_phone'=> $data["phoneNumber"],
			'customers_company_id'=> $data["company_id"],
			'customers_company_name'=> $data["company_name"],
			'customers_status'=> 1
		);
		$update->set($newData);
		$update->where('customers_rapiddo_id = '.$data["id"]);
		$selectString = $sql->getSqlStringForSqlObject($update);
		$resultUpdate = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		return $resultUpdate;
	}
	
	
	public function setjobs($data,$workers_rapiddo_id){
		$sql = new Sql($this->db);
		$insert = $sql->insert('jobs');
		$newData = array(
		
			'jobs_id'=> null,
			'jobs_rapiddo_id'=> $data["id"],
			'workers_rapiddo_id'=> $workers_rapiddo_id,
			'jobs_accountCode'=> $data["accountCode"],
			'jobs_clientId'=> $data["clientId"],
			'jobs_companyId'=> $data["companyId"],
			'jobs_creationDate'=> $data["creationDate"],
			'jobs_modificationDate'=> $data["modificationDate"],
			'jobs_status'=> $data["status"],
			'jobs_expectedTime'=> $data["expectedTime"],
			'jobs_expectedDistance'=> $data["expectedDistance"],
			'jobs_paymentDate'=> $data["paymentDate"],
			'jobs_assignmentDate'=> $data["assignmentDate"],
			'jobs_serviceAssignmentDate'=> $data["serviceAssignmentDate"],
			'jobs_finishDate'=> $data["finishDate"],
			'jobs_startDate'=> $data["startDate"],
			'jobs_scheduled'=> $data["scheduled"],
			'jobs_channel'=> $data["channel"],
			'jobs_deliveryPoints'=> $data["deliveryPoints"],

		);
		$insert->values($newData);
		$selectString = $sql->getSqlStringForSqlObject($insert);
		$results = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$lastInsert = $this->db->getDriver()->getLastGeneratedValue();
		if($results){
			return $lastInsert;
		}else{
			return $results;
		}
	}


	public function setJobValues($data,$jobs_rapiddo_id){
		$sql = new Sql($this->db);
		$insert = $sql->insert('jobvalues');
		$newData = array(
			
			'jobValues_id'=> null,
			'jobs_rapiddo_id'=> $jobs_rapiddo_id,
			'jobvalues_totalPrice'=> $data["totalPrice"],
			'jobvalues_totalPriceAddition'=> $data["totalPriceAddition"],
			'jobvalues_totalPriceDiscount'=> $data["totalPriceDiscount"],
			'jobvalues_totalCost'=> $data["totalCost"],
			'jobvalues_totalCostAddition'=> $data["totalCostAddition"],
			'jobvalues_totalCostDiscount'=> $data["totalCostDiscount"],

		);
		$insert->values($newData);
		$selectString = $sql->getSqlStringForSqlObject($insert);
		$results = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$lastInsert = $this->db->getDriver()->getLastGeneratedValue();
		if($results){
			return $lastInsert;
		}else{
			return $results;
		}
	}



	public function setJobItemList($data,$jobs_rapiddo_id){
		$sql = new Sql($this->db);
		$insert = $sql->insert('jobitemlist');
		$newData = array(
			
			'jobitemlist_id'=> null,
			'jobs_rapiddo_id'=> $jobs_rapiddo_id,
			'jobitemlist_lat'=> $data["lat"],
			'jobitemlist_lon'=> $data["lon"],
			'jobitemlist_status'=> $data["status"],
			'jobitemlist_checkinDate'=> $data["checkinDate"],
			'jobitemlist_finishDate'=> $data["finishDate"],
			'jobitemlist_expectedMinDate'=> $data["expectedMinDate"],
			'jobitemlist_expectedMaxDate'=> $data["expectedMaxDate"],
			'jobitemlist_geoAddress'=> $data["geoAddress"],
			'jobitemlist_expectedTime'=> $data["expectedTime"],
			'jobitemlist_expectedDistance'=> $data["expectedDistance"],
			'jobitemlist_orderExternalId'=> $data["orderExternalId"],
			'jobitemlist_deliverySuccess'=> $data["deliverySuccess"],
			'jobitemlist_deliveryStatus'=> $data["deliveryStatus"],
			'jobitemlist_index'=> $data["index"],
			'jobitemlist_checkinValidation'=> $data["checkinValidation"],
			'jobitemlist_checkoutValidation'=> $data["checkoutValidation"],
			'jobitemlist_checkoutLon'=> $data["checkoutLon"],
			'jobitemlist_checkoutLat'=> $data["checkoutLat"],
			'jobitemlist_description'=> $data["description"],

		);
		$insert->values($newData);
		$selectString = $sql->getSqlStringForSqlObject($insert);
		$results = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$lastInsert = $this->db->getDriver()->getLastGeneratedValue();
		if($results){
			return $lastInsert;
		}else{
			return $results;
		}
	}


	public function getJobsbyRapiddoId($jobs_rapiddo_id) {
		
		$result = $this->db->query('SELECT
									*
									FROM jobs
									WHERE jobs_rapiddo_id = ? ',array($jobs_rapiddo_id) );
		$jobs = $result->toArray();
		//Debug::dump($user);
		return $jobs;
	}


	public function getJobsByWorkersRapiddoIdAndRateType($workers_rapiddo_id,$rateTypeId,$datefrom,$dateto) {
		
		if($rateTypeId == 1){
			$groupByJobs="GROUP BY jil.jobs_rapiddo_id";
			//$groupByJobs="";
		}elseif($rateTypeId == 2 && $rateTypeId == 3){
			$groupByJobs="";
		}else{
			$groupByJobs="";
		}
		
		$result = $this->db->query("SELECT 

									j.jobs_rapiddo_id, 
									j.jobs_clientId,
									j.jobs_companyId,
									j.jobs_creationDate,
									j.jobs_assignmentDate,
									j.jobs_status,
									j.jobs_expectedDistance,
									j.jobs_channel,
									j.jobs_deliveryPoints,

									jil.jobitemlist_status,
									jil.jobitemlist_expectedDistance,
									jil.jobitemlist_orderExternalId,
									jil.jobitemlist_deliverySuccess,
									jil.jobitemlist_deliveryStatus,
									jil.jobitemlist_index,
									jil.jobitemlist_description,

									jv.jobvalues_totalPrice,
									jv.jobvalues_totalPriceAddition,
									jv.jobvalues_totalPriceDiscount,
									jv.jobvalues_totalCost,
									jv.jobvalues_totalCostAddition,
									jv.jobValues_totalCostDiscount,
									
									c.customers_rapiddo_id,
									c.customers_company_id

									FROM jobs j
									INNER JOIN jobitemlist jil ON jil.jobs_rapiddo_id = j.jobs_rapiddo_id
									INNER JOIN jobvalues jv ON jv.jobs_rapiddo_id = j.jobs_rapiddo_id
									INNER JOIN customers c ON c.customers_rapiddo_id = j.jobs_clientId
									
									WHERE workers_rapiddo_id = ?  AND jobitemlist_index <> 0
									AND c.ratetype_id = ?
									AND j.jobs_creationDate BETWEEN ? AND ? 
									".$groupByJobs,
									array($workers_rapiddo_id,$rateTypeId,$datefrom,$dateto) );
		$jobs = $result->toArray();
		//Debug::dump($jobs);
		return $jobs;
	}

	public function setWorkersPay($data){
		$sql = new Sql($this->db);
		$insert = $sql->insert('workerspay');
		$newData = array(
			
			'workerspay_id'=> null,
			'workers_rapiddo_id'=> $data["workers_rapiddo_id"],
			'jobs_rapiddo_id'=> $data["jobs_rapiddo_id"],
			'workerspay_value'=> $data["workerspay_value"],
			'workerspay_creationdate'=> $data["workerspay_creationdate"],
			'workerspay_assignmentdate'=> $data["workerspay_assignmentDate"],
			'workerspay_week'=> $data["workerspay_week"],
			'workerspay_status'=> 1,

		);
		$insert->values($newData);
		$selectString = $sql->getSqlStringForSqlObject($insert);
		$results = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$lastInsert = $this->db->getDriver()->getLastGeneratedValue();
		if($results){
			return $lastInsert;
		}else{
			return $results;
		}
	}
	
	public function updateWorkersPayValueByJobsRapiddoId($jobs_rapiddo_id,$rate){
		$sql = new Sql($this->db);
		$update = $sql->update('workerspay');
		$newData = array(
			'workerspay_value'=> $rate,
		);
		$update->set($newData);
		$update->where('jobs_rapiddo_id = '.$jobs_rapiddo_id);
		$selectString = $sql->getSqlStringForSqlObject($update);
		$resultUpdate = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		return $resultUpdate;
	}
	
	public function getRateRanges($jobs_clientId) {
		
		$result = $this->db->query('SELECT * 
									FROM customers c 
									INNER JOIN rateranges r ON r.customers_company_id=c.customers_company_id
									WHERE c.customers_rapiddo_id =  ? ',array($jobs_clientId) );
		$jobs = $result->toArray();
		//Debug::dump($user);
		return $jobs;
	}

	
	public function getRateFixed($jobs_clientId) {
		
		$result = $this->db->query('SELECT * 
									FROM customers c 
									INNER JOIN ratefixed r ON r.customers_company_id=c.customers_company_id
									WHERE c.customers_rapiddo_id = ? ',array($jobs_clientId) );
		$jobs = $result->toArray();
		//Debug::dump($user);
		return $jobs;
	}

	
	public function getWorkerspayByWorkerRapiddoId($workersRapiddoId,$dateFrom,$dateTo) {
		
		$result = $this->db->query("SELECT *
									FROM workerspay 
									WHERE workers_rapiddo_id = ? AND workerspay_creationdate BETWEEN ? AND ?
									",
									array($workersRapiddoId,$dateFrom,$dateTo) );
		$jobs = $result->toArray();
		//Debug::dump($jobs);
		return $jobs;
	}
	
	
	public function getJobsByJobsRapiddoId($jobsRapiddoId) {
		
		$result = $this->db->query("SELECT * FROM workerspay WHERE jobs_rapiddo_id = ? ",
									array($jobsRapiddoId) );
		$jobs = $result->toArray();
		//Debug::dump($jobs);
		return $jobs;
	}
	
	public function getCustomerById($customerId) {
		$result = $this->db->query('SELECT *
			FROM customers
			WHERE customers_rapiddo_id = ?
			AND customers_status = 1',array($customerId));
		return $result->toArray();
	}


	public function calculatePaymentWorkerbyDate($week) {

	$result = $this->db->query('SELECT w.workers_fullname, w.workers_rapiddo_id,
								(select SUM(workerspay_value)
									FROM workerspay
									WHERE workers_rapiddo_id =wp.workers_rapiddo_id
									AND workerspay_week = ? ) AS totalpayment
								FROM workerspay wp 
								INNER JOIN workers w ON w.workers_rapiddo_id = wp.workers_rapiddo_id
								WHERE wp.workerspay_week = ?
								GROUP BY wp.workers_rapiddo_id'
			, array($week,$week));
		$total = $result->toArray();
		return $total;
	}
	
	
	
	
	public function getGuaranteedhoursByWorkerIdAndDate($workers_rapiddo_id,$datefrom,$dateto) {
		
		
		$result = $this->db->query("SELECT 
									g.workers_rapiddo_id,
									g.guaranteedhours_starttime,
									g.guaranteedhours_endtime,
									g.guaranteedhours_timetotal,
									g.guaranteedhours_status,
									w.workers_fullname

									FROM guaranteedhours g
									INNER JOIN workers w ON g.workers_rapiddo_id = w.workers_rapiddo_id
									
									WHERE g.workers_rapiddo_id = ?  AND g.guaranteedhours_status = 1
									AND g.guaranteedhours_starttime BETWEEN ? AND ? ",
									array($workers_rapiddo_id,$datefrom,$dateto) );
		$jobs = $result->toArray();
		//Debug::dump($jobs);
		return $jobs;
	}
	
	
	
	
	public function setguaranteedhours($data){
		
		$sql = new Sql($this->db);
		$insert = $sql->insert('guaranteedhours');
		$newData = array(
			'guaranteedhours_id'=> null,
			'workers_rapiddo_id'=> $data["workerId"],
			'guaranteedhours_starttime'=> $data["datetimefrom"],
			'guaranteedhours_endtime'=> $data["datetimeto"],
			'guaranteedhours_timetotal'=> $data["time"],
			'guaranteedhours_status'=> 1
		);
		$insert->values($newData);
		$selectString = $sql->getSqlStringForSqlObject($insert);
		$results = $this->db->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$lastInsert = $this->db->getDriver()->getLastGeneratedValue();
		if($results){
			return $lastInsert;
		}else{
			return $results;
		}
	}
	
	public function getWorkersCount() {
		$result = $this->db->query('
		SELECT count(workers_id) AS total
		FROM workers', Adapter::QUERY_MODE_EXECUTE);
		return $result->toArray();
	}
	
	
	
	
	public function getWorkerspaginate($page = 1, $elementosPorPagina = 20) {
		
		$inicio = ($page-1)*$elementosPorPagina;
		
		$query='SELECT *
						FROM workers w
						LEFT JOIN cities c ON c.cities_id = w.cities_id
						WHERE workers_status = 1 order by workers_id LIMIT '.$inicio.', '.$elementosPorPagina;
		
		$result = $this->db->query($query, Adapter::QUERY_MODE_EXECUTE);
		return $result->toArray();
	}
	
	
	public function getWorkersBySearch($searchAttrs, $page = 1, $elementosPorPagina = 20) {
		
		$inicio = ($page-1)*$elementosPorPagina;


		if (trim($searchAttrs["worker_date_from"]) !== '' && trim($searchAttrs["worker_date_to"]) !== '') {

			$workers_creation_date = '';
			$workers_creation_date .= " AND ( workers_creation_date BETWEEN '".$searchAttrs["worker_date_from"]." 00:00:00' AND '".$searchAttrs["worker_date_to"]." 23:59:59' ) ";

		} else {
			$workers_creation_date = '';
		}

		if (trim($searchAttrs["worker_name"]) !== '') {

			$workers_fullname = '';
			$workers_fullname .= " AND ( workers_fullname LIKE '%".$searchAttrs["worker_name"]."%' ) ";

		} else {
			$workers_fullname = '';
		}

		if (trim($searchAttrs["worker_mail"]) !== '') {

			$workers_email = '';
			$workers_email .= " AND ( workers_email LIKE '%".$searchAttrs["worker_mail"]."%' ) ";

		} else {
			$workers_email = '';
		}

		if (trim($searchAttrs["city"]) !== '') {

			$cities_id = '';
			$cities_id .= " AND ( c.cities_id = '".$searchAttrs["city"]."' ) ";

		} else {
			$cities_id = '';
		}

		
		$query="SELECT *
						FROM workers w
						LEFT JOIN cities c ON c.cities_id = w.cities_id
						WHERE workers_status = 1 
		".$workers_fullname."
		".$workers_creation_date."
		".$workers_email."
		".$cities_id."
		
		order by workers_id LIMIT ".$inicio.", ".$elementosPorPagina;
		
		$result = $this->db->query($query, Adapter::QUERY_MODE_EXECUTE);
		return $result->toArray();
	}
	
	/*
	public function calcTimeOrder($oid) {
		$result = $this->db->query(' SELECT bko_orders_startdatetime, bko_orders_completeddatetime, bko_orders_distance, SEC_TO_TIME( TIMESTAMPDIFF(
SECOND , bko_orders_startdatetime, bko_orders_completeddatetime ) ) HORAS FROM bko_orders WHERE bko_orders_id = ? ', array($oid));
		$time = $result->toArray();
		return $time;
	}*/
	
	

}