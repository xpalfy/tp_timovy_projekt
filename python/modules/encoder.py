import json
import os
class Encoder:
    @staticmethod
    def get_key_json():
        # Define the path to the JSON file
        file_path = os.path.join(os.path.dirname(__file__), "example_key.json")
        
        # Open and read the JSON file
        with open(file_path, "r", encoding="utf-8") as file:
            data = json.load(file)
        
        return data
    @staticmethod
    def get_cipher_json():
        # Define the path to the JSON file
        file_path = os.path.join(os.path.dirname(__file__), "example_cipher.json")
        
        # Open and read the JSON file
        with open(file_path, "r", encoding="utf-8") as file:
            data = json.load(file)
        
        return data
    
    def encode_keys(self, polygons, ):
        for polygon in polygons:
            coordinates = polygon['coordinates']
            polygon_type = polygon['type']
            #TODO: Add encoding logic here
        
        return self.get_key_json()
        