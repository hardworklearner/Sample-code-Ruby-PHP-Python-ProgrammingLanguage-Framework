import sys
import os
import pytest

sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from src import Stimulation, Field, Car, Position

def test_stimulation_init():
  f = Field(10, 10)
  c = Car("A", Position(0, 0), "N")
  s = Stimulation(f)
  s.add_car(c, "FFFR")
  assert s.field == f
  assert s.car_collided_list == {}
  assert s.command_list == {'A': 'FFFR'}
  assert s

def test_stimulation_add_car():
  f = Field(10, 10)
  c = Car("A", Position(0, 0), "N")
  s = Stimulation(f)
  s.add_car(c, "FFFR")
  car2 = Car("B", Position(1, 1), "N")
  s.add_car(car2, "FFFR")
  assert s.field == f
  assert s.car_collided_list == {}
  assert s.command_list == {'A': 'FFFR', 'B': 'FFFR'}
  assert s

def test_stimulation_add_car_collision():
  f = Field(10, 10)
  c = Car("A", Position(0, 0), "N")
  s = Stimulation(f)
  s.add_car(c, "FFFR")
  car2 = Car("B", Position(0, 0), "N")
  with pytest.raises(ValueError):
    s.add_car(car2, "FFFR")

  car3 = Car("A", Position(0, 1), "N")
  with pytest.raises(ValueError):
    s.add_car(car3, "FFFR")

def test_stimulation_add_car_invalid_command():
  f = Field(10, 10)
  c = Car("A", Position(0, 0), "N")
  s = Stimulation(f)
  with pytest.raises(ValueError):
    s.add_car(c, None)

  with pytest.raises(ValueError):
    s.add_car(c, "")

def test_stimulation_run():
  f = Field(10, 10)
  c = Car("A", Position(0, 0), "N")
  s = Stimulation(f)
  s.add_car(c, "FFFR")
  assert s.field == f
  assert s.car_collided_list == {}
  assert s.command_list == {'A': 'FFFR'}
  assert s
  s.run()
  assert s.field.fields[c.name] == c
  assert s.field.fields[c.name].position.x == 0
  assert s.field.fields[c.name].position.y == 3
  assert s.field.fields[c.name].direction == "E"
  assert s.car_collided_list == {}
  assert s.command_list == {'A': 'FFFR'}
  assert s

def test_stimulation_run_collision():
  f = Field(10, 10)
  c = Car("A", Position(1, 1), "E")
  s = Stimulation(f)
  s.add_car(c, "F")
  car2 = Car("B", Position(2, 1), "W")
  s.add_car(car2, "F")
  s.run()
  assert c.position == Position(2, 1)
  assert car2.position == Position(2, 1)
  assert "A" in s.car_collided_list
  assert "B" in s.car_collided_list

def test_stimulation_run_with_invalid_command():
  field = Field(5, 5)
  car_a = Car("A", Position(0, 0), "E")

  stimulation = Stimulation(field)
  stimulation.add_car(car_a, "XFL")  # Invalid command 'X'

  stimulation.run()

  assert car_a.position == Position(1, 0)  # Car should only move with valid commands
  assert "A" not in stimulation.car_collided_list  # No collision occurred

def test_stimulation_run_out_of_bounds():
  field = Field(3, 3)  # Smaller field to test boundaries
  car_a = Car("A", Position(2, 2), "E")  # Start at edge

  stimulation = Stimulation(field)
  stimulation.add_car(car_a, "FFF")  # Moves outside field boundaries

  stimulation.run()

  assert car_a.position == Position(2, 2)  # Car should stop at boundary
  assert "A" not in stimulation.car_collided_list  # No collision

def test_stimulation_run_duplicate_car():
  field = Field(5, 5)
  car_a = Car("A", Position(0, 0), "E")

  stimulation = Stimulation(field)
  stimulation.add_car(car_a, "F")

  with pytest.raises(ValueError, match="Car with name 'A' already exists"):
    stimulation.add_car(car_a, "LR")

def test_stimulation_run_collision_multiple_commands():
  f = Field(10, 10)
  c = Car("A", Position(1, 2), "N")
  s = Stimulation(f)
  s.add_car(c, "FFRFFFFRRL")
  car2 = Car("B", Position(7, 8), "W")
  s.add_car(car2, "FFLFFFFFFF")
  s.run()
  assert c.position == Position(5, 4)
  assert car2.position == Position(5, 4)
  assert "A" in s.car_collided_list
  assert "B" in s.car_collided_list

def test_stimulation_run_collision_with_wall():
  f = Field(10, 10)
  c = Car("A", Position(1, 2), "N")
  s = Stimulation(f)
  s.add_car(c, "FFRFFFFRRL")
  car2 = Car("B", Position(7, 8), "E")
  s.add_car(car2, "FFLFFFFFFF")
  s.run()
  assert c.position == Position(5, 4)
  assert car2.position == Position(9, 9)

def test_simulation_with_swapped_axes_and_collisions():
    # Create a 100x100 field
    field = Field(100, 100)
    stimulation = Stimulation(field)

    # Define 10 cars with unique starting positions and directions
    cars = [
        Car("A", Position(0, 0), "N"),   # Moves north along the Y-axis
        Car("B", Position(10, 10), "E"), # Moves east along the X-axis
        Car("C", Position(99, 99), "W"), # Moves west and hits the western boundary
        Car("D", Position(50, 51), "S"), # Moves south along Y, collides with F
        Car("E", Position(20, 20), "N"), # Valid movement along Y
        Car("F", Position(50, 49), "N"), # Collides with D at (50, 50)
        Car("G", Position(0, 99), "W"),  # Hits western boundary at (0, 99)
        Car("H", Position(90, 0), "S"),  # Moves south without collision
        Car("I", Position(80, 80), "E"), # Hits eastern boundary at (99, y)
        Car("J", Position(25, 25), "N")  # Moves north without issues
    ]

    # Assign commands to each car
    commands = {
        "A": "FFFFF",      # Moves north along the Y-axis to (0, 5)
        "B": "FFFFFF",     # Moves east along the X-axis to (16, 10)
        "C": "FFFFFFFFF",  # Moves west to (90, 99)
        "D": "F",          # Moves south, collides with F at (50, 50)
        "E": "FFFF",       # Moves north to (20, 24)
        "F": "F",          # Moves north to (50, 50), collides with D
        "G": "FFFF",       # Hits the western boundary at (0, 99)
        "H": "FF",         # Moves south to (90, 2)
        "I": "FFFFFFFF",   # Moves east, hits boundary at (99, 80)
        "J": "FFF"         # Moves north to (25, 28)
    }

    # Add cars to the simulation
    for car, command in zip(cars, commands.values()):
        stimulation.add_car(car, command)

    # Run the simulation
    stimulation.run()

    # Check for collision between D and F
    assert "D" in stimulation.car_collided_list
    assert "F" in stimulation.car_collided_list

    # Verify the final positions for each car
    assert cars[0].position == Position(0, 5)   # A moved north
    assert cars[1].position == Position(16, 10) # B moved east
    assert cars[2].position == Position(90, 99) # C stopped west
    assert cars[3].position == Position(50, 50) # D collided with F
    assert cars[4].position == Position(20, 24) # E moved north
    assert cars[5].position == Position(50, 50) # F collided with D
    assert cars[6].position == Position(0, 99)  # G hit the western boundary
    assert cars[7].position == Position(90, 0)  # H moved south hit south boundary
    assert cars[8].position == Position(88, 80) # I hit the eastern boundary
    assert cars[9].position == Position(25, 28) # J moved north
