# Import necessary modules
import sys
import os
import pytest  # Testing framework used for assertions and error checking

# Add the parent directory to the system path to import the `Position` module from src
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

# Import the Position class from the src module
from src import Position

def test_initial_position():
    """Test that the initial position is set correctly."""
    # Create a Position object with x=3, y=5
    pos = Position(3, 5)
    # Assert that the coordinates are correctly initialized
    assert pos.x == 3
    assert pos.y == 5

def test_move_position():
    """Test moving the position with given offsets."""
    # Move the position by (1, 3)
    pos = Position(2, 4)
    pos.move(1, 3)  
    # Assert the new coordinates are correct
    assert pos.x == 3
    assert pos.y == 7

    # Move the position by (-2, -4) (back to origin)
    pos = Position(2, 4)
    pos.move(-2, -4)  
    # Assert the coordinates are reset to (0, 0)
    assert pos.x == 0
    assert pos.y == 0

def test_equality_position():
    """Test that two positions are considered equal if they have the same coordinates."""
    # Create two identical Position objects
    pos1 = Position(1, 2)
    pos2 = Position(1, 2)
    # Assert they are considered equal
    assert pos1 == pos2  

def test_inequality_position():
    """Test that two positions with different coordinates are not equal."""
    # Create two different Position objects
    pos1 = Position(1, 2)
    pos2 = Position(2, 3)
    # Assert they are not equal
    assert pos1 != pos2  

def test_move_position_with_negative_values():
    """Test moving the position where the result would be less than 0, ensuring it stays at 0."""
    # Move the position to test handling of negative values
    pos = Position(2, 3)
    pos.move(-5, -1)  
    # Assert that the position stays within bounds
    assert pos.x == 0  
    assert pos.y == 2  

def test_move_with_negative_x_value():
    """Test moving the position where the x value would be less than 0, ensuring it stays at 0."""
    pos = Position(2, 3)
    pos.move(-5, 0)  
    # Assert x does not go below 0
    assert pos.x == 0  
    assert pos.y == 3  

def test_move_with_negative_y_value():
    """Test moving the position where the y value would be less than 0, ensuring it stays at 0."""
    pos = Position(2, 3)
    pos.move(0, -5)  
    # Assert y does not go below 0
    assert pos.x == 2  
    assert pos.y == 0  

def test_move_not_change():
    """Test moving the position where x, y do not change when moved by (0, 0)."""
    pos = Position(2, 3)
    pos.move(0, 0)  
    # Assert the position remains unchanged
    assert pos.x == 2  
    assert pos.y == 3  

def test_change_position():
    """Test changing the position with given new coordinates."""
    pos = Position(2, 4)
    pos.change(1, 3)  
    # Assert the new coordinates are correctly set
    assert pos.x == 1
    assert pos.y == 3

def test_change_wrong_position_x():
    """Test that changing the x-coordinate to a negative value raises a ValueError."""
    with pytest.raises(ValueError):
        pos = Position(2, 4)
        pos.change(-1, 3)  # This should raise an error

def test_change_wrong_position_y():
    """Test that changing the y-coordinate to a negative value raises a ValueError."""
    with pytest.raises(ValueError):
        pos = Position(2, 4)
        pos.change(1, -3)  # This should raise an error

def test_change_wrong_position():
    """Test that changing any coordinate to a negative value raises a ValueError."""
    with pytest.raises(ValueError):
        pos = Position(2, 4)
        pos.change(1, -3)  # This should raise an error

def test_repr_position():
    """Test the string representation of the position."""
    pos = Position(2, 4)
    # Assert the __repr__ method returns the correct string
    assert repr(pos) == "(2, 4)"

def test_change_position_to_zero_axis():
    """Test changing the position to the origin (0, 0)."""
    pos = Position(2, 4)
    pos.change(0, 0)  
    # Assert the position is set to (0, 0)
    assert pos.x == 0
    assert pos.y == 0

def test_fail_eq():
    """Test that a Position object is not considered equal to a tuple."""
    pos = Position(2, 4)
    # Assert the position is not equal to a tuple with the same values
    assert pos != (2, 4)
