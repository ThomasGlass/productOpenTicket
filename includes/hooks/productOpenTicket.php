<?php
/**
Open Ticket on ordering selected products for WHMCS
Version 1.0 by TinyTunnel_Tom (ThomasGlassUK.com)
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
**/
function productOpenTicket($vars) {
	//Configuration
	$products 		= array('1','3');  		//Array of product IDs for this hook
	$adminuser 		= 'admin'; 				//Set admin user to preform action (required for internal WHMCS API)
	$departmentid 	= '1'; 					//Set support department to open ticket
	$subject 		= "Ticket Subject";  	//Subject for ticket
	$message 		= "Ticket Message"; 	//Message in ticket
	$priority 		= "LOW"; 				//Priority for ticket
	
	//The rest is magic
	$orderid = $vars['orderid']; //Get OrderID from WHMCS
	$result = select_query('tblhosting', '', array("orderid" => $orderid)); //Find the product from the order
	$data = mysql_fetch_assoc($result);
	foreach ($products as $pid) {
		if ($data['packageid'] == $pid) {
			$command = "openticket"; //WHMCS Internal API command
			$values = array( 'clientid' => $data['userid'], 'deptid' => $departmentid, 'subject' => $subject, 'message' => $message, 'priority' => $priority, 'admin' => '1'  ); //WHMCS Internal API values
			$results = localAPI($command, $values, $adminuser); //Run command to open ticket
			if ($results['result'] != "success") {
				logActivity('An Error Occurred: '.$results['message']); //Something went wrong check WHMCS logs
			}
		}
	}
}
add_hook("ShoppingCartCheckoutCompletePage",1,"productOpenTicket");
?>