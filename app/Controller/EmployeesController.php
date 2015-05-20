
<?php

App::uses('AppController', 'Controller');

class EmployeesController extends AppController {

	public function index() {
		$this->layout = 'main';
	}

	public function getEmployees() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('Employee');
			$joins = array(
							        array(
							            'table' => 'profiles',
							            'conditions' => array(
							                'Employee.profile_id = profiles.id'
							            )
							       	 	),
							        array(
													'table' => 'company_systems',
													'type' => 'LEFT',
													'conditions' => array(
															'Employee.company_system_id = company_systems.id'
													)
												),
											array(
													'table' => 'positions',
													'type' => 'LEFT',
													'conditions' => array(
															'Employee.position_id = positions.id'
													)
												),
											array(
													'table' => 'position_levels',
													'type' => 'LEFT',
													'conditions' => array(
															'Employee.position_level_id = position_levels.id'
													)
												),
											array(
													'table' => 'contract_logs',
													'type' => 'LEFT',
													'conditions' => array(
															'Employee.id = contract_logs.employees_id'
													)
												)
											);
			$conditions = array("concat(profiles.first_name, ' ',profiles.middle_name,' ',profiles.last_name) LIKE '%" . $this->request->data['value'] . "%' and Employee.status != 0");
			switch($this->request->data['field']) {
				case "name":
					$conditions = array("concat(profiles.first_name, ' ',profiles.middle_name,' ',profiles.last_name) LIKE '%" . $this->request->data['value'] . "%' and Employee.status != 0");
				break;
				case "employee_id":
					$conditions = array("employee_id LIKE '%" . $this->request->data['value'] . "%' and Employee.status != 0");
				break;
				case "position":
					if ($this->request->data['value']) {
						$positionLevelCondition = "";
						if ($this->request->data['position_level']) {
							$positionLevelCondition = "and position_levels.description = '" . $this->request->data['position_level'] . "'";
						}
						$conditions = array("positions.description = '" . $this->request->data['value'] . "' $positionLevelCondition and Employee.status != 0");
					}
				break;
				case "status":
					$conditions = array("Employee.status = '" . $this->request->data['value'] . "' and Employee.status != 0");
				break;
			}
			$employees = $this->Employee->find('all',array(
																						'joins' => $joins,
																						'conditions' => $conditions,
																						'fields' => array('*')
																						)
																				);
			$employees_arr = array();
			foreach($employees as $key => $employee) {
			$status = ($employee['Employee']['status'] == 1) ? "Inactive" : "Active";
				$data = array(
										'id' => $employee['Employee']['id'],
										'name' => $employee['profiles']['first_name']. " " . $employee['profiles']['middle_name'] . " " .$employee['profiles']['last_name'],
										'employee_id' => $employee['Employee']['employee_id'],
										'company_system' => $employee['company_systems']['name'],
										'username' => $employee['Employee']['username'],
										'password' => $employee['Employee']['password'],
										'tin' => $employee['Employee']['tin'],
										'salary' => $employee['Employee']['salary'],
										'drug_test' => $employee['Employee']['drug_test'],
										'pagibig' => $employee['Employee']['pagibig'],
										'philhealth' => $employee['Employee']['philhealth'],
										'medical' => $employee['Employee']['medical'],
										'sss' => $employee['Employee']['sss'],
										'insurance_id' => $employee['Employee']['insurance_id'],
										'position' => $employee['positions']['description'],
										'position_level' => $employee['position_levels']['description'],
										'contract' => $employee['contract_logs']['description'],
										'f_time_in' => $this->convertTimeToMilitary($employee['Employee']['f_time_in']),
										'f_time_out' => $this->convertTimeToMilitary($employee['Employee']['f_time_out']),
										'l_time_in' => $this->convertTimeToMilitary($employee['Employee']['l_time_in']),
										'l_time_out' => $this->convertTimeToMilitary($employee['Employee']['l_time_out']),
										'role' => $employee['Employee']['role'],
										'status' => $status
									);
				array_push($employees_arr,$data);
			}
			if (!$employees_arr) {
				$data = array(
										'id' => null,
										'employee_id' => null,
										'company_system' => null,
										'name' => null,
										'username' => null,
										'password' => null,
										'tin' => null,
										'salary' => null,
										'drug_test' => null,
										'pagibig' => null,
										'philhealth' => null,
										'medical' => null,
										'sss' => null,
										'insurance_id' => null,
										'position' => null,
										'position_level' => null,
										'contract' => null,
										'f_time_in' => null,
										'f_time_out' => null,
										'l_time_in' => null,
										'l_time_out' => null,
										'role' => null,
										'status' => null
									);
				array_push($employees_arr,$data);
			}
			echo json_encode($employees_arr);
		}
	}

	public function convertTimeToMilitary($time = '') {
		if ($time) {
			$this->autoRender = false;
			$split_time = split(':',$time);
			$hours = (int)$split_time[0];
			$minutes = $split_time[1];
			$period = 'AM';
			if ($hours >= 12) {
				if ($hours > 12) {
					$hours -= 12;
					$period = 'PM';
				}
			}
			if ($hours == 12 && $period === 'AM') {
				$period = 'PM';
			}
			$time = $hours.':'.$minutes.' '.$period;
			return $time;
		}
	}

	function convertTimeToDefault($time = '') {
		if ($time) {
			$time_split = split(':',$time);
			$hours = (int)$time_split[0];
			$minutes = $time_split[1];
			$time_split = split(' ',$time);
			$period = $time_split[1];
			if ($period === 'PM' && $hours !== 12) {
				$hours += 12;
			}
			if ($hours < 10) {
				$hours = '0'.$hours;
			}
			if ($hours == '00') {
				$hours = '12';
			}
			$time = $hours.':'.$minutes;
			return $time;
		}
	}

	public function getDropdownValues() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$json['names'] = $this->getNameLists();
			$json['companies'] = $this->getCompanyLists();
			$json['positions'] = $this->getPositionLists();
			$json['positionLevels'] = $this->getPositionLevelLists();
			echo json_encode($json);
		}
	}

	public function getNameLists() {
		$this->autoRender = false;
		$this->loadModel('Profiles');
		$employees = $this->Profiles->find('all',array(
														'conditions' => array("id not in (Select profile_id from employees)"),
														'fields' => array("first_name","middle_name","last_name")
													)
												);
		$names = array();
		foreach($employees as $employee) {
			$name =  $employee['Profiles']['first_name'] . " " . $employee['Profiles']['middle_name'] . " " . $employee['Profiles']['last_name'];
			array_push($names,$name);
		}
		return $names;
	}

	public function getCompanyLists() {
		$this->autoRender = false;
		$this->loadModel('Company_system');
		$companies = $this->Company_system->find('list',array(
																										'conditions' => array("status = '1'"),
																										'fields' => array('name')
																										)
																									);
		$company_lists = array();
		foreach($companies as $company) {
			array_push($company_lists,$company);
		}
		return $company_lists;
	}

	public function getPositionLists() {
		$this->autoRender = false;
		$this->loadModel('Position');
		$positions = $this->Position->find('all');
		$position_arr = array();
		foreach($positions as $position) {
			array_push($position_arr,$position['Position']['description']);
		}
		return $position_arr;
	}

	public function getPositionLevelLists() {
			$this->autoRender = false;
			$this->loadModel('Position');
			$this->loadModel('Position_level');
			$level_arr = array();
			$levels = $this->Position_level->find('all',array(
																										'fields' => array('distinct(description)')
																										)
																									);
			foreach($levels as $level) {
				array_push($level_arr,$level['Position_level']['description']);
			}
			return $level_arr;
	}

	public function validateFields() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$employee = $this->request->data['employee'];
			$this->loadModel('Employee');
			$this->Employee->set($employee);
			$validate = $this->Employee->validates();
			$errors = $this->Employee->validationErrors;
			echo json_encode($errors);
		}
	}

	function addEmployee() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$employee = $this->request->data['employee'];
			$this->loadModel('Employee');
			$this->loadModel('Profile');
			$this->loadModel('Position');
			$this->loadModel('Position_level');
			$this->loadModel('Profile');
			$validatedFields = array();
			$employeeInfo = $this->Profile->find('first',array(
															'conditions' => array("concat(first_name,' ',middle_name,' ',last_name) = '$employee[name]'")
														)
													);
			if ($employeeInfo) {
				$saveData = array();
				foreach($employee as $key => $detail) {
					$field = $key;
					$value = $detail;
					if ($key === 'position' || $key === 'position_level') {
						$value = "";
						$field = $field."_id";
						switch($key) {
							case 'company_system' :
								$company = $this->Company_system->findByName($employee['value']);
								if ($company) {
									$value = $company['Company_system']['id'];
								}
							break;
							case 'position' :
								$searchPosition = $this->Position->findByDescription($value);
								if ($searchPosition) {
									$value = $searchPosition['Position']['id'];
								}
							break;
							case 'position_level' :
								$searchPositionLevel = $this->Position_level->findByPositions_idAndDescription(1,$value);
								if ($searchPositionLevel) {
									$value = $searchPositionLevel['Position_level']['id'];
								}
							break;
						}
					}
					$data = array(
								$key => $value
							);
					if ($key !== 'name' && $key !== 'contract' && $key !== 'id') {
						array_push($validatedFields,$key);
						$this->Employee->set($data);
						if ($this->Employee->validates()) {
							$saveData[$field] = $value;
						}
					}
				}
				$employeeInfo = $employeeInfo['Profile'];
				$status = 1;
				if($employee['status'] === 'Active') {
					$status = 2;
				}
				$saveData['status'] = $status;
				$saveData['profile_id'] = $employeeInfo['id'];
				$this->Employee->validationErrors = array();
				foreach($validatedFields as $field) {
					if ($field !== 'employee_id') {
						$this->Employee->validator()->remove($field);	
					}
				}
				$success = $this->Employee->save($saveData);
				if ($success) {
					$employeeInfo = $this->Employee->findByEmployee_id($employee['employee_id']);
					$employeeInfo = $employeeInfo['Employee'];
					$json['id'] = $employeeInfo['id'];
				} else {
					$success = false;
				}
				$json['success'] = $success;
				echo json_encode($json);
			}
		}
	}

	public function saveAll() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$employees = $this->request->data['employees'];
			$this->loadModel('Employee');
			$this->loadModel('Company_system');
			$this->loadModel('Position');
			$this->loadModel('Position_level');
			$error_arr = array();
			foreach($employees as $employee) {
				$field = $employee['field'];
				$value = $employee['value'];
				if ($field === 'status') {
					$value = "";
					if (strtolower($employee['value']) === 'inactive') {
						$value = 1;
					} else if (strtolower($employee['value']) === 'active') {
						$value = 2;
					}
				}
				if ($field === 'f_time_in' || $field === 'f_time_out' || $field === 'l_time_in' || $field === 'l_time_out') {
					$value = $this->convertTimeToDefault($value);
				}
				if ($field === 'company_system' || $field === 'position' || $field === 'position_level') {
					$value = "";
					$field = $field."_id";
					switch($employee['field']) {
						case 'company_system' :
							$company = $this->Company_system->findByName($employee['value']);
							if ($company) {
								$value = $company['Company_system']['id'];
							}
						break;
						case 'position' :
							$searchPosition = $this->Position->findByDescription($employee['value']);
							if ($searchPosition) {
								$value = $searchPosition['Position']['id'];
							}
						break;
						case 'position_level' :
							$searchPositionLevel = $this->Position_level->findByPositions_idAndDescription(1,$employee['value']);
							if ($searchPositionLevel) {
								$value = $searchPositionLevel['Position_level']['id'];
							}
						break;
					}
				}
				if($field === 'password') {
					$value = Security::hash($value,'sha1',true);
				}
				$data = array(
							$field => $value
						);
				$this->Employee->id = $employee['id'];
				if(!$this->Employee->save($data)) {
					array_push($error_arr,array(
															'field' => $field,
															'value' => $value
																)
															);
				}
			}
			$json['errors'] = $error_arr;
			echo json_encode($json);
		}
	}

	public function updateAdditionInfo() {
		if($this->request->is('ajax')) {
			$this->autoRender = false;
			$employee = $this->request->data['employee'];
			$data = array(
									'tin' => $employee['tin'],
									'drug_test' => $employee['drug_test'],
									'medical' => $employee['medical'],
									'pagibig' => $employee['pagibig'],
									'sss' => $employee['sss'],
									'philhealth' => $employee['philhealth'],
									'insurance_id' => $employee['insurance_id'],
									'f_time_in' => $employee['f_time_in'],
									'f_time_out' => $employee['f_time_out'],
									'l_time_in' => $employee['l_time_in'],
									'l_time_out' => $employee['l_time_out'],
									'username' => $employee['username']
								);
			if(isset($employee['salary'])) {
				$data['salary'] = $employee['salary'];
			}
			if($employee['password'] !== 'company_default_password') {
				$data['password'] = Security::hash($employee['password'],'sha1',true);
			}
			$this->Employee->id = $employee['id'];
			$txtErrors = "";
			if(!$this->Employee->save($data)) {
				$errors = $this->Employee->validationErrors;
				$x = 0 ;
				foreach ($errors as $key => $error) {
					$txtErrors .= ($x === 0) ? $errors[$key][0] : ",<br>".$errors[$key][0];
					$x++;
				}
			}
			echo json_encode($txtErrors);
		}
	}

	function deleteEmployee() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('Employee');
			$status = array('status' => 0);
			$this->Employee->id = $this->request->data['id'];
			$success = $this->Employee->save($status);
			echo json_encode($success);
		}
	}

}