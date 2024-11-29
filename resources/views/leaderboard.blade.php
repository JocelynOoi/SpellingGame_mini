<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        table {
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #343a40;
        }

        a {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center my-4">Leaderboard</h1>
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scores as $index => $score)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $score->player->nickname }}</td>
                        <td>{{ $score->score }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('game') }}" class="btn btn-primary d-block mx-auto w-25">Back to Home</a>
    </div>
</body>
</html>
