<?php
/**
 * Created by PhpStorm.
 * User: appleuser
 * Date: 1/25/15
 * Time: 10:20 AM
 */
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
} else {
    if ($_SERVER['PHP_AUTH_USER'] == 'admin' && $_SERVER['PHP_AUTH_PW'] == 'LBUjan15') { ?>

        <?php
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
        $ordersCount = $mosapi->makeAPICall("Account.Order", "Get", null, null, 'json');

        if ($ordersCount['@attributes']['count'] > 100) $pageCount = ($ordersCount['@attributes']['count'] - $ordersCount['@attributes']['count'] % 100) / 100;

        $pageNumber = 0;

        if (isset($_GET['page'])) $pageNumber = $_GET['page'];
        $offset = $pageNumber * 100;


        $orders = $mosapi->makeAPICall("Account.Order", "Get", null, null, 'json', 'load_relations=all&offset=' . $offset);
        $vendors = array();

        foreach ($orders['Order'] as $order) {
//    if (!isset($vendors[$order['vendorID']])) {
//        $vendors[$order['vendorID']] = $mosapi->makeAPICall("Account.Vendor", "Get", $order['vendorID'], null, 'json');
//    }
//
//    var_dump('Order:', $order);
//    var_dump('Vendor:', $vendors[$order['vendorID']]);
//    var_dump('-------------------echo '<a href="/order.php?order=' . $order['orderID'] . '">' . $order["orderID"] . ' ' . $order["orderedDate"] . '</a><br/>';-');


        }
// get the itemID out of the response XML
//$item_id = $item_response_xml->itemID;
//// Change the item's description
//$updated_description = $item_description . " Updated!";
//// make another API call to Account.Item, this time with Update method and our changed Item XML.
//$updated_item_response_xml = $mosapi->makeAPICall("Account.Item","Update",$item_id,$xml_update_item); ?>

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
            <div class="row text-center">
                <div class="btn-group" role="group" aria-label="...">
                    <?php
                    if ($pageCount > 0) {
                        for ($i = 0; $i <= $pageCount; $i++) {
                            echo '<a type="button" class="btn btn-default';
                            if (!isset($_GET['page']) && $i == 0) echo ' active';
                            if (isset($_GET['page']) && $_GET['page'] == $i) echo ' active';
                            echo '" href="?page=' . $i . '">' . ($i + 1) . '</a> ';
                        }
                    }
                    ?>
                </div>
            </div>
            <table class="table table-hover">

                <tr>
                    <td>ID</td>
                    <td>Shop</td>
                    <td>Vendor</td>
                    <td>Date Ordered</td>
                    <td>Date Received</td>
                    <td>Print</td>
                </tr>
                <?php foreach ($orders['Order'] as $order): ?>

                    <tr>
                        <td><?= $order['orderID'] ?></td>
                        <td><?= $order['Shop']['name'] ?></td>
                        <td><?= $order['Vendor']['name'] ?></td>
                        <td><?= substr($order['orderedDate'], 0, -15) ?></td>
                        <td><?= substr($order['receivedDate'], 0, -15) ?></td>
                        <td><a href="order.php?order=<?= $order['orderID'] ?>"><span class="glyphicon glyphicon-print"
                                                                                     aria-hidden="true"></span></td>
                    </tr>

                <?php endforeach; ?>
            </table>
        </div>
        </body>
        </html>
    <?php } else {
        echo "The password you entered is not correct. Please clear your browser cache and try again";
    } ?>

<?php } ?>
