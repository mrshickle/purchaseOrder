<?php
/**
 * Created by PhpStorm.
 * User: appleuser
 * Date: 1/25/15
 * Time: 10:20 AM
 */


include_once('MOSAPICall.php');


//$orders_url = "https://api.merchantos.com/API/Account/" . $account_id . "/Order.json";
//$shops_url = "https://api.merchantos.com/API/Account/" . $account_id . "/Shop.json?callback=?";
//$vendors_url = "https://api.merchantos.com/API/Account/" . $account_id . "/Vendor.json?callback=?";



// setup our credentials
// this key is to our demo data and allows full access to just /Account/797/Item control
$apikey = 'fa7dfeae59395b0a2f1ebdabbab31b9b6d8039c596de645e765e37b1c370202d';
$account_id = '98982';
$mosapi = new MOSAPICall($apikey, $account_id);

// compose vendors array
//$buff = $mosapi->makeAPICall("Account.Vendor", "Get", null, null, 'json');
//$vendors = array();
//foreach ($buff['Vendor'] as $vendor) {
//    $vendors[$vendor['vendorID']] = $vendor;
//}
echo '<pre>';
$orders = $mosapi->makeAPICall("Account.Order", "Get", null, null, 'json');
$vendors = array();

foreach ($orders['Order'] as $order) {
//    if (!isset($vendors[$order['vendorID']])) {
//        $vendors[$order['vendorID']] = $mosapi->makeAPICall("Account.Vendor", "Get", $order['vendorID'], null, 'json');
//    }
//
//    var_dump('Order:', $order);
//    var_dump('Vendor:', $vendors[$order['vendorID']]);
//    var_dump('--------------------');

    echo '<a href="/order.php?order='. $order['orderID'] .'">' . $order["orderID"] .' '. $order["orderedDate"] .'</a><br/>';

}
die;
// get the itemID out of the response XML
//$item_id = $item_response_xml->itemID;
//// Change the item's description
//$updated_description = $item_description . " Updated!";
//// make another API call to Account.Item, this time with Update method and our changed Item XML.
//$updated_item_response_xml = $mosapi->makeAPICall("Account.Item","Update",$item_id,$xml_update_item);
