import json
import os
class Encoder:
    @staticmethod
    def get_json():
        # Define the path to the JSON file
        file_path = os.path.join(os.path.dirname(__file__), "example_json.json")
        
        # Open and read the JSON file
        with open(file_path, "r", encoding="utf-8") as file:
            data = json.load(file)
        
        return data
        