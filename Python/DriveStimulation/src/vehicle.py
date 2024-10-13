class Vehicle:
  def __init__(self, name):
    if name is None or name == "":
      raise ValueError('Name cannot be empty')
    self.name = str(name)
    pass