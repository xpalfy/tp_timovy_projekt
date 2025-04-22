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
        
        # return((x, y,x + width, y + height))
        return [98, 33, 770, 504]

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

        #     polygons.append((x, y, x + width, y + height))
        # return polygons
        return [[131, 143, 243, 389], [135, 60, 404, 146], [452, 61, 755, 94], [455, 131, 570, 236], [615, 105, 734, 133], [596, 140, 739, 173]]
    
    def segmentate_text(self, path):
        # TODO: based on image
        # image_width, image_height = get_image_size(image_path)
        image_width = 400
        image_height = 600
        polygons = []
        for i in range(randint(20,50)):
            width = randint(10, 30)
            height = randint(10, 30)
            x = randint(0, image_width - width)
            y = randint(0, image_height - height)
            polygons.append(((x, y), (x + width, y + height)))
            
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