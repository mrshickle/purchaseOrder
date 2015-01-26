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

$currentOrderID = $_GET["order"];

$orders = $mosapi->makeAPICall("Account.Order", "Get", $currentOrderID, null, 'json', 'load_relations=all');
$order = $orders['Order'];

$vendors = $mosapi->makeAPICall("Account.Vendor", "Get", $order['vendorID'], null, 'json', 'load_relations=all');
$vendor = $vendors['Vendor'];
$shops = $mosapi->makeAPICall("Account.Shop", "Get", $order['shopID'], null, 'json', 'load_relations=all');
$shop = $shops['Shop'];

?>

    <table>
        <tr>
            <td><img src="logo.png.png" alt=""/></td>
            <td><?= $shop['name'] ?></td>
            <td>
                <h2>Purchase Order</h2>

                <p>
                    Date: <?= $order['orderedDate'] ?> <br/>
                    PO Number: <?= $order['orderID'] ?>
                    <!--                    Ordered By: -->
                </p>
            </td>
        </tr>

    </table>
    <table>
        <tr>
            <td>Bill To:</td>
            <td>Ship To:</td>
        </tr>
        <tr>

            <td>
                <?= $shop['name'] ?> <br/>
                <?= $shop['Contact']['Addresses']['ContactAddress']['address1']; ?> <br/>
                <?= $shop['Contact']['Addresses']['ContactAddress']['city'] . ', ' . $shop['Contact']['Addresses']['ContactAddress']['state'] . ' ' . $shop['Contact']['Addresses']['ContactAddress']['zip']; ?>
                <br/>
                 <?php if (is_array($shop['Contact']['Phones']['ContactPhone'])) {
                    echo 'Phone: '.$shop['Contact']['Phones']['ContactPhone']['number'];
                } else {
                    foreach ($shop['Contact']['Phones']['ContactPhone'] as $phone) {
                        if ($phone['useType'] == 'Work') echo 'Phone: ' . $phone['number'];
                        if ($phone['useType'] == 'Fax') echo ' - Fax: ' . $phone['number'];
                    }
                };
                ?>
            </td>
            <td>
                <?= $shop['name'] ?> <br/>
                <?= $shop['Contact']['Addresses']['ContactAddress']['address1']; ?> <br/>
                <?= $shop['Contact']['Addresses']['ContactAddress']['city'] . ', ' . $shop['Contact']['Addresses']['ContactAddress']['state'] . ' ' . $shop['Contact']['Addresses']['ContactAddress']['zip']; ?>
                <br/>
                Phone: <?= $shop['Contact']['Phones']['ContactPhone']['number']; ?>
                <?php foreach ($shop['Contact']['Phones']['ContactPhone'] as $phone) {
//                    if ($phone['useType'] == 'Work') echo 'Phone: '.$phone['number'];
//                    if ($phone['useType'] == 'Fax') echo ' - Fax: '.$phone['number'];
//                    echo $phone['number'];
                } ?>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td>
                e-mailed invoices must be sent to:
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td>
                Vendor: <?= $vendor['name'] ?> <br/>
                Sales Rep: <?= $vendor['Reps']['VendorRep']['firstName'].' '.$vendor['Reps']['VendorRep']['lastName'] ?>
                Phone: <?php foreach ($vendor['Contact']['Phones']['ContactPhone'] as $phone) {
                    if ($phone['useType'] == 'Work') echo 'Phone: ' . $phone['number'];
                } ?>
            </td>
            <td>
                <br/>
                <?= $vendor['Contact']['Addresses']['ContactAddress']['address1']; ?> <br/>
                <?= $vendor['Contact']['Addresses']['ContactAddress']['city'] . ', ' . $vendor['Contact']['Addresses']['ContactAddress']['state'] . ' ' . $vendor['Contact']['Addresses']['ContactAddress']['zip']; ?>
                <br/>
                <?php foreach ($vendor['Contact']['Phones']['ContactPhone'] as $phone) {
                    if ($phone['useType'] == 'Work') echo 'Phone: ' . $phone['number'];
                    if ($phone['useType'] == 'Fax') echo ' - Fax: ' . $phone['number'];
                } ?>
            </td>
        </tr>
    </table>
<?php



echo '<pre>';
var_dump($orders['Order']);
var_dump($vendors['Vendor']);
var_dump($shops['Shop']);

//echo

foreach ($orders['Order'] as $order) {
//    if (!isset($vendors[$order['vendorID']])) {
//        $vendors[$order['vendorID']] = $mosapi->makeAPICall("Account.Vendor", "Get", $order['vendorID'], null, 'json');
//    }

//    var_dump('Order:', $order);
//    var_dump('Vendor:', $vendors[$order['vendorID']]);
//    var_dump('--------------------');
//
//    echo '<a href="/order.php?order="'. $order['orderID'] .'">' . $order['orderID'] . ' - '. $order['orderedDate'] .'</a><br/>';

}
//die;
// get the itemID out of the response XML
//$item_id = $item_response_xml->itemID;
//// Change the item's description
//$updated_description = $item_description . " Updated!";
//// make another API call to Account.Item, this time with Update method and our changed Item XML.
//$updated_item_response_xml = $mosapi->makeAPICall("Account.Item","Update",$item_id,$xml_update_item);
