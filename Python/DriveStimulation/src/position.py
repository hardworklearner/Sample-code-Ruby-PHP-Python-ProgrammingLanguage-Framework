class Position:
  def __init__(self, x, y):
    self.x = x
    self.y = y
    pass

  def move(self, dx: int, dy: int):
    """Move the position by adding dx and dy to x and y, ensuring they don't go below zero."""
    self.x = self.x + dx if (self.x + dx) >= 0 else 0
    self.y = self.y + dy if (self.y + dy) >= 0 else 0

  def change(self, x: int, y: int):
    """Change the position to the given x and y, ensuring they don't go below zero."""
    if x < 0 or y < 0:
      raise ValueError("Position coordinates cannot be negative.")
    self.x = x if x >= 0 else self.x
    self.y = y if y >= 0 else self.y

  def __eq__(self, other):
    """Override equality to compare positions."""
    if isinstance(other, Position):
        return self.x == other.x and self.y == other.y
    return False

  def __repr__(self):
    return f"({self.x}, {self.y})"
  