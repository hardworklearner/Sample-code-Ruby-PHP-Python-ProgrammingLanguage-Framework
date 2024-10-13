# content of test_sample.py
import sys
import os
import pytest

# Add the source directory to the Python path for importing modules correctly
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

# Import necessary classes from the src module
from src import Field, Position, Car

def test_initial_field():
    """Test the initialization of the Field object."""
    field = Field(3, 3)
    assert field.width == 3  # Check field width
    assert field.height == 3  # Check field height
    assert field.fields == {}  # Ensure the field is empty initially

def test_add_car_to_field():
    """Test adding cars to the field."""
    field = Field(3, 3)  # Create a field of size 3x3
    car = Car("A", Position(0, 0), "N")  # Create a car at (0, 0)

    # Add the first car and verify it is added
    field.add_car(car)
    assert len(field.fields) == 1
    assert field.fields["A"].position == car.position

    # Add a second car and verify both cars are in the field
    car2 = Car("B", Position(1, 1), "N")
    field.add_car(car2)
    assert len(field.fields) == 2
    assert field.fields["B"].position == car2.position

def test_add_car_out_of_field():
    """Test that adding a car outside the field's boundaries raises an error."""
    field = Field(3, 3)
    car = Car("A", Position(4, 4), "N")
    with pytest.raises(ValueError):
        field.add_car(car)

def test_add_car_collision():
    """Test that adding a car at an occupied position raises a collision error."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 0), "N")
    field.add_car(car1)
    with pytest.raises(ValueError):
        field.add_car(car2)

def test_add_car_collision_name():
    """Test that adding two cars with the same name raises an error."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("A", Position(0, 1), "N")
    field.add_car(car1)
    with pytest.raises(ValueError):
        field.add_car(car2)

def test_field_print():
    """Test the field's print function and car movement."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(1, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    field.car_move_by_command(car1, "F")  # Move car1 forward
    field.car_move_by_command(car2, "F")  # Move car2 forward
    assert car1.position == Position(0, 1)
    assert car2.position == Position(1, 2)
    field.field_print()

def test_car_move_by_command():
    """Test a valid move command for a car."""
    field = Field(3, 3)
    car = Car("A", Position(0, 0), "N")
    field.add_car(car)
    field.car_move_by_command(car, "F")  # Move forward
    assert car.position == Position(0, 1)
    assert field.car_collided_list == {}  # No collisions

def test_car_move_by_command_out_of_field():
    """Test that moving a car out of the field raises an error."""
    field = Field(3, 3)
    car = Car("A", Position(2, 2), "N")
    field.add_car(car)
    with pytest.raises(ValueError):
        field.car_move_by_command(car, "F")

def test_car_move_by_command_collision():
    """Test collision handling when two cars occupy the same position."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    field.car_move_by_command(car1, "F")  # Move car1 into car2's position
    assert field.car_collided_list == {'A': [car2], 'B': [car1]}

def test_car_move_by_command_invalid_command():
    """Test that an invalid move command raises an error."""
    field = Field(3, 3)
    car = Car("A", Position(0, 0), "N")
    field.add_car(car)
    with pytest.raises(ValueError):
        field.car_move_by_command(car, "X")  # Invalid command

def test_car_move_by_command_invalid_argument():
    """Test that passing incorrect arguments raises a TypeError."""
    field = Field(3, 3)
    car = Car("A", Position(0, 0), "N")
    field.add_car(car)
    with pytest.raises(TypeError):
        field.car_move_by_command("A", "F")  # Invalid car argument

def test_car_check_collision():
    """Test the collision detection system between cars."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    field.car_move_by_command(car1, "F")
    assert field.car_collided_list == {'A': [car2], 'B': [car1]}

def test_car_check_collision_empty():
    """Test that no collisions are detected when cars do not overlap."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    field.car_move_by_command(car1, "L")  # Turn left, no collision
    assert field.car_collided_list == {}

def test_field_print_with_collision():
    """Test the field print function after a collision occurs."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    field.car_move_by_command(car1, "F")  # Trigger collision
    field.field_print()
    assert field.car_collided_list == {'A': [car2], 'B': [car1]}

def test_field_print_without_collision():
    """Test the field print function when no collisions occur."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    field.field_print()
    assert field.car_collided_list == {}

def test_field_to_string():
    """Test the string representation of the field."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    field.car_move_by_command(car1, "F")
    rets = ("Field(width=3, height=3), cars = [(A, (0, 1), N)\n"
            "(B, (0, 1), N)\n],car_collided_list={'A': [(B, (0, 1), N)], "
            "'B': [(A, (0, 1), N)]}")
    assert str(field) == rets

def test_get_collision_list_of_car():
    """Test retrieving the collision list for a specific car."""
    field = Field(3, 3)
    car1 = Car("A", Position(0, 0), "N")
    car2 = Car("B", Position(0, 1), "N")
    field.add_car(car1)
    field.add_car(car2)
    assert field.get_collision_list_of_car(car1) == []  # No initial collisions
    field.car_move_by_command(car1, "F")  # Move into car2's position
    assert field.get_collision_list_of_car(car1) == [car2]
