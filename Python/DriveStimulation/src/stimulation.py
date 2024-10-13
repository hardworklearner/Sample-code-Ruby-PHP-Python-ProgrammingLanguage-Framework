from src import Field
from src.command import car_move_command

class Stimulation:
  def __init__(self, field: Field):
    self.field = field
    self.car_collided_list = {}
    self.command_list = {}
    pass

  def add_car(self, car, command):
    if car.name in self.field.fields:
      raise ValueError(f"Car with name '{car.name}' already exists")
    if command is None or len(command) == 0:
      raise ValueError("Invalid command")
    
    self.field.add_car(car)
    self.command_list[car.name] = command
    pass

  def run(self):
    step = 0
    while True:
      step += 1
      command_executed = False
      # Debug print step
      # print(f"Step {step}")

      for car_name, car in self.field.fields.items():
        if car_name in self.car_collided_list:
          # Debug print car collision
          # print(f"Car {car.name} is stopped due to collision.")
          continue

        commands = self.command_list[car.name]
        if step > len(commands):
          # run out of commands, stop the car
          continue

        command = commands[step - 1]
        if command not in car_move_command:
          print(f"Invalid command '{command}' for car {car.name}.")
          command_executed = True # Enable car run allow invalid command
          continue 
        try:
          car = self.field.car_move_by_command(car, command)
          collided_list = self.field.get_collision_list_of_car(car)
          if collided_list:
            self.car_collided_list[car.name] = [step, collided_list]
            # update collided car to other cars
            for collied_car in collided_list:
              if self.car_collided_list.get(collied_car.name) is None:
                self.car_collided_list[collied_car.name] = [step, [car]]
        except ValueError as e:
          print(e)

        command_executed = True

      if not command_executed:
        break

    for car_name, car in self.field.fields.items():
      if car_name in self.car_collided_list:
        collided_list = self.car_collided_list[car_name]
        for collided_car in collided_list[1]:
          print(f"Car {car.name} collided with {collided_car.name} at {collided_car.position} at step {collided_list[0]}")
      else:
        print(f"{car}")
