# Driver Simulation

An implemented car simulation in Python, designed using Object-Oriented Programming (OOP), Test Driven Development (TDD), and Domain Driven Design (DDD).

## Overview

An Implemented Car Stimulation by Python code. This implemented code follow Object oriented programming and design, Test Driven Domain(TDD), and Domain Driven Design(DDD).

### Auto Driving Car Simulation

You are tasked with developing a simulation program for an autonomous driving car, aimed at competing with Tesla. Your team has already developed a prototype car, but it is still in its primitive stage.

The simulation program operates within a rectangular field defined by its width and height. The bottom left corner is at (0,0), and the top right is at (width,height). For example, a field with dimensions 10 x 10 has its upper right corner at (9,9).

One or more cars can be added to the field, each with a unique name, starting position, and facing direction (N, S, E, W).

### Commands

- **L**: Rotate the car 90 degrees to the left.
- **R**: Rotate the car 90 degrees to the right.
- **F**: Move forward by 1 grid point.

If a car tries to move beyond the boundary of the field, the command is ignored.

## Getting Started

### Prerequisites

- Python 3.x installed on your machine.

### Installation

1. Clone the repository:

```bash
git clone git@github.com:zmerrychristmas/Sample-code-Ruby-PHP-Python-ProgrammingLanguage-Framework.git
cd Python/DriverSimulation
```

2. Install required packages (if any):

```bash
pip install -r requirements.txt
```

3. Running the Simulationa

- Launch the program:

```bash
python cli.py
```

- Follow the command line prompts to set up the simulation field, add cars, and run the simulation.

## The full description below:

Auto Driving Car Simulation
You are tasked with developing a simulation program for an autonomous driving car, with the aim of competing with Tesla. Your team has already developed a prototype car, but it is still in its primitive stage.

The simulation program is designed to work with a rectangular field, specified by its width and height. The bottom left coordinate of the field is at position (0,0), and the top right position is denoted (width,height). For example, a field with dimensions 10 x 10 would have its upper right coordinate at position (9,9).

One or more cars can be added to the field, each with a unique name, starting position, and direction they are facing. For instance, a car named "A" may be placed at position (1,2) and facing North.

A list of commands can be issued to each car, which can be one of three commands:

L: rotates the car by 90 degrees to the left
R: rotates the car by 90 degrees to the right
F: moves forward by 1 grid point

If a car tries to move beyond the boundary of the field, the command is ignored, and the car stays in its current position. For example, if a car at position (0,0) is facing South and receives an F command, the command will be ignored as it would take the car beyond the boundary of the field.

Users can interact with your simulation program through the command line interface. Upon launching the program, users are prompted with the following message:

Welcome to Auto Driving Car Simulation!

Please enter the width and height of the simulation field in x y format:

User is then able to enter:

10 10

The system responds with:

You have created a field of 10 x 10.

Please choose from the following options:

[1] Add a car to field

[2] Run simulation

User is then able to enter:

1

The system responds with:

Please enter the name of the car:

User is then able to enter:

A

The system responds with:

Please enter initial position of car A in x y Direction format:

User is then able to enter:

1 2 N

Please note that only N, S, W, E (representing North, South, West, East) are allowed for direction.

The system responds with:

Please enter the commands for car A:

User is then able to enter:

FFRFFFFRRL

This means car A will move forward twice, turn right, move forward four times, turn right twice, and turn left once.

The system responds with:

Your current list of cars are:

- A, (1,2) N, FFRFFFFRRL

Please choose from the following options:

[1] Add a car to field

[2] Run simulation
Scenario 1 - Running simulation with a single car
At this point, there are a field, a car with initial position, facing, and commands available. If user attempts to run simulation, user can enter:

2

Then the system runs all the commands for car A, and responds with:

Your current list of cars are:

- A, (1,2) N, FFRFFFFRRL

After simulation, the result is:

- A, (5,4) S

Please choose from the following options:

[1] Start over

[2] Exit

If user chooses to start over, the system will show:

Welcome to Auto Driving Car Simulation!

Please enter the width and height of the simulation field in x y format:

If use choose to exit, the system will show:

Thank you for running the simulation. Goodbye!
Scenario 2 - Running simulation with multiple cars
After user adds one car to the field, user can also choose to continue to add more cars. Hence, following example above, when system responds with:

Your current list of cars are:

- A, (1,2) N, FFRFFFFRRL

Please choose from the following options:

[1] Add a car to field

[2] Run simulation

User then can enter:

1

The system then responds with:

Please enter the name of the car:

User is then able to enter:

B

The system responds with:

Please enter initial position of car B in x y Direction format:

User is then able to enter:

7 8 W

The system responds with:

Please enter the commands for car B:

User is then able to enter:

FFLFFFFFFF

Please note that the length of commands do not have to be the same. If a car runs out of command, it will stay put.

The system responds with:

Your current list of cars are:

- A, (1,2) N, FFRFFFFRRL

- B, (7,8) W, FFLFFFFFFF

Please choose from the following options:

[1] Add a car to field

[2] Run simulation

At this point, user can continue to add more cars or run simulation. If user tries to add new car, the program follows the process above. If user tries to run simulation, then user will enter:

2

Then the system will run all car A's commands and all car B's commands, then respond with:

Your current list of cars are:

- A, (1,2) N, FFRFFFFRRL

- B, (7,8) W, FFLFFFFFFF

After simulation, the result is:

- A, collides with B at (5,4) at step 7

- B, collides with A at (5,4) at step 7

Please choose from the following options:

[1] Start over

[2] Exit

When processing commands for multiple cars, at every step, only one command can be processed for each car.

Using the example above:

At step 1, car A moves forward, and car B moves forward.
At step 2, car A moves forward, and car B moves forward.
At step 3, car A turn right, and car B turns left.
So on and so forth for the rest of the commands.

If some cars collide at certain step, then collided cars stop moving and no longer process further commands.

If cars do not have collision, then the system will print the final positions following example in Scenario 1.

## An Implemented Code

Folder structure are:

- Position.py implemented represent location object of `X` and `Y` axis.
- Vehicle.py implemented represent location Vehicle class
- Cli.py implement interaction runtime
- Car.py implemented Car object extends from Vehicle object.
- Field.py implemented Field object.
- Stimulation.py implemented Stimulation object.
- tests folder store factory data to test attribute and action of class.

### Domain Driven Design

Domain driven design applied on this project by analysis domain business of Auto Car Driven Stimulation. To follow DDD:

- Car class is an Entity class declare attributes and behaviour of unique Car object
- Field class is Value Object Classes declare attributes of Field object
- Stimulation is class object stimulate Cars move in Field

### Test Driven Design

All test case will be founded in `Test` folder. Which following test class attributes include `Position`, `Car`, `Field`, `Stimulation` and behaviour of `Car` move in `Field` and also test `Stimulation` services.

- Automatically test are placed in `tests` folder with pair folders: `input` and `output`.

### Position Test Case

The `Position` class represents a point in a two-dimensional space with two attributes, `x` and `y`, corresponding to the coordinates on the x-axis and y-axis, respectively.

#### Attributes:

- **`x`**: Represents the horizontal coordinate (non-negative integer).
- **`y`**: Represents the vertical coordinate (non-negative integer).

Both `x` and `y` should always be non-negative numbers, ensuring that the position is valid within the designated space.

#### Example:

```py
# Create a position at (3, 4)
pos = Position(3, 4)
print(pos.x)  # Output: 3
print(pos.y)  # Output: 4
```

### Car Test case

The Car class models a vehicle with a unique identifier and a position in space. The car can move in different directions based on commands provided during the simulation.

#### Attributes:

name: A string representing the unique identifier of the car.
position: An instance of the Position class representing the car's current location.
direction: A string indicating the car's current facing direction (e.g., "N", "E", "S", "W").

#### Example:

```py
# Create a car named "CarA" at position (0, 0) facing North
car = Car("CarA", Position(0, 0), "N")
print(car.name)          # Output: CarA
print(car.position.x)    # Output: 0
print(car.position.y)    # Output: 0
print(car.direction)      # Output: N
```

### Field Test Case

The `Field` class represents a defined area where cars can operate within specified dimensions. It ensures that cars remain within the boundaries of the field during the simulation.

#### Attributes:

- **`width`**: The width of the field (non-negative integer).
- **`height`**: The height of the field (non-negative integer).
- **`fields`**: A dictionary that maps car names to their corresponding `Position` objects, tracking their locations within the field.

#### Example:

```python
# Create a field with dimensions 10x10
field = Field(10, 10)

# Check the field dimensions
print(field.width)  # Output: 10
print(field.height) # Output: 10

# Add a car to the field at a specific position
car = Car("CarA", Position(5, 5), "N")
field.fields[car.name] = car.position

# Check the car's position in the field
print(field.fields["CarA"].x)  # Output: 5
print(field.fields["CarA"].y)  # Output: 5
```

### Stimulation Test Case

The Stimulation class orchestrates the simulation of car movements within a field. It manages the addition of cars, their commands for movement, and handles collision detection.

#### Attributes:

field: An instance of the Field class representing the area where the simulation occurs.
car_collided_list: A dictionary that keeps track of cars that have collided during the simulation.
command_list: A dictionary that maps car names to their respective movement commands, allowing for coordinated actions.

#### Example:

```py
# Create a field and a stimulation instance
field = Field(10, 10)
simulation = Stimulation(field)

# Add a car to the simulation with a movement command
car_a = Car("CarA", Position(0, 0), "N")
simulation.add_car(car_a, "FFFR")  # Move the car forward, then turn right

# Run the simulation
simulation.run()

# Check the final position of the car
print(car_a.position.x)  # Output: 0 (unchanged)
print(car_a.position.y)  # Output: 3 (moved forward)
print(car_a.direction)    # Output: E (turned right)

# Check for collisions
print(simulation.car_collided_list)  # Output: {} (no collisions occurred)
```

#### Test Cases:

Verify that cars are added to the simulation correctly with valid commands.
Test the simulation's ability to execute movement commands and update car positions accordingly.
Ensure that collisions are detected when cars occupy the same position after running commands.
Confirm that the simulation handles invalid commands gracefully without affecting valid car movements.

### Car implemented class

Car Implemented Class

The `Car` class represents a vehicle within the simulation. Each car has a unique identifier, a position on the field, and a direction in which it is facing. The class provides functionality to manage the car's state and behavior during the simulation.

#### Attributes:

- **`name`** (str): A unique identifier for the car (e.g., "CarA"). This name must be unique within the simulation.
- **`position`** (Position): An instance of the `Position` class that defines the car's current coordinates on the field.
- **`direction`** (str): A string representing the car's current facing direction. Possible values are "N" (North), "E" (East), "S" (South), and "W" (West).

#### Example:

```python
# Create a car instance with a unique name, position, and direction
car_a = Car("CarA", Position(0, 0), "N")

# Accessing car attributes
print(car_a.name)       # Output: CarA
print(car_a.position.x) # Output: 0
print(car_a.position.y) # Output: 0
print(car_a.direction)   # Output: N

# Update the car's position and direction as needed
car_a.position = Position(1, 0)  # Move the car to (1, 0)
car_a.direction = "E"             # Change direction to East
```

### Field Implemented Class

The `Field` class represents the environment in which the simulation takes place. It defines the boundaries of the simulation area and manages the placement and movement of cars within this space.

#### Attributes:

- **`width`** (int): The width of the field, representing the number of units along the X-axis.
- **`height`** (int): The height of the field, representing the number of units along the Y-axis.
- **`fields`** (dict): A dictionary that stores the current positions of cars on the field. The keys are the car names, and the values are the corresponding `Car` objects.

#### Example:

```python
# Create a field instance with specified width and height
field = Field(10, 10)

# Accessing field attributes
print(field.width)   # Output: 10
print(field.height)  # Output: 10
print(field.fields)  # Output: {} (initially empty)

# Adding a car to the field
car_a = Car("CarA", Position(0, 0), "N")
field.fields[car_a.name] = car_a

# Checking the current positions of cars on the field
print(field.fields)  # Output: {'CarA': <Car object at ...>}
```

### Stimulation Implemented Class

The `Stimulation` class orchestrates the interaction between cars and the field, managing their movements and handling collisions. It serves as the central control unit for simulating the behavior of vehicles within a defined environment.

#### Attributes:

- **`field`** (Field): An instance of the `Field` class that represents the environment where the cars operate.
- **`car_collided_list`** (dict): A dictionary that tracks cars that have collided during the simulation, with car names as keys and their corresponding collision details as values.
- **`command_list`** (dict): A dictionary that stores the commands associated with each car, where the keys are car names and the values are the movement commands (e.g., "FFFR").

#### Example:

```python
# Create a field instance
field = Field(10, 10)

# Initialize the stimulation with the field
stimulation = Stimulation(field)

# Add a car with commands
car_a = Car("CarA", Position(0, 0), "N")
stimulation.add_car(car_a, "FFFR")  # Add CarA with movement commands

# Accessing stimulation attributes
print(stimulation.field)              # Output: <Field object at ...>
print(stimulation.car_collided_list)  # Output: {}
print(stimulation.command_list)        # Output: {'CarA': 'FFFR'}
```

### Collision Logic

The collision logic in the simulation handles interactions between cars, ensuring that their movements are accurately represented while preventing illegal overlaps. This section outlines how collisions are detected, managed, and recorded within the simulation.

#### Key Concepts

1. **Collision Detection**: The simulation continuously checks the positions of cars after each movement to determine if two or more cars occupy the same location. If a collision is detected, appropriate measures are taken to manage the situation.

2. **Collision Handling**: When a collision occurs, the involved cars are marked in the `car_collided_list`. This allows for easy tracking of which cars have collided during the simulation, enabling further analysis or reporting as needed.

#### Example Collision Detection Logic

Here is an example of how collision detection might be implemented in the `Stimulation` class:

```python
def check_collision(self, car):
    # Get the position of the car
    position = car.position

    # Check if any other car occupies the same position
    for other_car in self.field.fields.values():
        if other_car != car and other_car.position == position:
            # Collision detected
            self.car_collided_list[car.name] = "Collided with " + other_car.name
            self.car_collided_list[other_car.name] = "Collided with " + car.name
            return True
    return False
```

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss changes.

## License

This project is licensed under the MIT License - see the LICENSE file for details.
