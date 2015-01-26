<html>
<head>
    <title>JSON Test Page</title>
    <script src="http://code.jquery.com/jquery-1.4.4.js"></script>
    <script>
        $(document).ready(function () {
            var apikey = "1d97805f5ba41b2131fd500621330e444b599d2285dd1eac7c2f65cca62a6043";
            var account_id = "98259";

            var order_url = "https://" + apikey + ":apikey@api.merchantos.com/API/Account/" + account_id + "/Order/";
            var shop_url = "https://" + apikey + ":apikey@api.merchantos.com/API/Account/" + account_id + "/Shop/";
            var vendor_url = "https://" + apikey + ":apikey@api.merchantos.com/API/Account/" + account_id + "/Vendor/";
            var order_lines_url = "https://" + apikey + ":apikey@api.merchantos.com/API/Account/" + account_id + "/OrderLine.json?callback=?";

            var order;
            var shop;
            var vendor;
            var order_lines = {};

            var url = window.location.pathname;
            var orderID = url.split('/').pop();

            $.getJSON(order_url + orderID + '.json?callback=?', function (order_data) {
                order = order_data.Order;
                $.getJSON(shop_url + order.shopID + '.json?callback=?', function (shop_data) {
                    shop = shop_data.Shop;
                    $.getJSON(vendor_url + order.vendorID + '.json?callback=?', function (vendor_data) {
                        vendor = vendor_data.Vendor;
                        $.getJSON(order_lines_url, function (all_order_lines) {
                            $.each(all_order_lines.OrderLine, function (i, order_line) {
                                if (order_line.orderID == orderID) {
                                    order_lines[order_line.orderLineID] = order_line;
                                }
                            });
                        });
                    });
                });
            });
        });
    </script>
</head>
<body>
</body>
</html>