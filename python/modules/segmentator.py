from random import randint
import cv2
import numpy as np
from PIL import Image

class Segmentator:

    def segmentate_page(self, path):
        # TODO: based on image
        # image_width, image_height = get_image_size(image_path)
        # image_width = 400
        # image_height = 600
        
        # width = randint(300, 400)
        # height = randint(550, 600)

        # # Ensure the polygon remains within bounds
        # x = randint(0, image_width - width)
        # y = randint(0, image_height - height)
        
        # return((x, y,x + width, y + height, type)) type can be "page", "word", "alphabet", "double", "null", "default"
        return [98, 33, 418, 504, "page"]

    def segmentate_sections(self, path):
        # TODO: based on image
        # image_width, image_height = get_image_size(image_path)
        # image_width = 400
        # image_height = 600
        # polygons = []
        # for _ in range(randint(4,6)):
        #     width = randint(100, 300)
        #     height = randint(100, 300)

        #     # Ensure the polygon remains within bounds
        #     x = randint(0, image_width - width)
        #     y = randint(0, image_height - height)

        #     polygons.append((x, y, x + width, y + height, type)) type can be "page", "word", "alphabet", "double", "null", "default"
        # return polygons
        return [[131, 143, 243, 389, 'word'], [133, 62, 402, 108, 'alphabet'], [133, 109, 218, 127, 'null'], [239, 108, 372, 146, 'double'], [452, 61, 755, 94, 'alphabet'] , [615, 105, 734, 133, 'double'], [490, 98, 615, 114, 'null'], [455, 132, 573, 237, 'word'], [595, 140, 742, 174, 'default']]
    
    def segmentate_text(self, path):
        # TODO: based on image
        # image_width, image_height = get_image_size(image_path)
        #image_width = 400
        #image_height = 600
        #polygons = []
        #for i in range(randint(20,50)):
        #    width = randint(10, 30)
        #    height = randint(10, 30)
        #    x = randint(0, image_width - width)
        #    y = randint(0, image_height - height)
        #    polygons.append(((x, y), (x + width, y + height)))
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
        with Image.open(image_path) as img:
            return img.size 