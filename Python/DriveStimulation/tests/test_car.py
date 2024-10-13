# content of test_sample.py
import sys
import os
import pytest

# Insert the parent directory into sys.path to allow importing from 'src'
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from src import Car, Position

def test_car_creation():
    """Test creating a car with valid name, position, and direction."""
    car = Car("ABC123", Position(0, 0), "N")
    assert car.name == "ABC123"
    assert car.position.x == 0
    assert car.position.y == 0
    assert car.direction == "N"

def test_car_creation_invalid_direction():
    """Test that creating a car with an invalid direction raises a ValueError."""
    with pytest.raises(ValueError):
        Car("ABC123", Position(0, 0), "X")

def test_car_creation_invalid_direction_empty():
    """Test that an empty direction raises a ValueError."""
    with pytest.raises(ValueError):
        Car("ABC123", Position(0, 0), "")

def test_car_creation_invalid_direction_none():
    """Test that a None direction raises a ValueError."""
    with pytest.raises(ValueError):
        Car("ABC123", Position(0, 0), None)

def test_car_creation_invalid_position_none():
    """Test that a None position raises a ValueError."""
    with pytest.raises(ValueError):
        Car("ABC123", None, "N")

def test_car_creation_invalid_position_not_instance_of_position():
    """Test that creating a car with invalid position types raises a ValueError."""
    with pytest.raises(ValueError):
        Car("ABC123", 0, "N")
    with pytest.raises(ValueError):
        Car("ABC123", "0,0", "N")

def test_car_creation_invalid_name_none():
    """Test that creating a car with a None name raises a ValueError."""
    with pytest.raises(ValueError):
        Car(None, Position(0, 0), "N")

def test_car_creation_invalid_name_empty():
    """Test that creating a car with an empty name raises a ValueError."""
    with pytest.raises(ValueError):
        Car("", Position(0, 0), "N")

def test_car_move_forward_north():
    """Test moving the car forward when facing north."""
    car = Car("ABC123", Position(0, 0), "N")
    car.move_forward()
    assert car.position.x == 0
    assert car.position.y == 1

def test_car_move_forward_east():
    """Test moving the car forward when facing east."""
    car = Car("ABC123", Position(0, 0), "E")
    car.move_forward()
    assert car.position.x == 1
    assert car.position.y == 0

def test_car_move_forward_south():
    """Test moving the car forward when facing south."""
    car = Car("ABC123", Position(0, 1), "S")
    car.move_forward()
    assert car.position.x == 0
    assert car.position.y == 0

def test_car_move_forward_west():
    """Test moving the car forward when facing west."""
    car = Car("ABC123", Position(1, 0), "W")
    car.move_forward()
    assert car.position.x == 0
    assert car.position.y == 0

def test_car_turn_left():
    """Test turning the car to the left from all directions."""
    car = Car("ABC123", Position(0, 0), "N")
    car.turn('L')
    assert car.direction == "W"
    car.turn('L')
    assert car.direction == "S"
    car.turn('L')
    assert car.direction == "E"
    car.turn('L')
    assert car.direction == "N"

def test_car_turn_right_north():
    """Test turning the car to the right from the north direction."""
    car = Car("ABC123", Position(0, 0), "N")
    car.turn('R')
    assert car.direction == "E"
    car.turn('R')
    assert car.direction == "S"
    car.turn('R')
    assert car.direction == "W"
    car.turn('R')
    assert car.direction == "N"

def test_car_turn_invalid_direction():
    """Test that an invalid turn direction raises a ValueError."""
    car = Car("ABC123", Position(0, 0), "N")
    with pytest.raises(ValueError):
        car.turn('X')

def test_car_move_command_forward():
    """Test moving the car forward using the 'F' command."""
    car = Car("ABC123", Position(0, 0), "N")
    car.move("F")
    assert car.position.x == 0
    assert car.position.y == 1

def test_car_move_command_turn_left():
    """Test turning the car left using the 'L' command."""
    car = Car("ABC123", Position(0, 0), "N")
    car.move("L")
    assert car.direction == "W"

def test_car_move_command_turn_right():
    """Test turning the car right using the 'R' command."""
    car = Car("ABC123", Position(0, 0), "N")
    car.move("R")
    assert car.direction == "E"

def test_car_move_invalid_direction():
    """Test that an invalid move command raises a ValueError."""
    car = Car("ABC123", Position(0, 0), "N")
    with pytest.raises(ValueError):
        car.move("X")

def test_car_move_command_invalid_command():
    """Test that an invalid command raises a ValueError."""
    car = Car("ABC123", Position(0, 0), "N")
    with pytest.raises(ValueError):
        car.move("X")

def test_car_move_command_invalid_command_empty():
    """Test that an empty command raises a ValueError."""
    car = Car("ABC123", Position(0, 0), "N")
    with pytest.raises(ValueError):
        car.move("")

def test_car_move_command_invalid_command_none():
    """Test that a None command raises a ValueError."""
    car = Car("ABC123", Position(0, 0), "N")
    with pytest.raises(ValueError):
        car.move(None)

def test_car_equality():
    """Test that two cars with the same name, position, and direction are equal."""
    car1 = Car("ABC123", Position(0, 0), "N")
    car2 = Car("ABC123", Position(0, 0), "N")
    assert car1 == car2

def test_car_inequality():
    """Test that two cars with different attributes are not equal."""
    car1 = Car("ABC123", Position(0, 0), "N")
    car2 = Car("ABC", Position(0, 1), "N")
    assert car1 != car2
