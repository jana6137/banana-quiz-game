/**
 * Redirects the user to a given page.
 * @param {string} targetPage - The page to redirect to (default: dashboard.html).
 * @param {number} delay - Optional delay in milliseconds before redirect.
 */
function redirectToPage(targetPage = "dashboard.html", delay = 0) {
  try {
    // Log the redirection action (useful for debugging)
    console.log(`Redirecting user to: ${targetPage} in ${delay}ms`);

    // If a delay is specified, wait before redirecting
    if (delay > 0) {
      setTimeout(() => {
        window.location.href = targetPage;
      }, delay);
    } else {
      // Immediate redirect
      window.location.href = targetPage;
    }

  } catch (error) {
    // Handle unexpected errors gracefully
    console.error("Redirection failed:", error);
    alert("⚠️ Something went wrong while redirecting. Please try again.");
  }
}

// Redirect immediately to dashboard after login
function redirectToDashboard() {
  redirectToPage("dashboard.html");
}

// Redirect to scoreboard after finishing the game
function redirectToScoreboard() {
  redirectToPage("scoreboard.html");
}

// Redirect to login page after logout with a 2-second delay
function redirectToLogin() {
  redirectToPage("login.html", 2000);
}
