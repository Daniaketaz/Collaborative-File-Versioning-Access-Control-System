<!DOCTYPE html>
<html>
<head>
    <title>Files Logs Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
<h1>Files Logs Report</h1>
<table>
    <thead>
    <tr>
        @if (!empty($logs))
            @foreach (array_keys($logs[0]) as $header)
                <th>{{ $header }}</th>
            @endforeach
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach ($logs as $log)
        <tr>
            @foreach ($log as $value)
                <td>{{ $value }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
