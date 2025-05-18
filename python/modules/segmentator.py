from random import randint
import cv2
import numpy as np
from PIL import Image
#from processor import CipherKeyProcessor

class Segmentator:
    
    example_cipher_page = "0 0.99 0.50137941176470588236 0.50102272727272727 0.737058823529411764 0.87704545454545454"
    example_key_pages = ["0 0.99 0.3022058823529412 0.4931818181818182 0.387058823529411764 0.840704545454545454",
                        "0 0.98 0.7122058823529412 0.4931818181818182 0.367058823529411764 0.840704545454545454"]
    
    
    def __init__(self):
        #self.preprocess = CipherKeyProcessor
        self.preprocess = None  # Placeholder for the preprocessor

    def segmentate_page(self, path):
        img_size = get_image_size(path)
        # image = cv2.imread(path)
        # raw_yolo_output = self.preprocess().segment_page(image)
        
        image_width, image_height = img_size if img_size else (400, 600)

        raw_yolo_output = self.example_cipher_page if 'cipher' in path.lower() else self.example_key_pages

        class_names = ["page"]
        
        yolo_result = self.yolo_to_dict_list(raw_yolo_output, image_width, image_height, class_names)
        print(yolo_result)
        return yolo_result

    def segmentate_sections(self, path):
        # TODO: get raw yolo output like in segmentate_page convert it to dict list and return it

        #return [[131, 143, 243, 389, 'word'], [133, 62, 402, 108, 'alphabet'], [133, 109, 218, 127, 'null'], [239, 108, 372, 146, 'double'], [452, 61, 755, 94, 'alphabet'] , [615, 105, 734, 133, 'double'], [490, 98, 615, 114, 'null'], [455, 132, 573, 237, 'word'], [595, 140, 742, 174, 'default']]
        example_output_for_key = [{'polygon': [131, 143, 243, 389], 'type': 'word'},
                                  {'polygon': [133, 62, 402, 108], 'type': 'alphabet'},
                                  {'polygon': [133, 109, 218, 127], 'type': 'null'},
                                  {'polygon': [239, 108, 372, 146], 'type': 'double'},
                                  {'polygon': [452, 61, 755, 94], 'type': 'alphabet'},
                                  {'polygon': [615, 105, 734, 133], 'type': 'double'},
                                  {'polygon': [490, 98, 615, 114], 'type': 'null'},
                                  {'polygon': [455, 132, 573, 237], 'type': 'word'},
                                  {'polygon': [595, 140, 742, 174], 'type': 'default'}]
        
        example_output_for_cipher = [{'polygon': [160, 170, 690, 220], 'type': 'word'},
                                  {'polygon': [157, 225, 680, 300], 'type': 'default'}]
        if 'cipher' not in path:
            return example_output_for_key
        else:
            return example_output_for_cipher

    def segmentate_text(self, path):
        # TODO: based on yolo output like in segmentate_page and segmentate_sections
        letters = self.get_example_key_letters() if 'key' in path else self.get_example_cipher_letters()
        

        dict_list = [{"polygon": item[:4], "type": item[4]} for item in letters if letters]
        return dict_list
    
    def get_example_cipher_letters(self):
        letters = []
        for i in range(9):
            letter = [166+(i*40), 173, 203+(i*40), 225, 'word']
            letters.append(letter)
        for i in range(3):
            letter = [547+(i*48), 175, 594+(i*40), 227, 'null']
            letters.append(letter)

        for i in range(2):
            letter = [167+(i*60), 227, 220+(i*60), 268, 'double']
            letters.append(letter)
        for i in range(10):
            letter = [280+(i*40), 227, 320+(i*40), 268, 'default']
            letters.append(letter)
        
        for i in range(3):
            letter = [166+(i*40), 263, 203+(i*40), 304, 'word']
            letters.append(letter)
        for i in range(9):
            letter = [302+(i*43), 263, 349+(i*43), 304, 'double']
            letters.append(letter)
        
        return letters
    
    def get_example_key_letters(self):
        letters = []
        for i in range(25):
            letter = [131, 149 + i * 9, 237, 149 + (i + 1) * 9, 'word']
            letters.append(letter)

        second_box = [135, 60, 404, 146]
        for i in range (22):
            letter = [135 + i * 12 , 65, 135 + (i + 1) * 12, 110, 'alphabet']
            letters.append(letter)

        letters.append([135, 110, 220, 124, 'null'])  

        for i in range(9):
            letter = [255 + i * 12, 120, 255 + (i + 1) * 12, 145, 'double']
            letters.append(letter)       

        third_box = [452, 61, 755, 94]
        for i in range(26):
            letter = [455 + i * 12, 60, 455 + (i + 1) * 12, 95, 'alphabet']
            letters.append(letter)

        fourth_box = [455, 131, 570, 236]
        for i in range(10):
            letter = [455, 131 + i * 10, 570, 131 + (i + 1) * 10 , 'word']
            letters.append(letter)

        fifth_box = [615, 105, 734, 133]
        for i in range(9):
            letter = [620 + i * 12, 105, 620 + (i + 1) * 12, 133 , 'double']
            letters.append(letter)

        sixth_box = [596, 140, 739, 173]  
        letters.append([595, 140, 620, 175, 'default'])
        letters.append([625, 140, 645, 175 , 'default'])
        letters.append([650, 140, 680, 175 , 'default'])
        letters.append([685, 140, 705, 175 , 'default'])
        letters.append([710, 140, 740, 175 , 'default'])

        return letters
    #def generate_codes(self, path):
    def yolo_to_dict_list(self, raw_yolo_output, image_width, image_height, class_names):
        """
            Converts raw YOLO output (Darknet format) to a list of dictionaries.
            
            Args:
                raw_yolo_output (str): Raw YOLO output as a string (per line: class_id conf x_center y_center w h).
                image_width (int): Width of the image.
                image_height (int): Height of the image.
                class_names (list): List of class names (e.g., ["person", "car", "chair"]).
            
            Returns:
                list: List of dictionaries in the format {"polygon": [x1,y1,x2,y2], "type": class_name}.
            """
        detections = []
        lines = raw_yolo_output.strip().split('\n') if isinstance(raw_yolo_output, str) else raw_yolo_output
        
        for line in lines:
            parts = line.split()
            if len(parts) != 6:
                continue  # Skip malformed lines
            
            class_id = int(parts[0])
            confidence = float(parts[1])
            x_center = float(parts[2]) * image_width
            y_center = float(parts[3]) * image_height
            width = float(parts[4]) * image_width
            height = float(parts[5]) * image_height
            
            # Convert center coordinates to [x1, y1, x2, y2] (polygon format)
            x1 = int(x_center - width / 2)
            y1 = int(y_center - height / 2)
            x2 = int(x_center + width / 2)
            y2 = int(y_center + height / 2)
            
            # Get class name (handle out-of-range class_id)
            class_name = class_names[class_id] if class_id < len(class_names) else f"class_{class_id}"
            
            detections.append({
                "polygon": [x1, y1, x2, y2],
                "type": class_name
            })
        
        return detections
     
       
    def crop_polygon(image_path, polygon):
        """
        Crops a polygon from an image. If the polygon is a trapezoid, corrects the perspective.
        
        :param image_path: Path to the original image.
        :param polygon: List of (x, y) tuples representing the polygon vertices (4 points).
        :return: Cropped and corrected image (PIL Image).
        """
        # Load image
        image = cv2.imread(image_path)
        
        if image is None:
            raise ValueError("Image not found or cannot be opened.")

        # Convert polygon to numpy array
        polygon = np.array(polygon, dtype=np.float32)

        # Ensure we have 4 points
        if len(polygon) != 4:
            raise ValueError("Polygon must have exactly 4 points.")

        # Compute the bounding box size
        width = int(max(np.linalg.norm(polygon[0] - polygon[1]), np.linalg.norm(polygon[2] - polygon[3])))
        height = int(max(np.linalg.norm(polygon[0] - polygon[3]), np.linalg.norm(polygon[1] - polygon[2])))

        # Define the destination rectangle (warped image)
        dst_rect = np.array([
            [0, 0],
            [width, 0],
            [width, height],
            [0, height]
        ], dtype=np.float32)

        # Compute the perspective transform
        matrix = cv2.getPerspectiveTransform(polygon, dst_rect)

        # Apply the warp transformation
        warped = cv2.warpPerspective(image, matrix, (width, height))

        # Convert to PIL image for easy display
        return Image.fromarray(cv2.cvtColor(warped, cv2.COLOR_BGR2RGB))

def get_image_size(image_path):
    try:
        with Image.open(image_path) as img:
            return img.size 
    except Exception as e:
        print(f"Error opening image: {e}")
        return None