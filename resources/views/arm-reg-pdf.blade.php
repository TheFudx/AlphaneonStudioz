<!DOCTYPE html>
<html>
<head>
    <title>Registration Details</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #fff;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 95%;
            max-width: 800px;
            margin: 0 auto;
            color: #333;
        }
        h3 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        td {
            padding: 8px 15px;
            border: 1px solid #444;
            text-align: left;
        }
        .label {
            font-weight: bold;
            background-color: #fff;
        }
        .value {
            background-color: #fff;
        }
        .amount-paid {
            text-align: right;
            font-weight: bold;
            font-size: 18px;
        }
        .text-center {
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .amount {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Registration Details Table -->
    
     <h3>Registration Details</h3>
    <table>
        <tr>
            <td class="label">Full Name</td>
            <td class="value">{{ $registration->fullname }}</td>
            <td class="label">Email</td>
            <td class="value">{{ $registration->email }}</td>
        </tr>
        <tr>
            <td class="label">Mobile</td>
            <td class="value">{{ $registration->mobile }}</td>
            <td class="label">Age</td>
            <td class="value">{{ $registration->age }}</td>
        </tr>
        <tr>
            <td class="label">Weight</td>
            <td class="value">{{ $registration->weight }}</td>
            <td class="label">Injury</td>
            <td class="value">{{ $registration->injury }}</td>
        </tr>
        <tr>
            <td class="label">Dominant Hand</td>
            <td class="value">{{ $registration->dhand }}</td>
            <td class="label">Experience</td>
            <td class="value">{{ $registration->experience }}</td>
        </tr>
    </table>
    <!-- Venue and Time Details -->
    <h3>Venue and Time Details</h3>
    <table>
        <tr>
            <td class="label">Venue</td>
            <td class="value">Mumbai</td>
            <td class="label">Date</td>
            <td class="value">30 SEP, 2024</td>
        </tr>
        <tr>
            <td class="label">Time</td>
            <td class="value" colspan="3">11:00 AM to 5:00 PM</td>
        </tr>
    </table>
    <!-- Amount Paid -->
    <div class="amount-paid">
        <p class="amount">Amount Paid: Rs. {{ $registration->amount }}</p>
    </div>
   
</div>
</body>
</html>
