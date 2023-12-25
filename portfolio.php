<?php
// Function to fetch JSON data from the API
function fetchCurrencyData($url)
{
    $json_data = file_get_contents($url);

    if ($json_data !== false) {
        return json_decode($json_data, true);
    } else {
        return false;
    }
}

// URL for fetching currency data (replace with your actual URL)
$url = "https://www.myfxbook.com/api/get-history.json?session=fIs7GlGFfaABJxI3DzBu2505997&id=10290396";

// Fetch currency data
$data = fetchCurrencyData($url);

if ($data !== false && isset($data['history']) && is_array($data['history'])) {
    $history = $data['history'];

    // Initialize an empty array to store currency data for the pie chart
    $currencyData = array();

    // Loop through the history data and collect currency information
    foreach ($history as $entry) {
        $symbol = $entry['symbol'];

        // Check if the currency symbol exists in the $currencyData array
        if (isset($currencyData[$symbol])) {
            $currencyData[$symbol] += 1; // Increment the count for this currency symbol
        } else {
            $currencyData[$symbol] = 1; // Initialize the count for this currency symbol
        }
    }

    // Prepare the data for the pie chart
    $dataPoints = array();
    foreach ($currencyData as $symbol => $count) {
        $dataPoints[] = array("label" => $symbol, "y" => $count);
    }
} else {
    echo "Failed to fetch or process currency data.";
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <script>
        window.onload = function() {
            var chart = new CanvasJS.Chart("chartContainer", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "Currency Distribution"
                },
                data: [{
                    type: "doughnut",
                    indexLabel: "{label} - {y}",
                    yValueFormatString: "#,##0\"%\"",
                    showInLegend: true,
                    legendText: "{label} : {y}",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();
        }
    </script>
</head>

<body>
    <div id="chartContainer" style="height: 370px; width: 100%;"></div>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>

</html>