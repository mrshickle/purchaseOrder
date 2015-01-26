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
$apikey = '1d97805f5ba41b2131fd500621330e444b599d2285dd1eac7c2f65cca62a6043';
$account_id = '98259';
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

$orderLines = $mosapi->makeAPICall("Account.OrderLine", "Get", null, null, 'json', 'orderID=' . $currentOrderID);


// Get items query for getting only items from order Lines;
$itemsQuery = 'itemID=IN,[';
foreach ($orderLines['OrderLine'] as $orderLine):
    $itemsQuery = $itemsQuery . $orderLine['itemID'] . ',';
endforeach;
$itemsQuery = substr($itemsQuery, 0, strlen($itemsQuery) - 1);
$itemsQuery = $itemsQuery . ']';
//echo $itemsQuery;
$items = $mosapi->makeAPICall("Account.Item", "Get", null, null, 'json', $itemsQuery);
$item = $items['Item'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap 101 Template</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-4">
            <img src="logo.png.png" alt="" class="img-responsive"/>
        </div>
        <div class="col-xs-4">
            <h4><br/>MP II, LTD <br/> (dba) Light Bulbs Unlimited</h4>
        </div>
        <div class="col-xs-4">
            <h3>Purchase Order</h3>
            Date: <?= $order['orderedDate'] ?> <br/>
            PO Number: #<?= $order['orderID'] ?> <br/>
            Ordered By: John
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6"><h4>Bill To:</h4></div>
        <div class="col-xs-6"><h4>Ship To:</h4></div>
    </div>
    <div class="row">
        <div class="col-xs-6" style="border: 1px solid #777777">
            MPII, LTD (dba) Light Bulbs Unlimited<br/>
            14446 Ventura Blvd <br/>
            Sherman Oaks, CA. 91423 <br/>
            Phone: 323-621-0330 - Fax: 323-651-0313
        </div>
        <div class="col-xs-6" style="border: 1px solid #777777; border-left: none;">
            <?= $shop['name'] ?> <br/>
            <?= $shop['Contact']['Addresses']['ContactAddress']['address1']; ?> <br/>
            <?= $shop['Contact']['Addresses']['ContactAddress']['city'] . ', ' . $shop['Contact']['Addresses']['ContactAddress']['state'] . ' ' . $shop['Contact']['Addresses']['ContactAddress']['zip']; ?>
            <br/>
            Phone: <?= $shop['Contact']['Phones']['ContactPhone']['number']; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 text-center" style="background: #777">
            <strong style="color: #fff">e-mailed invoices must be sent to: LBULVENDOR@gmail.com</strong>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7" style="border: 1px solid #777777; height: 100px; padding-top: 15px; ">
            <div class="row">
                <div class="col-xs-6">
                    Vendor: <?= $vendor['name'] ?> <br/>
                    Account#: <?= $vendor['accountNumber']; ?> <br/>
                    Sales
                    Rep: <?= $vendor['Reps']['VendorRep']['firstName'] . ' ' . $vendor['Reps']['VendorRep']['lastName'] ?>
                </div>
                <div class="col-xs-6">
                    <?php foreach ($vendor['Contact']['Phones']['ContactPhone'] as $phone) {
                        if ($phone['useType'] == 'Work') echo 'Phone: ' . $phone['number'] . '<br/>';
                        if ($phone['useType'] == 'Fax') echo 'Fax: ' . $phone['number'] . '<br/>';
                    } ?>
                    Email: <?= $vendor['Contact']['Emails']['ContactEmail']['address'] ?>
                </div>
            </div>
        </div>
        <div class="col-xs-5" style="height: 100px; border: 1px solid #777777; border-left: none;">
            <div class="row">
                <h5 style="border-bottom: 1px solid #777; background: #dadada; margin: 0; text-align: center; padding: 4px 0;">
                    Special Instructions: </h5>
                <?= $order['shipInstructions']; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <table class="theTable table text-center">

            <tr>
                <td>Qty Ordered</td>
                <td>Qty Shipped</td>
                <td>Units</td>
                <td>Mfgr Code</td>
                <td>Description</td>
                <td>Unit Cost</td>
                <td>Ordered Ext Cost</td>
                <td>Shipped Ext Cost</td>
            </tr>


            <?php
            if (is_array($orderLines['OrderLine'])):
                foreach ($orderLines['OrderLine'] as $orderLine): ?>


                    <tr>
                        <td><?= $orderLine['quantity'] ?></td>
                        <td><?= $orderLine['numReceived'] ?></td>
                        <td></td>
                        <?php foreach ($item as $lineItem):
                            if ($lineItem['itemID'] == $orderLine['itemID']): ?>
                                <td class="text-left"><?= $lineItem['manufacturerSku'] ?></td>
                                <td class="text-left"><?= $lineItem['description'] ?></td>
                            <?php endif;?>
                        <?php endforeach; ?>

                        <td><?= $orderLine['price'] ?></td>
                        <td><?= $orderLine['price'] * $orderLine['quantity'] ?></td>
                        <td><?= $orderLine['price'] * $orderLine['numReceived'] ?></td>
                    </tr>


                <?php
                endforeach;
            endif;
            ?>

            <tr style="border: 1px solid #777;">
                <td colspan="5">

                </td>
                <td>
                    Subtotal
                </td>
                <td>
                    <?php
                    $subtotalOrdered = 0;
                    foreach ($orderLines['OrderLine'] as $orderLine):

                        $subtotalOrdered += $orderLine['price'] * $orderLine['quantity'];

                    endforeach;
                    echo $subtotalOrdered; ?>
                </td>
                <td>
                    <?php
                    $subtotalReceived = 0;
                    foreach ($orderLines['OrderLine'] as $orderLine):

                        $subtotalReceived += $orderLine['price'] * $orderLine['numReceived'];

                    endforeach;
                    echo $subtotalReceived; ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" rowspan="3" class="policy">
                    Vendor must show Purchase Number on Invoices, packing slips, etc. Shipment of this order is "Sold
                    To/Bill To" MPII, LTD, Corporate Office. Vendor must "Ship To: MPII, LTD store, as instructed above.
                    This Purchase Order is to be entered as specified herein. Notify us immediately if unable to ship as
                    specified.                                                    
                </td>
                <td>
                    Shipping
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Tax</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Total:</td>
                <td><?= $subtotalOrdered; ?></td>
                <td><?= $subtotalReceived; ?></td>
            </tr>
        </table>
    </div>
    <div class="row text-center">
        <div class="col-xs-8 col-xs-offset-2">
            <h3>
                Do not send duplicate email and postal invoices. <br/>
                e-mailed invoices must be sent to: LBULAVENDOR@gmail.com
            </h3>
        </div>
    </div>
</div>
</body>
</html>