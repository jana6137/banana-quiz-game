/*Configuration: Level settings <!-- Interesting Features - Different Difficulty Levels--> */
const levelConfig = {
  1: { games: 5, time: 20 * 60 },
  2: { games: 7, time: 15 * 60 },
  3: { games: 9, time: 10 * 60 },
  4: { games: 11, time: 5 * 60 }
};

/*Global Variables*/
let currentLevel = 1;
let puzzlesPlayed = 0;
let correctAnswer;
let attemptsLeft;
let timeRemaining;
let timerInterval;
let currentScore = 0;
let correctCount = 0;
let wrongCount = 0;

/* Function: loadPuzzle <!--Version Control--> */
async function loadPuzzle() {
  const config = levelConfig[currentLevel];

  // ✅ Check if level should end BEFORE loading new puzzle
  if (puzzlesPlayed >= config.games || currentScore >= 100) {
    console.log("🎯 Level completed in loadPuzzle! Puzzles:", puzzlesPlayed, "Max:", config.games);
    document.getElementById('feedback').textContent = "🎉 Level Complete!";
    clearInterval(timerInterval);
    saveScore(); // ✅ Save and redirect
    return;
  }

  /*Interoperability - Banana API */
  try {
    const response = await fetch('https://marcconrad.com/uob/banana/api.php?out=json');
    const data = await response.json();

    document.getElementById('puzzleImage').src = data.question;
    correctAnswer = data.solution;

    attemptsLeft = 3;
    document.getElementById('attemptsLeft').textContent = attemptsLeft;

    document.getElementById('feedback').textContent = "";
    puzzlesPlayed++;
    
    console.log("🔄 Loaded puzzle", puzzlesPlayed, "of", config.games); // ✅ Debug: track puzzle loading progress
  } catch (error) {
    console.error("Error loading puzzle:", error);
    document.getElementById('feedback').textContent = "⚠️ Failed to load puzzle. Please try again.";
  }
}

/*Function: checkAnswer
   Purpose: Compare user's input with the correct answer */
function checkAnswer() {
  const userAnswer = parseInt(document.getElementById('answerInput').value);
  const feedback = document.getElementById('feedback');

  //<!-- Interesting Features - User-Friendly Design-->
  if (isNaN(userAnswer)) {
    feedback.textContent = "⚠️ Please enter a number!";
    return;
  }

  if (userAnswer === correctAnswer) {
    feedback.textContent = "✅ Correct!";
    feedback.className = "correct";
    currentScore += 10;
    correctCount++;
    document.getElementById('currentScore').textContent = currentScore;

    // ✅ Check if level should end after correct answer
    console.log("✅ Correct answer! Checking if level should end...");
    console.log("📊 Current stats - Puzzles played:", puzzlesPlayed, "Score:", currentScore);
    
    const config = levelConfig[currentLevel];
    if (puzzlesPlayed >= config.games || currentScore >= 100) {
      console.log("🎯 Level completion condition met! Calling saveScore()...");
      saveScore();
    } else {
      setTimeout(loadPuzzle, 2000);
    }
  } else {
    feedback.textContent = "❌ Wrong!";
    feedback.className = "wrong";
    attemptsLeft--;
    wrongCount++;
    document.getElementById('attemptsLeft').textContent = attemptsLeft;

    if (attemptsLeft <= 0) {
      feedback.textContent = "⏳ Out of attempts! Loading next puzzle...";
      console.log("❌ Out of attempts! Calling saveScore()...");
      saveScore();
    }
  }
}

/*Function: startTimer <!--Event Driven - Timer Events-->
   Purpose: Start countdown timer for each level */
function startTimer() {
  if (timerInterval) clearInterval(timerInterval);

  timerInterval = setInterval(() => {
    timeRemaining--;
    document.getElementById('timeRemaining').textContent = formatTime(timeRemaining);

    if (timeRemaining <= 0) {
      clearInterval(timerInterval);
      document.getElementById('feedback').textContent = "⏰ Time's up! Level ended.";
      saveScore(); // ✅ Save and redirect
    }
  }, 1000);
}

/*Function: formatTime
   Purpose: Convert seconds into MM:SS format */
function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
}

/*Function: initializeGame
   Purpose: Called when a level is selected */
function initializeGame(level) {
  currentLevel = level;
  puzzlesPlayed = 0;
  currentScore = 0;
  correctCount = 0;
  wrongCount = 0;

  const config = levelConfig[currentLevel];
  timeRemaining = config.time;

  document.getElementById('currentScore').textContent = currentScore;
  document.getElementById('attemptsLeft').textContent = attemptsLeft;
  document.getElementById('timeRemaining').textContent = formatTime(timeRemaining);

  document.getElementById('gameArea').style.display = 'block';

  startTimer();
  loadPuzzle();
}

/* Updated Function: saveScore
   Purpose: Send score data to save_score.php and redirect*/
function saveScore() {
  const timeTaken = levelConfig[currentLevel].time - timeRemaining;

  console.log("🔄 saveScore() FUNCTION CALLED!"); // ✅ Debug: confirm function is called
  console.log("📊 Final Score Data to Save:", { // ✅ Debug: log the data being sent
    level: currentLevel,
    score: currentScore,
    correct: correctCount,
    wrong: wrongCount,
    time: timeTaken,
    puzzlesPlayed: puzzlesPlayed
  });

  // Stop the timer
  if (timerInterval) {
    clearInterval(timerInterval);
    console.log("⏰ Timer stopped for score saving");
  }

  // ✅ Create form data for sending
  const formData = new URLSearchParams();
  formData.append('level', currentLevel);
  formData.append('score', currentScore);
  formData.append('correct', correctCount);
  formData.append('wrong', wrongCount);
  formData.append('time', timeTaken);

  console.log("📨 Sending data to php/save_score.php...");

  fetch('php/save_score.php', {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/x-www-form-urlencoded' 
    },
    body: formData
  })
  .then(response => {
    console.log("📨 Response status:", response.status, response.statusText);
    return response.text();
  })
  .then(data => {
    console.log("✅ PHP Response:", data);
    
    if (data.includes("✅ Score saved successfully")) {
      console.log("🎉 Score saved successfully! Redirecting to scoreboard...");
      alert("Score saved! Redirecting to scoreboard...");
      window.location.href = 'scoreboard.html';
    } else {
      console.error("❌ Score save failed. PHP returned:", data);
      alert("❌ Failed to save score. Error: " + data);
      // Still redirect to show something
      window.location.href = 'scoreboard.html';
    }
  })
  .catch(error => {
    console.error("🚨 Network error saving score:", error);
    alert("❌ Network error while saving score: " + error.message);
    window.location.href = 'scoreboard.html';
  });
}

/* Temporary function: manualSaveForTesting
   Purpose: Manually trigger score saving for testing */
function manualSaveForTesting() {
  console.log("🔧 MANUAL SAVE TRIGGERED FOR TESTING");
  currentScore = 50; // Set a test score
  correctCount = 5;  // Set test correct answers
  wrongCount = 2;    // Set test wrong answers
  saveScore();
}

// Add this to your HTML temporarily for testing:
// <button onclick="manualSaveForTesting()" style="position:fixed; top:10px; right:10px; z-index:1000;">TEST SAVE</button>

/*Auto-start game based on URL parameter <!--Event Driven - Page Loading-->*/
window.onload = function() {
  const params = new URLSearchParams(window.location.search);
  const level = parseInt(params.get("level")) || 1;
  document.getElementById("levelNum").textContent = level;
  initializeGame(level);
};
