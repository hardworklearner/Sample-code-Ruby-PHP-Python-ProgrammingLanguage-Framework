# Import necessary modules
import sys
import os
import pytest

# Add the parent directory to the system path for module import
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

# Import the Vehicle class from the src module
from src import Vehicle

def test_vehicle_creation():
    # Test the successful creation of a Vehicle object with a valid name
    vehicle = Vehicle("ABC123")  # Create a vehicle with the name "ABC123"
    assert vehicle.name == "ABC123"  # Verify that the vehicle's name is correctly set

def test_vehicle_creation_with_empty_name():
    # Test the creation of a Vehicle object with an empty name
    with pytest.raises(ValueError):
        Vehicle("")  # Expect a ValueError to be raised when an empty string is used as the name

def test_vehicle_creation_with_none_name():
    # Test the creation of a Vehicle object with None as the name
    with pytest.raises(ValueError):
        Vehicle(None)  # Expect a ValueError to be raised when None is used as the name
