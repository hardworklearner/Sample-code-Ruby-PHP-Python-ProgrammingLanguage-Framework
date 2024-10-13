from src import Vehicle, Position
from src.command import car_direction, car_move_command, car_turn

class Car(Vehicle):
  def __init__(self, name, position: Position, direction):
    super().__init__(name)
    if direction is None or direction == "":
      raise ValueError('Direction cannot be empty')
    elif direction not in car_direction:
      raise ValueError('Invalid direction')
    else:
      self.direction = direction
    
    if position is None:
      raise ValueError('Position cannot be empty')
    elif not isinstance(position, Position):
      raise ValueError('Invalid position')
    else:
      self.position = position 
    pass
  
  def move_forward(self):
    if self.direction == 'N':
      self.position.y += 1
    elif self.direction == 'S':
      self.position.y -= 1 if self.position.y > 0 else 0
    elif self.direction == 'E':
      self.position.x += 1
    elif self.direction == 'W':
      self.position.x -= 1 if self.position.x > 0 else 0

    
  def move(self, move_command):
    # Implement car movement logic
    if move_command not in car_move_command:
      raise ValueError('Invalid move command')
    elif move_command == 'F':
      self.move_forward()
    elif move_command == 'L':
      self.turn('L')
    elif move_command == 'R':
      self.turn('R')
    pass

  def turn(self, new_direction):
    if new_direction not in car_turn:
      raise ValueError('Invalid turn direction')
    if new_direction == 'L':
      if self.direction == 'N':
        self.direction = 'W'
      elif self.direction == 'W':
        self.direction = 'S'
      elif self.direction == 'S':
        self.direction = 'E'
      elif self.direction == 'E':
        self.direction = 'N'
    elif new_direction == 'R':
      if self.direction == 'N':
        self.direction = 'E'
      elif self.direction == 'E':
        self.direction = 'S'
      elif self.direction == 'S':
        self.direction = 'W'
      elif self.direction == 'W':
        self.direction = 'N'
    pass

  def __eq__(self, other):
    """Override equality to compare cars."""
    if isinstance(other, Car):
        return self.name == other.name
    return False
  
  def __repr__(self):
    return f"({self.name}, {self.position}, {self.direction})"
