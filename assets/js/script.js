class NavigationStack {
  constructor(maxSize = 10) {
      this.stack = [];
      this.maxSize = maxSize;
  }

  push(url) {
      console.log("Pushing URL:", url); 
      if (this.stack.length >= this.maxSize) {
          this.stack.shift(); // Remove oldest URL if stack is full
      }
      this.stack.push(url);
      this.saveToLocalStorage();
      updateBackButtonState(); 
  }

  pop() {
      console.log("Popping URL from stack"); 
      if (!this.isEmpty()) {
          this.stack.pop(); // Just pop, don't need the URL here
          this.saveToLocalStorage();
          updateBackButtonState(); 
          return true; // Indicate success
      }
      return false; // Indicate stack is empty
  }

  isEmpty() {
      return this.stack.length === 0;
  }

  loadFromLocalStorage() {
      const storedStack = localStorage.getItem('navigationHistory');
      if (storedStack) {
          this.stack = JSON.parse(storedStack);
      }
  }

  saveToLocalStorage() {
      localStorage.setItem('navigationHistory', JSON.stringify(this.stack));
  }
}

const navStack = new NavigationStack();

// Load navigation history from local storage on page load
navStack.loadFromLocalStorage();

function goBack() {
  if (navStack.pop()) { // Use pop() for its side effects and check return value
      history.back(); // Use history.back() for navigation
  } 
}

// Function to update the "Back" button's state
function updateBackButtonState() {
  const backButton = document.querySelector('button[onclick="goBack()"]'); 
  console.log("Back button element:", backButton); 

  if (!backButton) {
    console.error("Back button not found!");
    return; 
  }

  if (navStack.isEmpty()) {
      backButton.disabled = true;
      console.log("Navigation stack is empty, disabling back button"); 
  } else {
      backButton.disabled = false;
      console.log("Navigation stack has entries, enabling back button"); 
  }
}


// Add current page to navigation stack when the page loads OR when the URL changes
window.addEventListener('load', () => {
  navStack.push(window.location.href);
  updateBackButtonState(); 
});

// Listen for URL changes (e.g., manual URL entry, refresh)
window.addEventListener('popstate', () => {
  navStack.push(window.location.href);
  updateBackButtonState(); 
});