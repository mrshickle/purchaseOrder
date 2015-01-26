<html>
<head>
    <title>JSON Test Page</title>
    <script src="http://code.jquery.com/jquery-1.4.4.js"></script>
    <script>
        // Replace YOURAPIKEY with appropriate key
        // Replace ACCOUNT_ID with your ACCOUNT_ID
        // APIKEY should have access to Read on Account.Category

        $(document).ready(function () {
            var apikey = "1d97805f5ba41b2131fd500621330e444b599d2285dd1eac7c2f65cca62a6043";
            var account_id = "98259";

            var orders_url = "https://" + apikey + ":apikey@api.merchantos.com/API/Account/" + account_id + "/Order.json?callback=?";
            var shops_url = "https://" + apikey + ":apikey@api.merchantos.com/API/Account/" + account_id + "/Shop.json?callback=?";
            var vendors_url = "https://" + apikey + ":apikey@api.merchantos.com/API/Account/" + account_id + "/Vendor.json?callback=?";

            var shops = {};
            var vendors = {};

            $.getJSON(orders_url, function (orders_data) {
                $.getJSON(shops_url, function (shops_data) {
                    $.getJSON(vendors_url, function (vendors_data) {
                        $.each(shops_data.Shop, function (i, shop) {
                            shops[shop.shopID] = shop;
                        });
                        $.each(vendors_data.Vendor, function (i, vendor) {
                            vendors[vendor.vendorID] = vendor;
                        });
                        $.each(orders_data.Order, function (i, order) {
                            if (typeof (vendors[order.vendorID]) != 'undefined') {
                                name = vendors[order.vendorID].name;
                            } else {
                                name = '';
                            }
                            $("<tr>" +
                            "<td>" + order.orderID + "</td>" +
                            "<td>" + shops[order.shopID].name + "</td>" +
                            "<td>" + name + "</td>" +
                            "<td><a href='order.php/" + order.orderID + "'>View</a></td>" +
                            "</tr>").appendTo($("#orders"));
                        });
                    });
                });
            });
        });
    </script>
</head>
<body>
<table id="orders">
    <tr>
        <th>ID</th>
        <th>SHOP</th>
        <th>VENDOR</th>
        <th></th>
    </tr>
</table>
</body>
</html>