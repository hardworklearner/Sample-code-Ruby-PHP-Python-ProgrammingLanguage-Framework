from src import Car, Position

class Field:
  def __init__(self, width: int, height: int):
    if width <= 0 or height <= 0:
      raise ValueError('Field dimensions must be positive')
    elif not isinstance(width, int) or not isinstance(height, int):
      raise TypeError('Field dimensions must be integers')
    else:
      self.width = width
      self.height = height
    # self.fields = [[None for _ in range(width)] for _ in range(height)]
    self.fields = {}
    self.car_collided_list = {}
    pass

  def add_car(self, car: Car):
    # Ensure the input is a Car instance
    if not isinstance(car, Car):
        raise TypeError('car must be an instance of Car')

    # Ensure the car's position is within the field bounds
    if car.position.x >= self.width or car.position.y >= self.height:
        raise ValueError('Car position out of field bounds')

    # Add the car to the field if it doesn't exist already
    if car.name in self.fields:
        raise ValueError(f"Car with name '{car.name}' already exists")

    # Add the car to the same position if it doesn't exist already
    for existing_car in self.fields.values():
        if existing_car.position == car.position:
            raise ValueError(f"Car with position {car.position} already exists")
        
    # Store the car by its name in the 'fields' dictionary
    self.fields[car.name] = car

  def car_move_by_command(self, car: Car, command: str):
    # Ensure the input is a Car instance
    if not isinstance(car, Car):
        raise TypeError('car must be an instance of Car')

    if car.name in self.car_collided_list:
      return car
    
    old_position = Position(car.position.x, car.position.y)
    self.fields[car.name].move(command)
    # Update car position in fields dictionary
    if command == 'F':
       if self.fields[car.name].position.x >= self.width or self.fields[car.name].position.y >= self.height:
          self.fields[car.name].position = old_position
          # car = self.fields[car.name]
          raise ValueError('Car position out of field bounds' + str(old_position))
       
    if command == 'F':
      # Collided list with other cars
      self.check_collision(self.fields[car.name])
    car = self.fields[car.name]

    return car

  def check_collision(self, car: Car):
    car_collided_list = []
    for other_car in self.fields.values():
      if car != other_car and car.position == other_car.position:
        car_collided_list.append(other_car)
    if car_collided_list:
       self.car_collided_list[car.name] = car_collided_list 
    # update collided car to other cars
    for other_car in car_collided_list:
      if self.car_collided_list.get(other_car.name) is None:
        self.car_collided_list[other_car.name] = [car] 
      elif car not in self.car_collided_list[other_car.name]: 
        self.car_collided_list[other_car.name] = self.car_collided_list[other_car.name].append(car) 
    return car_collided_list  

  def field_print(self):
    for car in self.fields.values():
      print(car)

    for car, collided_list in self.car_collided_list.items():
      if collided_list:
        for other_car in collided_list:
          print(f"Car {car} collided with {other_car}")

  def get_collision_list_of_car(self, car: Car):
     return self.car_collided_list.get(car.name, [])
  
  def __str__(self):
    cars = ""
    for car in self.fields.values():
      cars += str(car) + "\n"
    return f"Field(width={self.width}, height={self.height}), cars = [{cars}],car_collided_list={self.car_collided_list}"

  def __repr__(self):
    return str(self)
  