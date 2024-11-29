<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Spelling Mini Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        #nicknameForm {
            margin-top: 100px;
        }

        #gameArea {
            margin-top: 50px;
        }

        .card {
            padding: 20px;
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Nickname Form -->
        <form id="nicknameForm" method="POST" class="p-4 border rounded shadow-lg bg-light mx-auto" style="max-width: 400px;">
            @csrf
            <h3 class="text-center mb-4">Welcome to Spelling Mini Game</h3>
            <div class="mb-3">
                <label for="nickname" class="form-label">Enter Your Name:</label>
                <input type="text" id="nickname" name="nickname" class="form-control" placeholder="Your name" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Start Game</button>
                <a href="{{ route('leaderboard') }}" class="btn btn-secondary">View Leaderboard</a>
            </div>
        </form>

        <!-- Game Area -->
        <div id="gameArea" class="container text-center mt-5" style="display: none;">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h3 id="word" class="card-title my-3 text-uppercase text-primary"></h3>
                    <div class="mb-3">
                        <input type="text" id="answer" class="form-control w-50 mx-auto" placeholder="Your answer">
                    </div>
                    <button id="submitAnswer" class="btn btn-success">Submit</button>
                    <hr>
                    <p class="mt-3">Time left: <span id="timeleft" class="fw-bold text-danger">60</span> seconds</p>
                    <p>Score: <span id="score" class="fw-bold text-success">0</span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const words = ['apple', 'banana', 'cherry', 'dog', 'cat', 'fish', 'elephant', 'computer'];
        let userId, score = 0, timer = 60, currentWordIndex = 0, originalWord = '';

        function shuffleWords() {
            for (let i = words.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [words[i], words[j]] = [words[j], words[i]];
            }
        }

        function maskWord(word) {
            const letters = word.split('');
            let masked = letters.map(() => '_');

            const indicesToReveal = new Set();
            while (indicesToReveal.size < Math.min(3, letters.length)) {
                indicesToReveal.add(Math.floor(Math.random() * letters.length));
            }
            indicesToReveal.forEach((index) => {
                masked[index] = letters[index];
            });

            return masked.join('');
        }

        document.getElementById('nicknameForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const nickname = document.getElementById('nickname').value;

            const response = await fetch('/start-game', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ nickname })
            });

            const data = await response.json();
            userId = data.player.id;

            document.getElementById('nicknameForm').style.display = 'none';
            document.getElementById('gameArea').style.display = 'block';

            shuffleWords();
            startGame();
        });

        async function startGame() {
            const interval = setInterval(() => {
                timer--;
                document.getElementById('timeleft').textContent = timer;

                if (timer <= 0) {
                    clearInterval(interval);
                    endGame();
                }
            }, 1000);

            showNextWord();
        }

        function showNextWord() {
            if (currentWordIndex < words.length) {
                originalWord = words[currentWordIndex];
                const maskedWord = maskWord(originalWord);
                document.getElementById('word').textContent = maskedWord;
                document.getElementById('answer').value = '';
            } else {
                endGame();
            }
        }

        function checkAnswer() {
            const userAnswer = document.getElementById('answer').value.trim().toLowerCase();

            if (userAnswer === originalWord.toLowerCase()) {
                score++;
                document.getElementById('score').textContent = score;
            }

            currentWordIndex++;
            showNextWord();
        }

        async function endGame() {
            await fetch('/submit_score', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ player_id: userId, score })
            });

            alert('Game over! Your score: ' + score);
            location.reload();
        }

        document.getElementById('submitAnswer').addEventListener('click', checkAnswer);

    </script>
</body>
</html>
