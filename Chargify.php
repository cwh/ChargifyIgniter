<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ChargeIgniter
 *
 * A Chargify API class for CodeIgniter
 *
 * @author		Kyle Anderson <kyle@chargeigniter.com>
 * @link		http://www.chargeigniter.com
 */

class Chargify {
	protected $username 	= '';		// Chargify API Key
	protected $domain 	= '';		// Chargify Subdomain
	protected $password 	= 'x';
	
	public function get_customer($customer_id, $source = 'remote') {
		switch($source) {
			case 'remote':
				$result = $this->query('/customers/'.$customer_id.'.json');
			break;
			case 'local':
				$result = $this->query('/customers/lookup.json?reference='.$customer_id);
			break;
		}
		
		if($result->code == 200) {
			$customer = json_decode($result->response);
			
			if(count($customer) == 1) {
				return $customer->customer;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function get_customers($page_number = 1) {
		$result = $this->query('/customers.json?page='.$page_number);
		
		if($result->code == 200) {
			$customers = json_decode($result->response);
			
			if(count($customers) > 0) {
				foreach($customers as $customer) {
					$temp[] = $customer->customer;
				}
				
				return $temp;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function create_customer($data) {
		$data = array(
			'customer' => $data
		);
		
		$result = $this->query('/customers.json', 'post', $data);
		
		if($result->code == 201) {
			$customer = json_decode($result->response);
			
			if(count($customer) == 1) {
				return $customer->customer;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function edit_customer($customer_id, $data) {
		$data = array(
			'customer' => $data
		);
		
		$result = $this->query('/customers/'.$customer_id.'.json', 'put', $data);
		
		if($result->code == 200) {
			$customer = json_decode($result->response);
			
			if(count($customer) == 1) {
				return $customer->customer;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	public function delete_customer($customer_id) {
		return $this->query('/customers/'.$customer_id.'.json', 'delete');
	}
	
	public function get_customer_subscriptions($customer_id) {
		$result = $this->query('/customers/'.$customer_id.'/subscriptions.json');
		
		if($result->code == 200) {
			$subscriptions = json_decode($result->response);
			
			if(count($subscriptions) > 0) {
				return $subscriptions;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	/**************************************************************************************************************
	 Products
	***************************************************************************************************************/
	
	public function get_product($product_id, $source = 'remote') {
		switch($source) {
			case 'remote':
				$result = $this->query('/products/'.$product_id.'.json');
			break;
			case 'local':
				$result = $this->query('/products/handle/'.$product_id.'.json');
			break;
		}
		
		if($result->code == 200) {
			$product = json_decode($result->response);
			
			if(count($product) == 1) {
				return $product->product;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function get_products() {
		$result = $this->query('/products.json');
		
		if($result->code == 200) {
			$products = json_decode($result->response);
			
			if(count($products) > 0) {
				foreach($products as $product) {
					$temp[] = $product->product;
				}
				
				return $temp;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	/**************************************************************************************************************
	 Subscriptions
	***************************************************************************************************************/
	
	public function get_subscription($subscripton_id) {
		$result = $this->query('/subscriptions/'.$subscripton_id.'.json');
		
		if($result->code == 200) {
			$subscription = json_decode($result->response);
			
			if(count($subscription) == 1) {
				return $subscription->subscription;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function get_subscriptions($page_number = 1, $results_per_page = 2000) {
		$result = $this->query('/subscriptions.json?page='.$page_number.'&per_page='.$results_per_page);
		
		if($result->code == 200) {
			$subscriptions = json_decode($result->response);
			
			if(count($subscriptions) > 0) {
				return $subscriptions;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function create_subscription($data) {
		$data = array(
			'subscription' => $data
		);
		
		$result = $this->query('/subscriptions.json', 'post', $data);
		
		if(isset($result->subscription)) {
			return $result->subscription;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function edit_subscription($subscripton_id, $data) {
		$data = array(
			'subscription' => $data
		);
		
		$result = $this->query('/subscriptions/'.$subscripton_id.'.json', 'put', $data);
		
		if($result->code == 200) {
			$subscription = json_decode($result->response);
			
			if(count($subscription) == 1) {
				return $subscription->subscription;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	public function upgrade_subscription($subcription_id, $data) {
		$data = array(
			'subscription' => $data
		);
		
		$result = $this->query('/subscriptions/'.$subscription_id.'/migrations.json', 'post', $data);
		
		if($result->code == 200) {
			$subscription = json_decode($result->response);
			
			if(count($subscription) == 1) {
				return $subscription->subscription;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	public function cancel_subscription($subscripton_id, $message = '') {
		if(!empty($message)) {
			$data = array(
				'subscription' => array(
					'cancellation_message' => $message
				)
			);
			
			$result = $this->query('/subscriptions/'.$subscripton_id.'.json', 'delete', $data);
		} else {
			$result = $this->query('/subscriptions/'.$subscripton_id.'.json', 'delete');
		}
		
		if($result->code == 200) {
			$subscription = json_decode($result->response);
			
			if(count($subscription) == 1) {
				return $subscription->subscription;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function reactivate_subscription($subscription_id) {
		$result = $this->query('/subscriptions/'.$subscription_id.'/reactivate.json', 'put');
		
		if($result->code == 200) {
			$subscription = json_decode($result->response);
			
			if(count($subscription) == 1) {
				return $subscription->subscription;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function reset_subscription($subscription_id) {
		$result = $this->query('/subscriptions/'.$subscription_id.'/reset_balance.json', 'put');
		
		if($result->code == 200) {
			$subscription = json_decode($result->response);
			
			if(count($subscription) == 1) {
				return $subscription->subscription;
			}
			
			return false;
		}
		
		return $this->error($result->response, $result->code);
	}
	
	public function adjust_subscription($subscription_id, $data) {
        $data = array(
            'adjustment' => $data
        );
            
        $result = $this->query('/subscriptions/' . $subscription_id . '/adjustments.json', 'post', $data);

        if ($result->code == 200) {
            $subscription = json_decode($result->response);

            if (count($subscription) == 1) {
                return $subscription->subscription;
            }

            return false;
        }
//        return $result->response;

        $this->error($result->response, $result->code); 
    }
	
	/**************************************************************************************************************
	 Charges
	***************************************************************************************************************/
	
	public function create_charge($subscripton_id, $data) {
		$data = array(
			'charge' => $data
		);
		
		$result = $this->query('/subscriptions/'.$subscripton_id.'/charges.json', 'post', $data);
		
		if($result->code == 201) {
			$charge = json_decode($result->response);
			
			if(count($charge) == 1) {
				return $charge->charge;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	/**************************************************************************************************************
	 Coupons
	***************************************************************************************************************/
	
	public function get_coupon($product_family_id, $coupon, $find_by_id = true) {
		if(is_int($coupon) && $find_by_id == true) {
			$result = $this->query('/product_families/'.$product_family_id.'/coupons/'.$coupon.'.json');
		} else {
			$result = $this->query('/product_families/'.$product_family_id.'/coupons/find.json?code='.urlencode($coupon));
		}
		
		if($result->code == 200) {
			$coupon = json_decode($result->response);
			
			if(count($coupon) == 1) {
				return $coupon->coupon;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	/**************************************************************************************************************
	 Components
	***************************************************************************************************************/
	
	public function get_components($product_family_id) {
		$result = $this->query('/product_families/'.$product_family_id.'/components.json');
		
		if($result->code == 200) {
			$components = json_decode($result->response);
			
			if(count($components) > 0) {
				foreach($components as $component) {
					$temp[] = $component->component;
				}
				
				return $temp;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	public function create_component_usage($subscription_id, $component_id, $data) {
		$data = array(
			'usage' => $data
		);
		
		$result = $this->query('/subscriptions/'.$subscription_id.'/components/'.$component_id.'/usages.json', 'post', $data);
		
		if($result->code == 200) {
			$component = json_decode($result->response);
			
			if(count($component) == 1) {
				return $component->usage;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	public function get_component_usage($subscription_id, $component_id) {
		$result = $this->query('/subscriptions/'.$subscription_id.'/components/'.$component_id.'/usages.json');
		
		if($result->code == 200) {
			$components = json_decode($result->response);
			
			if(count($components) > 0) {
				foreach($components as $component) {
					$temp[] = $component->usage;
				}
				
				return $temp;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	
	/**************************************************************************************************************
	 Transactions
	***************************************************************************************************************/
	
	public function get_transactions($types = '', $start_id = '', $end_id = '', $start_date = '', $end_date = '', $page_number = 1, $results_per_page = 20) {
		$arguments = '';
		
		$page_number 		= (empty($page_number)) ? '1' : $page_number;
		$results_per_page 	= (empty($results_per_page)) ? '20' : $results_per_page;
		
		if(is_array($types)) {
			foreach($types as $type) {
				$arguments .= '&kinds[]='.urlencode($type);
			}
		}
		
		if(is_int($start_id)) {
			$arguments .= '&since_id='.$start_id;
		}
		
		if(is_int($end_id)) {
			$arguments .= '&max_id='.$end_id;
		}
		
		if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $start_date)) {
			$arguments .= '&since_date='.urlencode($start_date);
		}
		
		if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $end_date)) {
			$arguments .= '&until_date='.urlencode($end_date);
		}
		
		$result = $this->query('/transactions.json?page='.$page_number.'&per_page='.$results_per_page.$arguments);
		
		if($result->code == 200) {
			$transactions = json_decode($result->response);
			
			if(count($transactions) > 0) {
				foreach($transactions as $transaction) {
					$temp[] = $transaction->transaction;
				}
				
				return $temp;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	/**************************************************************************************************************
	 Credits
	***************************************************************************************************************/
	
	public function add_credit($subscription_id, $data) {
		$data = array(
			'credit' => $data
		);
		
		$result = $this->query('/subscriptions/'.$subscription_id.'/credits.json', 'post', $data);
		
		if($result->code == 201) {
			$credit = json_decode($result->response);
			
			if(count($credit) == 1) {
				return $credit->credit;
			}
			
			return false;
		}
		
		$this->error($result->response, $result->code);
	}
	
	/**************************************************************************************************************
	 Connector
	***************************************************************************************************************/
	
	protected function query($uri, $method = 'GET', $data = '') {
		$method = strtoupper($method);
		
		$curl_handler = curl_init();
		
		$options = array(
			CURLOPT_URL 				=> 'https://'.$this->domain.'.chargify.com'.$uri,
			CURLOPT_SSL_VERIFYPEER 		=> false,
			CURLOPT_SSL_VERIFYHOST 		=> 2,
			CURLOPT_FOLLOWLOCATION 		=> false,
			CURLOPT_MAXREDIRS			=> 1,
			CURLOPT_RETURNTRANSFER 		=> true,
			CURLOPT_CONNECTTIMEOUT 		=> 10,
			CURLOPT_TIMEOUT 			=> 30,
			CURLOPT_HTTPHEADER 			=> array('Content-Type: application/json', 'Accept: application/json'),
			CURLOPT_USERPWD 			=> $this->username.':'.$this->password
		);
		
		switch($method) {
			case 'POST':
				$options[CURLOPT_POST] = true;
			break;
			case 'PUT':
			case 'DELETE':
				$options[CURLOPT_CUSTOMREQUEST] = $method;
			break;
		}
		
		if($data != '') {
			$options[CURLOPT_POST] 			= true;
			$options[CURLOPT_POSTFIELDS] 	= json_encode($data);
		}
		
		curl_setopt_array($curl_handler, $options);
		
		$result = new StdClass();
		
		$result->response 	= curl_exec($curl_handler);
		$result->code 		= curl_getinfo($curl_handler, CURLINFO_HTTP_CODE);
		$result->meta 		= curl_getinfo($curl_handler);
		
		$curl_error = ($result->code > 0 ? null : curl_error($curl_handler).' ('.curl_errno($curl_handler).')');
		
		curl_close($curl_handler);
		
		if($curl_error) {
			die('An error occurred while connecting to Chargify: '.$curl_error);
		}
		
		return $result;
	}
	
	/**************************************************************************************************************
	 Error Handler
	***************************************************************************************************************/
	
	public function error($errors, $code) {
		$errors = json_decode($errors);
		
		switch($code) {
			case 401:
				$code 	= 'ERROR CODE 401: UNAUTHORIZED';
				$detail = 'API authentication has failed. Please check your API key and make sure API access is enabled.';
			break;
			case 403:
				$code 	= 'ERROR CODE 403: FORBIDDEN';
				$detail = 'A valid request was made, but the API does not have this feature enabled for use.';
			break;
			case 404:
				return false;
			break;
			case 405:
				$code 	= 'ERROR CODE 405: METHOD NOT ALLOWED';
				$detail = 'A request was made to a resource that does not support this method.';
			break;
			case 411:
				$code 	= 'ERROR CODE 411: LENGTH REQUIRED';
				$detail = 'The request did not specify the length of its content, which is required by the requested resource.';
			break;
			case 422:
				$code  	= 'ERROR CODE 422: UNPROCESSABLE ENTITY';
				$detail = 'A POST or PUT request was sent but is invalid or missing data.';
			break;
			case 500:
				$code 	= 'ERROR CODE 500: INTERNAL SERVER ERROR';
				$detail = 'A generic error message, given when no more specific message is suitable.';
			break;
			default:
				$code 	= 'ERROR CODE UNKNOWN';
				$detail = 'An error code was thrown that is not defined in the application.';
			break;
		}
		
		print '<pre>'."\n";
		print '============================================================'."\n";
		print $code."\n";
		print '============================================================'."\n";
		
		if(isset($detail)) {
			print "\n".wordwrap($detail, 60)."\n\n";
		}
		
		if(isset($errors->errors)) {
			foreach($errors->errors as $error) {
				print wordwrap($error, 60)."\n";
			}
		}
		
		print '</pre>'."\n\n";
	}
}
?>
