
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symbol Details</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <h1>Symbol Details</h1>
        
        @if ($symbolDetails->isEmpty())
            <p>No symbols found.</p>
        @else
            <table border="1" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th>Symbol</th>
                        <th>Latest Delivery Percentage</th>
                        <th>3-Day Average</th>
                        <th>5-Day Average</th>
                        <th>30-Day Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($symbolDetails as $details)
                    @php
                        $highlight = false;

                        // Conditions to highlight the row
                        if (
                            $details['latest_deliv_per'] > $details['three_day_avg'] &&
                            $details['three_day_avg'] >= $details['five_day_avg'] &&
                            $details['five_day_avg'] >= $details['thirty_day_avg']
                        ) {
                            $highlight = true;
                        }
                    @endphp

                        <tr style="{{ $highlight ? 'background-color: #d4edda;' : '' }}">
                            <td>{{ $details['symbol'] }}</td>
                            <td>{{ number_format($details['latest_deliv_per'], 2) }}</td>
                            <td>{{ number_format($details['three_day_avg'], 2) }}</td>
                            <td>{{ number_format($details['five_day_avg'], 2) }}</td>
                            <td>{{ number_format($details['thirty_day_avg'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>