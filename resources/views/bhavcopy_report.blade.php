
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Details</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>
<body>
    <div class="container">
        <h1>Stock Details</h1>
         <!-- Sorting Form -->
         <form method="GET" action="{{ route('showReport') }}">
            <label for="datepicker">Select Date:</label>
            <input type="text" id="datepicker" name="date" value="{{ old('date', $selectedDate ?? '') }}">
            <label for="sort_by">Sort by:</label>
            <select name="sort_by" id="sort_by" onchange="this.form.submit()">
                <option value="latest_deliv_per" {{ request('sort_by') == 'latest_deliv_per' ? 'selected' : '' }}>Latest Delivery Percentage</option>
                <option value="three_day_avg" {{ request('sort_by') == 'three_day_avg' ? 'selected' : '' }}>3-Day Average</option>
                <option value="five_day_avg" {{ request('sort_by') == 'five_day_avg' ? 'selected' : '' }}>5-Day Average</option>
                <option value="thirty_day_avg" {{ request('sort_by') == 'thirty_day_avg' ? 'selected' : '' }}>30-Day Average</option>
                <option value="highest_price_move" {{ request('sort_by') == 'highest_price_move' ? 'selected' : '' }}>Highest Price Move</option>
                <option value="turnover_lacs" {{ request('sort_by') == 'turnover_lacs' ? 'selected' : '' }}>Turnover (Lacs)</option>
            </select>
            <label for="sort_by">Data as on: {{ $dataAsOn }}</label>
        </form>
        @if ($symbolDetails->isEmpty())
            <p>No symbols found.</p>
        @else
            <table border="1" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th colspan="4">Delivery Percentages and Averages</th>
                        <th>Highest Price Move</th>
                        <th>Turnover (Lacs)</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Today</th>
                        <th>3-Day</th>
                        <th>5-Day</th>
                        <th>30-Day </th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($symbolDetails as $details)
                    @php
                        $highlight = false;

                        // Conditions to highlight the row
                        if (
                            $details['latest_deliv_per'] > $details['three_day_avg'] &&
                            $details['three_day_avg'] > $details['five_day_avg'] &&
                            $details['five_day_avg'] > $details['thirty_day_avg']
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
                            <td>{{ number_format($details['highest_price_move'], 2) }}</td>
                            <td>{{ number_format($details['turnover_lacs'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
<script>
    $(function() {
        $("#datepicker").datepicker({
            dateFormat: "dd-mm-yy", // Set the date format
            defaultDate: "{{ old('date', $selectedDate) }}",
            onSelect: function(dateText, inst) {
                // Automatically submit the form when a date is selected
                this.form.submit()
                // $('#dateForm').submit();
            }
        });
    });
</script>