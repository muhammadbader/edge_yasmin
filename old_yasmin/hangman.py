import random

# ---------------------------
# Configuration
# ---------------------------
WORDS = [
    "python", "robotics", "artificial", "machine", "learning",
    "flutter", "developer", "science", "technology", "computer"
]

HANGMAN_STAGES = [
    """
      +---+
          |
          |
          |
         ===
    """,
    """
      +---+
      O   |
          |
          |
         ===
    """,
    """
      +---+
      O   |
      |   |
          |
         ===
    """,
    """
      +---+
      O   |
     /|   |
          |
         ===
    """,
    """
      +---+
      O   |
     /|\\  |
          |
         ===
    """,
    """
      +---+
      O   |
     /|\\  |
     /    |
         ===
    """,
    """
      +---+
      O   |
     /|\\  |
     / \\  |
         ===
    """
]

# ---------------------------
# Functions
# ---------------------------

def choose_word():
    """Return a random word from the list."""
    return random.choice(WORDS)

def display_state(word_display, guessed_letters, tries):
    """Display current game state."""
    print(HANGMAN_STAGES[len(HANGMAN_STAGES) - 1 - tries])
    print("Word: ", " ".join(word_display))
    print(f"Guessed letters: {', '.join(sorted(guessed_letters))}")
    print(f"Remaining tries: {tries}\n")

def get_guess(guessed_letters):
    """Ask player for a letter and validate input."""
    while True:
        guess = input("Enter a letter: ").lower().strip()
        if not guess.isalpha() or len(guess) != 1:
            print("❌ Please enter a single alphabetic character.\n")
        elif guess in guessed_letters:
            print("⚠️ You already guessed that letter.\n")
        else:
            return guess

def update_display(word, word_display, guess):
    """Update the displayed word with correct guesses."""
    for i, letter in enumerate(word):
        if letter == guess:
            word_display[i] = guess

def check_win(word_display):
    """Check if the player has guessed all letters."""
    return "_" not in word_display

def play_hangman():
    """Main game loop."""
    print("🎯 Welcome to Hangman!")
    word = choose_word()
    guessed_letters = set()
    tries = len(HANGMAN_STAGES) - 1
    word_display = ["_"] * len(word)

    while tries > 0 and not check_win(word_display):
        display_state(word_display, guessed_letters, tries)
        guess = get_guess(guessed_letters)
        guessed_letters.add(guess)

        if guess in word:
            print("✅ Good guess!\n")
            update_display(word, word_display, guess)
        else:
            print("❌ Wrong guess.\n")
            tries -= 1

    end_game(word, word_display)

def end_game(word, word_display):
    """Handle end of game messages."""
    if check_win(word_display):
        print("🎉 Congratulations! You guessed the word:", word)
    else:
        print(HANGMAN_STAGES[-1])
        print("💀 Game Over! The word was:", word)

# ---------------------------
# Run Game
# ---------------------------
if __name__ == "__main__":
    play_hangman()
