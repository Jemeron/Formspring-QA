<?php

class Formspring {
	
	private $user;
	private $options = array();
	private $lastId;
	private $error = false;
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- INITIALIZATION -----------------------
	// >>	1.	Username in Formspring
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public function __construct($user)
	{
		$this->user = $user;
		
		$this->options = array(
								'token'			=> '',
								'user_agent'	=> $_SERVER['HTTP_USER_AGENT'],
								'limit'			=> 20
							);
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- SET AN OPTION ------------------------
	// >>	1.	Associative array with options
	//		--- or ---
	//		1.	Option name
	//		2.	Option value
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public function setOpt()
	{
		$numArgs = func_num_args();
		$args = func_get_args();
		
		if ($numArgs == 1)
		{
			foreach ($args[0] as $key => $value)
			{
				$this->options[$key] = $value;
			}
		}
		else
		{
			$this->options[$args[0]] = $args[1];
		}
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- LOAD 20 RECORDS ----------------------
	// >>	1.	Start loading records from the 
	//			specified id number
	// <<	+.	Array with records
	//		-.	False
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	private function load($before = '')
	{
		if ($before != '') $before = '/?before='.$before;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://beta-api.formspring.me/answered/list/'.$this->user.$before);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$data = json_decode(curl_exec($ch));
		
		curl_close($ch);
		
		if ($data->status != 'error')
		{
			$this->lastId = $data->response[19]->id;
			return $data->response;	
		}
		else
		{
			$this->error = $data->error;
			return false;
		}
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- GET THE ANSWERS ----------------------
	// >>	1.	Start loading records from the 
	//			specified id number
	// <<	+.	Array with records
	//		-.	False
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public function get($before = '')
	{
		$data = array();
		if ($this->options['limit'] != 20)
		{
			$loads = intval($this->options['limit'] / 20) + 1;
			
			for ($i = 0; $i < $loads; $i++)
			{
				$response = $this->load($before);
				
				if (! $this->error)
				{
					$data = array_merge($data, $response);
					$before = $this->lastId;
				}
				else
				{
					break;
					return false;
				}	
			}
			
			$data = array_slice($data, 0, $this->options['limit']);
			
			$lastKey = $this->options['limit'] - 1;
			$this->lastId = $data[$lastKey]->id;
		}
		else
		{
			$data = $this->load($before);
		}
		
		return $data;
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- ASK ANONYMOUSLY ----------------------
	// >>	1.	Text of the question (255 chars)
	// <<	+.	Id of created question
	//		-.	False
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public function ask($question)
	{
		$ch = curl_init('http://www.formspring.me/profile/ask/'.$this->user);

		$data = array(
					'token'						=> $this->options['token'],
					'question' 					=> $question,
					'ajax'						=> 1
				);

		curl_setopt($ch, CURLOPT_URL, 'http://www.formspring.me/profile/ask/'.$this->user);
		curl_setopt($ch, CURLOPT_REFERER, 'http://www.formspring.me/'.$this->user);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_USERAGENT, $this->options['user_agent']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$res = json_decode(curl_exec($ch));

		curl_close($ch);
		
		if ($res !== FALSE && $res->stat != 'fail')
		{
			return $res->question_id;
		}
		else if ($res->stat == 'fail')
		{
			$this->error = $res->error;
			return false;
		}
		else
		{
			$this->error = curl_error($ch);
			return false;
		}
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- GET A NUMBER OF ANSWERED QUESTIONS ---
	// <<	+.	The number of answered questions
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public function total()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://beta-api.formspring.me/answered/count/'.$this->user);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$res = json_decode(curl_exec($ch));
		
		curl_close($ch);
		return $res->response->count;
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- GET ID OF THE LAST SHOWN RECORD ------
	// <<	+.	Record id
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public function lastId()
	{
		return $this->lastId;
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- GET THE ERROR ------------------------
	// <<	+.	Text of the error
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public function error()
	{
		return $this->error;
	}
	
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// --- GET USER'S TOKEN ---------------------
	// >>	1.	Username in Formspring
	// <<	+.	Token string
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	public static function getToken($user)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://www.formspring.me/'.$user);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		preg_match('/name=\"token\" value=\"(.*?)\"/', curl_exec($ch), $m);
		
		curl_close($ch);
		
		return $m[1];
	}
	
}

?>