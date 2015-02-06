<?php
/**
 * Created by PhpStorm.
 * User: appleuser
 * Date: 1/25/15
 * Time: 10:20 AM
 */


/**
 * Created by PhpStorm.
 * User: appleuser
 * Date: 1/25/15
 * Time: 10:20 AM
 */
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Please clear your browser cache and try again';
    exit;
} else {
    if ($_SERVER['PHP_AUTH_USER'] == 'admin' && $_SERVER['PHP_AUTH_PW'] == 'LBUjan15') { ?>


        <?php include_once('MOSAPICall.php');


//https://1d97805f5ba41b2131fd500621330e444b599d2285dd1eac7c2f65cca62a6043:apikey@api.merchantos.com/API/Account/98259/Order.json


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

        $all_orderLines = $mosapi->makeAPICall("Account.OrderLine", "Get", null, null, 'json', 'orderID=' . $currentOrderID);
        if (isset($all_orderLines['OrderLine'][0])) {
            $orderLines = $all_orderLines;
        } else {
            $orderLines['OrderLine'][0] = $all_orderLines['OrderLine'];
        }

        if (count($orderLines['OrderLine']) > 1) {
            // Get items query for getting only items from order Lines;
            $itemsQuery = 'load_relations=all&itemID=IN,[';
            foreach ($orderLines['OrderLine'] as $orderLine):
                $itemsQuery = $itemsQuery . $orderLine['itemID'] . ',';
            endforeach;
            $itemsQuery = substr($itemsQuery, 0, strlen($itemsQuery) - 1);
            $itemsQuery = $itemsQuery . ']';
            $all_items = $mosapi->makeAPICall("Account.Item", "Get", null, null, 'json', $itemsQuery);
            $items = $all_items['Item'];
        } else {
            $item = $mosapi->makeAPICall("Account.Item", "Get", null, null, 'json', 'load_relations=all&itemID=' . $orderLines['OrderLine'][0]['itemID']);
            $items[0] = $item['Item'];
        }
//echo '<pre>';
//print_r($items);
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
                    Date: <?= substr($order['orderedDate'], 0, -15) ?> <br/>
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
                    <?php
                    if (isset($shop['Contact']['Phones']['ContactPhone'][0])) {
                        foreach ($shop['Contact']['Phones']['ContactPhone'] as $phone) {
                            if ($phone['useType'] == 'Work') echo 'Phone: ' . $phone['number'] . ' - ';
                            if ($phone['useType'] == 'Fax') echo 'Fax: ' . $phone['number'];
                        }
                    } else {
                        if ($shop['Contact']['Phones']['ContactPhone']['useType'] == 'Work')
                            echo 'Phone: ' . $shop['Contact']['Phones']['ContactPhone']['number'] . ' - ';
                        if ($shop['Contact']['Phones']['ContactPhone']['useType'] == 'Fax')
                            echo 'Fax: ' . $shop['Contact']['Phones']['ContactPhone']['number'];
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center" style="background: #777">
                    <strong style="color: #fff">e-mailed invoices must be sent to: LBULVENDOR@gmail.com</strong>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-7" style="border: 1px solid #777777; height: 120px; padding-top: 15px; ">
                    <div class="row">
                        <div class="col-xs-6">
                            Vendor: <?= $vendor['name'] ?> <br/>
                            Account#: <?= $vendor['accountNumber']; ?> <br/>
                            Sales
                            Rep: <?= $vendor['Reps']['VendorRep']['firstName'] . ' ' . $vendor['Reps']['VendorRep']['lastName'] ?>
                        </div>
                        <div class="col-xs-6">
                            <?php
                            if (isset($vendor['Contact']['Phones']['ContactPhone'][0])) {
                                foreach ($vendor['Contact']['Phones']['ContactPhone'] as $phone) {
                                    if ($phone['useType'] == 'Work') echo 'Phone: ' . $phone['number'] . '<br/>';
                                    if ($phone['useType'] == 'Fax') echo 'Fax: ' . $phone['number'] . '<br/>';
                                }
                            } else {
                                if ($vendor['Contact']['Phones']['ContactPhone']['useType'] == 'Work')
                                    echo 'Phone: ' . $vendor['Contact']['Phones']['ContactPhone']['number'] . '<br/>';
                                if ($vendor['Contact']['Phones']['ContactPhone']['useType'] == 'Fax')
                                    echo 'Fax: ' . $vendor['Contact']['Phones']['ContactPhone']['number'] . '<br/>';
                            }
                            ?>
                            <?php if (isset($vendor['Contact']['Emails']['ContactEmail']['address'])) {
                                echo 'Email: ' . $vendor['Contact']['Emails']['ContactEmail']['address'];
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-5" style="height: 120px; border: 1px solid #777777; border-left: none;">
                    <div class="row">
                        <h5 style="border-bottom: 1px solid #777; background: #dadada; margin: 0; text-align: center; padding: 4px 0;">
                            Special Instructions: </h5>
                        <?= $order['Note']['note']; ?>
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
                    $subtotalOrdered = 0;
                    $subtotalReceived = 0;
                    foreach ($orderLines['OrderLine'] as $orderLine):
                        $subtotalOrdered += $orderLine['price'] * $orderLine['quantity'];
                        if ($order['complete']) $subtotalReceived += $orderLine['price'] * $orderLine['checkedIn'];
                        else $subtotalReceived += $orderLine['price'] * $orderLine['numReceived'];
                        ?>
                        <tr>
                            <td><?= $orderLine['quantity'] ?></td>
                            <td><?php
                                if ($order['complete']) echo $orderLine['checkedIn']; else echo $orderLine['numReceived'] ?></td>
                            <?php foreach ($items as $item):
                                if ($item['itemID'] == $orderLine['itemID']): ?>
                                    <?php
                                    if (isset($item['CustomFieldValues']['CustomFieldValue'][0])) {
                                        foreach ($item['CustomFieldValues']['CustomFieldValue'] as $customField) {
                                            if ($customField['name'] == 'Unit') {
                                                echo '<td>' . $customField['value'] . '</td>';
                                            }
                                        }
                                    } else {
                                        if ($item['CustomFieldValues']['CustomFieldValue']['name'] == 'Unit') {
                                            echo '<td>' . $item['CustomFieldValues']['CustomFieldValue']['value'] . '</td>';
                                        } else {
                                            echo '<td></td>';
                                        }
                                    }
                                    ?>
                                    <td><?= $item['manufacturerSku'] ?></td>
                                    <td><?= $item['description'] ?></td>
                                <?php endif;?>
                            <?php endforeach; ?>

                            <td><?= $orderLine['price'] ?></td>
                            <td><?= $orderLine['price'] * $orderLine['quantity'] ?></td>
                            <td><?php if ($order['complete']) echo  $orderLine['price'] * $orderLine['checkedIn']; else echo $orderLine['price'] * $orderLine['numReceived'] ?></td>
                        </tr>

                    <?php
                    endforeach;
                    ?>

                    <tr style="border: 1px solid #777;">
                        <td colspan="5">

                        </td>
                        <td>
                            Subtotal
                        </td>
                        <td>
                            <?= $subtotalOrdered; ?>
                        </td>
                        <td>
                            <?= $subtotalReceived; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" rowspan="3" class="policy">
                            Vendor must show Purchase Number on Invoices, packing slips, etc. Shipment of this order is
                            "Sold
                            To/Bill To" MPII, LTD, Corporate Office. Vendor must "Ship To: MPII, LTD store, as
                            instructed above.
                            This Purchase Order is to be entered as specified herein. Notify us immediately if unable to
                            ship as
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
        </html>    <?php } else {
        echo "The password you entered is not correct. Please clear your browser cache and try again";
    } ?>

<?php } ?>
