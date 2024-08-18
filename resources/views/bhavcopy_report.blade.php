
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
                    @foreach ($symbolDetails as $detail)
                        <tr>
                            <td>{{ $detail['symbol'] }}</td>
                            <td>{{ number_format($detail['latest_deliv_per'], 2) }}</td>
                            <td>{{ number_format($detail['three_day_avg'], 2) }}</td>
                          
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>