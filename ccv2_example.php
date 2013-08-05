<?php
/*
 * Basic Constant Contact REST API V2 Example
 * Language: PHP
 * Date: August 5th, 2013
 * Author: Elijah Gaiter
 * Questions?: Visit http://developer.constantcontact.com/ to access API endpoint
 *   documentation and developer forums.
 *
 */

define("_apiKey", "<APIKEY>"); // Your API key
define("_authUser", "------"); // Username for BASIC auth
define("_authPass", "------"); // Password for BASIC auth
define("_authToken", "<AUTHTOKEN>"); // Access Token for OAUTH

// this function handles sending a request to the Constant Contact REST API and fetching the result.
/*
 * MakeRequest
 * Returns: JSON encoded string output from Constant Contact API
 * Parameters:
 *   $url - Request URL including parameters
 *   $method - Request method. Default is GET, possibilities are GET, POST, PUT, DELETE
 *   $body - Request to be sent. Use only with PUT or POST methods.
 *   $rqheaders - Array of additional headers to be sent. Default headers are:
 *      Content-type: application/json;charset=UTF-8
 *      Authorization: Bearer <authtoken>
 *
 */
function MakeRequest($url,$method="GET", $body=null, $rqheaders=null)
{
    $theHeaders = Array("Content-type: application/json;charset=UTF-8");
    $theHeaders[] = 'Authorization: Bearer '._authToken;
	if ($rqheaders)
		$theHeaders = array_merge($theHeaders,$rqheaders);
	
    $rq = curl_init();
    curl_setopt($rq, CURLOPT_URL, $url);
    curl_setopt($rq, CURLOPT_HTTPHEADER, $theHeaders);
    curl_setopt($rq, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($rq, CURLOPT_HEADER, 0);
    curl_setopt($rq, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($rq, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($rq, CURLOPT_CUSTOMREQUEST, $method);
    if ($body) {
        curl_setopt($rq, CURLOPT_POSTFIELDS, $body);
    }
    if (!$result = curl_exec($rq)) {
		//Return a JSON formatted Curl Error
        return "{\"curl_error\":\"Error ".curl_errno($rq)." ".curl_error($rq)."\"}";
    } else {
		curl_close($rq);
        return $result;
    }
    
}

// Functions are provided below for some basic actions

// API call to fetch a contact's information from their ID
function getContact($id) {
	return json_decode( MakeRequest("https://api.constantcontact.com/v2/contacts/$id?api_key="._apiKey,"GET"));
}

// API call to update a contact. Additional extra parameter to specify action by VISITOR rather than OWNER to send notification emails.
function updateContact($contact,$actionBy="OWNER") {
	print_r(json_encode($contact));
	return json_decode( MakeRequest("https://api.constantcontact.com/v2/contacts/".$contact->id."?api_key="._apiKey."&action_by=ACTION_BY_".$actionBy,"PUT",json_encode($contact)));
}

// API Call to create a contact Additional extra parameter to specify action by VISITOR rather than OWNER to send welcome emails.
function createContact($contact,$actionBy="OWNER") {
	return json_decode( MakeRequest("https://api.constantcontact.com/v2/contacts?api_key="._apiKey."&action_by=ACTION_BY_".$actionBy,"POST",json_encode($contact)));
}

// API Call to create a campaign
function createCampaign($campaign) {
	return json_decode( MakeRequest("https://api.constantcontact.com/v2/emailmarketing/campaigns?api_key="._apiKey,"POST",json_encode($campaign)));
}

//=================================
// Example of removing a contact from a list. Uncomment to use.
//=================================
/*
// Get the contact's data from Constant Contact
$contact = getContact("1");
// Un-set the array index containing the list you want to remove. You can specify a certain list by searching this array for the list ID.
// This example simply removes them from the first list in the array to demonstrate how.
unset($contact->lists[0]);
// Re-index the array
$contact->lists = array_values($contact->lists);
// Update the contact!
updateContact($contact);
*/

//=================================
// Example of adding a contact to a list. Uncomment to use.
//=================================
/*
// Get the contact's data from Constant Contact
$contact = getContact("<id>"); // Replace <id> with the contact ID you wish to update
// Add a new entry to the end of the array. This is an object containing a list ID
$list = new stdClass();
$list->id = "<id>"; // Replace <id> with the list ID you wish yto add.
$contact->lists[] = $list;
// Clean up some data we don't need
unset($contact->last_update_date);
// Update the contact!
updateContact($contact);
*/

//=================================
// Example of creating a new contact. Uncomment to use.
//=================================
/*
// Create a contact object
$contact = new stdClass();
// Create the email addresses array and add an email address object.
$contact->email_addresses = Array( new stdClass());
// Set the email address
$contact->email_addresses[0]->email_address = "email@address.com";
//Create the list array on the object.
$contact->lists = Array( new stdClass());
// Add a list by ID. 1 is the original General Interest list.
$contact->lists[0]->id = "1"; 
// Send the contact to Constant Contact
createContact($contact);
*/

//=================================
// Example of creating an email campaign. Uncomment to use.
//=================================
/*
// Create our campaign data
$campaign = new stdClass();
$campaign->name = "My Campaign Name";
$campaign->from_name = "My Name";
$campaign->subject = "This email is a test";
// Both the from and reply-to email address MUST be verified on your 
//   Constant Contact account
$campaign->from_email = "email@address.com";
$campaign->reply_to_email = "email@address.com";
$campaign->email_content = "<html><body>This is some text</body></html>";
$campaign->email_content_format = "HTML";
$campaign->text_content = "<text>This is some text</text>";
//Send it to Constant Contact!
createCampaign($campaign);
*/


?>