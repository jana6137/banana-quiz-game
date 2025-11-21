/**
 * Function: startGame
 * -------------------
 * @param {number} level - The level number chosen by the user.
 */
function startGame(level) {
  try {
    // Save the selected level in localStorage
    localStorage.setItem("selectedLevel", level);

    // Log the action for debugging
    console.log(`Level ${level} selected. Redirecting to game.html...`);

    // Redirect to the game page
    window.location.href = "game.html";
  } catch (error) {
    console.error("Error starting game:", error);
    alert("⚠️ Unable to start the game. Please try again.");
  }
}

/**
 * Function: loadSelectedLevel
 * ---------------------------
 */
function loadSelectedLevel() {
  try {
    // Retrieve the selected level from localStorage
    const level = localStorage.getItem("selectedLevel");

    // If a level was found, display it
    if (level && document.getElementById("levelNum")) {
      document.getElementById("levelNum").textContent = level;
      console.log(`Loaded Level ${level} into game.html`);
    } else {
      // If no level is found, redirect back to dashboard
      console.warn("No level selected. Redirecting to dashboard...");
      window.location.href = "dashboard.html";
    }
  } catch (error) {
    console.error("Error loading selected level:", error);
    alert("⚠️ Unable to load the selected level. Returning to dashboard.");
    window.location.href = "dashboard.html";
  }
}

/**
 * Function: clearProgress
 * -----------------------
 */
function clearProgress() {
  localStorage.removeItem("selectedLevel");
  localStorage.removeItem("currentScore");
  localStorage.removeItem("attemptsLeft");
  console.log("Progress cleared. Returning to dashboard.");
  window.location.href = "dashboard.html";
}

/*Page Initialization */
window.onload = function () {
  // Check if we are on the game page
  if (document.getElementById("levelNum")) {
    loadSelectedLevel();
  }
};
