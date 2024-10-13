from src import Car, Position, Stimulation, Field
class CLI:
  def __init__(self):
    self.field = None
    self.stimulation = None

  def start(self):
    print("Welcome to Auto Driving Car Simulation!")
    width, height = 0, 0
    while True:
      try:
        width, height = map(int, input("Enter field dimensions (width height): ").split())
        if width > 0 and height > 0:
          break
        else:
          print("Invalid dimensions. Please try again.")
      except ValueError:
        print("Invalid input. Please enter integer numbers, try again.")

    self.field = Field(width, height)
    self.stimulation = Stimulation(self.field)
    print(f"Created field of {width} x {height}.")
    while True:
      print("[1] Add a car to field\n[2] Run simulation")
      choice = input("Choose an option: ")
      if choice == '1':
          self.add_car()
      elif choice == '2':
          self.run_simulation()
      else:
          print("Invalid choice. Please try again.")

  def add_car(self):
    name = input("Enter car name: ")
    while True:
      try: 
        x, y, direction = input(f"Enter initial position of {name} (x y direction): ").split()
        if direction not in ['N', 'S', 'E', 'W']:
          print("Invalid direction. Please try again.")
        elif not x.isdigit() and not y.isdigit():
          print("Invalid position. Please try again.")
        else:
          break
      except ValueError:
        print("Invalid input. Please try again.")

    commands = input(f"Enter commands for {name}: ")

    car = Car(name, Position(int(x), int(y)), direction)
    try:
        self.stimulation.add_car(car, commands)
        print(f"Car {name} added at ({x},{y}) facing {direction}.")
    except ValueError as e:
        print(e)

  def run_simulation(self):
    # c = Car("A", Position(1, 2), "N")
    # self.stimulation.add_car(c, "FFRFFFFRRL")
    # car2 = Car("B", Position(7, 8), "W")
    # self.stimulation.add_car(car2, "FFLFFFFFFF")
    self.stimulation.run()
    while True:
      print("[1] Start Over\n[2] Exit")
      choice = input("Choose an option: ")
      if choice == '1':
          self.start()
      elif choice == '2':
          exit(1)
      else:
          print("Invalid choice. Please try again.")

cli = CLI()
cli.start()